<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TalentRequest;
use App\Models\User; // Import User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Although admin, might be useful for logging actions
use App\Services\DecisionSupportService; // Import the DSS service
use Illuminate\Support\Facades\DB; // Import DB facade
use Illuminate\Support\Facades\Log; // Import Log facade

class TalentRequestController extends Controller
{
    /**
     * Display a listing of all talent requests.
     */
    public function index(Request $request)
    {
        // Add filtering/sorting logic if needed (e.g., by status)
        // Corrected to use the new relationship 'assignedTalents'
        $query = TalentRequest::with(['requestingUser', 'assignedTalents', 'competencies'])->latest(); // Eager load competencies

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(15);
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
    public function show(TalentRequest $talentRequest, DecisionSupportService $dss)
    {
        // Load required relationships
        // Corrected to use the new relationship 'assignedTalents'
        $talentRequest->load(['requestingUser', 'assignedTalents', 'competencies']); // Corrected relationship names

        // Get ranked talent suggestions using the DSS
        $rankedTalents = $dss->findAndRankTalents($talentRequest);

        // Pass both the request and the ranked talents to the view
        return view('admin.talent-requests.show', compact('talentRequest', 'rankedTalents'));
    }

    /**
     * Update the specified talent request status (Admin rejection).
     * This method specifically handles the admin rejecting a request *before* assignment.
     */
    public function update(Request $request, TalentRequest $talentRequest)
    {
        $validated = $request->validate([
            'action' => 'required|in:reject', // Only allow 'reject' action here
            // Add validation for admin comments if needed
        ]);

        // Ensure the request is pending admin approval
        if ($talentRequest->status !== 'pending_admin') {
            return back()->with('error', 'This request is not awaiting admin approval/rejection.');
        }

        // Action must be 'reject'
        $newStatus = 'rejected_admin';
        // Optional: Notify the requesting user

        $talentRequest->update(['status' => $newStatus]);

        // Optional: Log admin action

        return redirect()->route('admin.talent-requests.index')->with('success', 'Request rejected successfully.');
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
                    // Prepare for sync with the required pivot status
                    $assignmentsToSync[$talentId] = ['status' => 'pending_assignment_response'];
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
     */
    public function markAsCompleted(TalentRequest $talentRequest)
    {
        // Ensure the request is currently approved before marking as completed
        if ($talentRequest->status !== 'approved') {
            return back()->with('error', 'Only approved requests can be marked as completed.');
        }

        $talentRequest->update(['status' => 'completed']);

        // Optional: Log admin action
        // Optional: Notify user/talent

        return redirect()->route('admin.talent-requests.index')->with('success', 'Request marked as completed successfully.');
    }
}
