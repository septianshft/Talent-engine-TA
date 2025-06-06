<?php

namespace App\Http\Controllers\Talent;

use App\Http\Controllers\Controller;
use App\Models\TalentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Added for potential direct pivot updates

class TalentRequestController extends Controller
{
    /**
     * Display a listing of the talent requests assigned to the talent.
     */
    public function index()
    {
        /** @var \\App\\Models\\User $talent */
        $talent = Auth::user();

        // Get requests assigned to this talent where their specific assignment status
        // is either 'pending_assignment_response' (from admin) or 'direct_offer_pending' (direct from requester).
        $requests = $talent->assignedRequests()
                           ->wherePivotIn('status', ['pending_assignment_response', 'direct_offer_pending'])
                           ->with('requestingUser') // Eager load the requesting user
                           ->latest('talent_request_assignments.created_at') // Order by when the assignment was created
                           ->paginate(10);

        return view('talent.requests.index', compact('requests'));
    }

    /**
     * Display the specified talent request.
     */
    public function show(TalentRequest $talentRequest)
    {
        /** @var \\App\\Models\\User $talent */
        $talent = Auth::user();

        // Check if the talent is assigned to this request and their assignment status is relevant
        $assignment = $talent->assignedRequests()
                              ->where('talent_request_id', $talentRequest->id)
                              ->wherePivotIn('status', ['pending_assignment_response', 'direct_offer_pending', 'approved_by_talent', 'rejected_by_talent']) // Talent can see if pending or already actioned
                              ->first();

        if (!$assignment) {
            abort(403, 'You are not authorized to view this request or your assignment status is not applicable.');
        }

        // Pass the assignment details (including pivot status and type) to the view
        $talentRequest->load('requestingUser', 'competencies'); // Load requesting user and competencies
        $currentAssignmentStatus = $assignment->pivot->status;
        $assignmentType = $assignment->pivot->assignment_type; // Pass assignment_type to the view

        return view('talent.requests.show', compact('talentRequest', 'currentAssignmentStatus', 'assignmentType'));
    }

    /**
     * Update the talent's response to the assignment.
     */
    public function update(Request $request, TalentRequest $talentRequest)
    {
        /** @var \App\Models\User $talent */
        $talent = Auth::user();

        // Ensure the talent is assigned to this request and it's awaiting their response
        $assignment = $talent->assignedRequests()
                              ->where('talent_request_id', $talentRequest->id)
                              ->wherePivot('status', 'pending_assignment_response')
                              ->first();

        if (!$assignment) {
            return back()->with('error', 'This request is not awaiting your response or you are not assigned to it.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            // Add validation for comments if talents can add comments
        ]);

        $newPivotStatus = $validated['action'] === 'approve' ? 'approved_by_talent' : 'rejected_by_talent';

        // Update the pivot table status for this specific talent's assignment
        $talent->assignedRequests()->updateExistingPivot($talentRequest->id, ['status' => $newPivotStatus]);

        if ($newPivotStatus === 'approved_by_talent') {
            // Update the main TalentRequest status to 'approved'
            // This assumes that one talent approval is enough to consider the request approved for completion.
            if ($talentRequest->status === 'pending_talent') { // Only update if it's currently pending talent
                $talentRequest->status = 'approved';
                $talentRequest->save();
                // Optionally, add a log or notification here
            }
        }

        // Optional: Add notification for the requesting user and/or admin here
        // Optional: Logic to update the main TalentRequest status if all assigned talents have responded.
        // For example, if all approved, set TalentRequest to 'awaiting_requester_confirmation'
        // If all rejected, set TalentRequest to 'closed_no_talent'
        // This logic might be complex and better handled in a service or observer.

        return redirect()->route('talent.requests.index')->with('success', 'Your response has been recorded successfully.');
    }

    /**
     * Handle the talent\'s response (approve/reject) to a talent request assignment.
     * Specifically for direct offers or admin assignments awaiting talent action.
     */
    public function respond(Request $request, TalentRequest $talentRequest)
    {
        /** @var \\App\\Models\\User $talent */
        $talent = Auth::user();

        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            // 'comments' => 'nullable|string|max:1000', // Future enhancement
        ]);

        // Find the specific assignment for this talent and request
        $assignment = DB::table('talent_request_assignments')
                        ->where('talent_request_id', $talentRequest->id)
                        ->where('user_id', $talent->id)
                        ->first();

        if (!$assignment) {
            return back()->with('error', 'You are not assigned to this request.');
        }

        if ($assignment->status !== 'direct_offer_pending' && $assignment->status !== 'pending_assignment_response') {
            return back()->with('error', 'This request is not currently awaiting your response or has already been actioned.');
        }

        $newPivotStatus = $validated['action'] === 'approve' ? 'approved_by_talent' : 'rejected_by_talent';
        $updateData = ['status' => $newPivotStatus];
        // if (isset($validated[\'comments\'])) { // Future enhancement
        //     $updateData[\'talent_comments\'] = $validated[\'comments\'];
        // }

        $talent->assignedRequests()->updateExistingPivot($talentRequest->id, $updateData);

        if ($newPivotStatus === 'approved_by_talent' && $assignment->assignment_type === 'direct_offer') {
            if ($talentRequest->status === 'pending_talent') {
                $talentRequest->status = 'approved';
                $talentRequest->save();
            }
        } elseif ($newPivotStatus === 'rejected_by_talent' && $assignment->assignment_type === 'direct_offer') {
            // If a direct offer is rejected by the talent, set the request status to pending_admin
            // so the admin can take further action.
            if ($talentRequest->status === 'pending_talent') { // Ensure it was in the expected state
                $talentRequest->status = 'pending_admin';
                $talentRequest->save();
            }
        }

        return redirect()->route('talent.requests.index')->with('success', 'Your response has been recorded successfully.');
    }
}
