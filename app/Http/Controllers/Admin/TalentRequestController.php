<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TalentRequest;
use App\Models\User; // Import User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Although admin, might be useful for logging actions
use App\Services\EnhancedDecisionSupportService; // Import the Enhanced DSS service
use Illuminate\Support\Facades\DB; // Import DB facade
use Illuminate\Support\Facades\Log; // Import Log facade
use Illuminate\Validation\ValidationException; // Import ValidationException

class TalentRequestController extends Controller
{
    /**
     * Display a listing of all talent requests.
     */
    public function index(Request $request)
    {
        // Corrected to use the new relationship 'assignedTalents' and eager load pivot data
        $query = TalentRequest::with(['requestingUser', 'assignedTalents', 'competencies'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Add location-based filtering
        if ($request->filled('work_location_type')) {
            $query->where('work_location_type', $request->work_location_type);
        }

        if ($request->filled('work_location_country')) {
            $query->where('work_location_country', 'LIKE', '%' . $request->work_location_country . '%');
        }

        $requests = $query->paginate(15);

        // Add a flag to each request if it is pending_admin due to a direct offer rejection
        $requests->getCollection()->transform(function ($talentRequest) {
            $talentRequest->isPendingAdminAfterDirectOfferRejection = false;
            if ($talentRequest->status === 'pending_admin') {
                $talentRequest->isPendingAdminAfterDirectOfferRejection = $talentRequest->assignedTalents
                                                                    ->where('pivot.assignment_type', 'direct_offer')
                                                                    ->where('pivot.status', 'rejected_by_talent')
                                                                    ->isNotEmpty();
            }
            return $talentRequest;
        });

        // Define possible statuses as an associative array for filtering and display
        $statuses = [
            'pending_admin' => 'Pending Admin',
            'pending_talent' => 'Pending Talent',
            'approved' => 'Approved',
            'rejected_admin' => 'Rejected (Admin)',
            'rejected_talent' => 'Rejected (Talent)',
            'completed' => 'Completed'
        ];

        return view('admin.talent-requests.index', compact('requests', 'statuses'));
    }

    /**
     * Display the specified talent request.
     */
    public function show(TalentRequest $talentRequest, EnhancedDecisionSupportService $enhancedDss)
    {
        // Load required relationships
        $talentRequest->load(['requestingUser', 'assignedTalents', 'competencies']); // Ensure pivot data is loaded

        // Check if there is an active direct offer pending for this request
        $hasDirectOfferPending = $talentRequest->assignedTalents()
                                            ->wherePivot('status', 'direct_offer_pending')
                                            ->exists();

        // Check if the request is pending admin review due to a rejected direct offer
        $isPendingAdminAfterDirectOfferRejection = false;
        if ($talentRequest->status === 'pending_admin') {
            $isPendingAdminAfterDirectOfferRejection = $talentRequest->assignedTalents()
                                                                ->wherePivot('assignment_type', 'direct_offer')
                                                                ->wherePivot('status', 'rejected_by_talent')
                                                                ->exists();
        }

        $rankedTalents = collect(); // Initialize as empty collection
        $methodologyExplanation = null;
        $dssErrorMessage = null; // Initialize error message

        try {
            // Get ranked talent suggestions using the Enhanced DSS
            $rankedTalents = $enhancedDss->findAndRankTalents($talentRequest);

            // Get methodology explanation for transparency
            $methodologyExplanation = $enhancedDss->getMethodologyExplanation();
        } catch (ValidationException $e) {
            // Handle validation errors (e.g., invalid weight distribution)
            Log::warning('[Admin Controller] DSS validation error: ' . $e->getMessage());
            // Set dssErrorMessage instead of redirecting back
            $dssErrorMessage = 'Invalid competency configuration: ' . $e->getMessage();
            // $rankedTalents and $methodologyExplanation remain as their initialized empty/null values
        } catch (\Exception $e) { // Corrected: removed extra backslash before Exception
            Log::error('[Admin Controller] DSS error: ' . $e->getMessage());
            // $rankedTalents and $methodologyExplanation remain as their initialized empty/null values
            $dssErrorMessage = 'An unexpected error occurred with the Decision Support System.'; // Generic error for other exceptions
        }

        // Pass both the request and the ranked talents to the view, including the direct offer status and rejection info
        return view('admin.talent-requests.show', compact(
            'talentRequest',
            'rankedTalents',
            'methodologyExplanation',
            'dssErrorMessage',
            'hasDirectOfferPending',
            'isPendingAdminAfterDirectOfferRejection' // Add this new variable
        ));
    }

    /**
     * Update the specified talent request status (Admin rejection).
     * This method specifically handles the admin rejecting a request *before* assignment.
     * Phase 1 Fix: Enhanced error handling and transaction safety
     */
    public function update(Request $request, TalentRequest $talentRequest)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|in:reject', // Only allow 'reject' action here
                // Add validation for admin comments if needed
            ]);

            // Ensure the request is pending admin approval
            if ($talentRequest->status !== 'pending_admin') {
                return back()->with('error', 'This request is not awaiting admin approval/rejection.');
            }

            // Use transaction for data consistency
            return DB::transaction(function () use ($talentRequest) {
                // Action must be 'reject'
                $newStatus = 'rejected_admin';
                $talentRequest->update(['status' => $newStatus]);

                // Log admin action for audit trail
                Log::info('[Admin] Request rejected', [
                    'request_id' => $talentRequest->id,
                    'admin_id' => Auth::id(),
                    'previous_status' => 'pending_admin',
                    'new_status' => $newStatus
                ]);

                return redirect()->route('admin.talent-requests.index')->with('success', 'Request rejected successfully.');
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('[Admin] Error rejecting request: ' . $e->getMessage(), [
                'request_id' => $talentRequest->id,
                'admin_id' => Auth::id()
            ]);

            return back()->with('error', 'An error occurred while rejecting the request. Please try again.');
        }
    }

    /**
     * Assign one or more talents to the talent request.
     */
    public function assign(Request $request, TalentRequest $talentRequest)
    {
        $validated = $request->validate([
            'talent_ids' => 'required|array|min:1', // Ensures at least one talent is selected
            'talent_ids.*' => 'required|exists:users,id', // Validate each talent ID
        ]);

        // Allow assignment if status is pending_admin or pending_talent
        if (!in_array($talentRequest->status, ['pending_admin', 'pending_talent'])) {
            return back()->with('error', 'This request cannot be assigned talents at this stage. Current status: ' . $talentRequest->status);
        }

        $talentIdsToAssign = $validated['talent_ids'];
        $assignmentsToSync = [];

        try {
            DB::transaction(function () use ($talentRequest, $talentIdsToAssign, &$assignmentsToSync) {
                foreach ($talentIdsToAssign as $talentId) {
                    $talentUser = User::find($talentId);
                    if (!$talentUser || !$talentUser->hasRole('talent')) {
                        Log::warning("[DSS] Attempted to assign non-talent user ID {$talentId} to request ID {$talentRequest->id}. Skipping this user.");
                        continue; // Skip this assignment if user is not a talent
                    }
                    // Prepare for sync with the required pivot status and assignment_type
                    $assignmentsToSync[$talentId] = [
                        'status' => 'pending_assignment_response',
                        'assignment_type' => 'dss_assigned',
                        'assigned_by' => Auth::id()
                    ];
                }

                if (empty($assignmentsToSync)) {
                    // This block executes if talent_ids were provided, but none were valid talents.
                    // Detach all previously assigned talents.
                    $talentRequest->assignedTalents()->sync([]);
                    if ($talentRequest->status === 'pending_talent') {
                        $talentRequest->status = 'pending_admin'; // Revert to pending_admin
                        $talentRequest->save();
                    }
                    // Throw an exception to be caught by the outer catch block, to show a message.
                    // Using a custom exception or just returning a redirect with error might be cleaner.
                    // For now, let the transaction rollback and rely on the generic error message,
                    // or add a specific session flash message before returning.
                    // For simplicity here, we'll let it proceed to the generic error or success message logic.
                    // A better approach would be to set a specific error message.
                    // Let's adjust to provide a specific warning if no valid talents assigned.
                } else {
                    $talentRequest->assignedTalents()->sync($assignmentsToSync);
                }


                // Update the main request status
                if ($talentRequest->assignedTalents()->count() > 0) {
                    if ($talentRequest->status === 'pending_admin') {
                        $talentRequest->status = 'pending_talent';
                        $talentRequest->save();
                    }
                    // If already 'pending_talent', it remains 'pending_talent'.
                } else {
                    // This case (no talents assigned) should now also cover when $assignmentsToSync was empty.
                    if ($talentRequest->status === 'pending_talent') {
                        $talentRequest->status = 'pending_admin';
                        $talentRequest->save();
                    }
                }
            });

            if (empty($assignmentsToSync) && !empty($talentIdsToAssign)) {
                // This means talent_ids were submitted, but none were valid talents.
                return back()->with('warning', 'No valid talents were assigned. Please ensure selected users have the talent role. Any previous assignments have been cleared.');
            }

            return redirect()->route('admin.talent-requests.index')->with('success', 'Talents assigned/updated successfully. Requests sent to talents for review.');
        } catch (\Exception $e) {
            Log::error("[DSS] Error assigning talents to request ID {$talentRequest->id}: " . $e->getMessage());
            return back()->with('error', 'An error occurred while assigning talents. Please try again.');
        }
    }

    /**
     * Mark the specified talent request as completed.
     * Phase 1 Fix: Enhanced error handling and transaction safety
     */
    public function markAsCompleted(TalentRequest $talentRequest)
    {
        try {
            // Ensure the request is currently approved before marking as completed
            if ($talentRequest->status !== 'approved') {
                return back()->with('error', 'Only approved requests can be marked as completed.');
            }

            return DB::transaction(function () use ($talentRequest) {
                $previousStatus = $talentRequest->status;
                $talentRequest->update(['status' => 'completed']);

                // Log admin action for audit trail
                Log::info('[Admin] Request marked as completed', [
                    'request_id' => $talentRequest->id,
                    'admin_id' => Auth::id(),
                    'previous_status' => $previousStatus,
                    'new_status' => 'completed'
                ]);

                return redirect()->route('admin.talent-requests.index')->with('success', 'Request marked as completed successfully.');
            });
        } catch (\Exception $e) {
            Log::error('[Admin] Error marking request as completed: ' . $e->getMessage(), [
                'request_id' => $talentRequest->id,
                'admin_id' => Auth::id()
            ]);

            return back()->with('error', 'An error occurred while marking the request as completed. Please try again.');
        }
    }
}
