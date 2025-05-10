<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TalentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Although admin, might be useful for logging actions
use App\Services\DecisionSupportService; // Import the DSS service

class TalentRequestController extends Controller
{
    /**
     * Display a listing of all talent requests.
     */
    public function index(Request $request)
    {
        // Add filtering/sorting logic if needed (e.g., by status)
        $query = TalentRequest::with(['requestingUser', 'assignedTalent', 'competencies'])->latest(); // Eager load competencies

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
        $talentRequest->load(['requestingUser', 'assignedTalent', 'competencies']); // Corrected relationship names

        // Get ranked talent suggestions using the DSS
        $rankedTalents = $dss->findAndRankTalents($talentRequest);

        // Pass both the request and the ranked talents to the view
        return view('admin.talent-requests.show', compact('talentRequest', 'rankedTalents'));
    }

    /**


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
     * Assign a specific talent to the talent request.
     */
    public function assign(Request $request, TalentRequest $talentRequest)
    {
        $validated = $request->validate([
            'talent_id' => 'required|exists:users,id',
        ]);

        // Ensure the request is pending admin approval before assigning
        if ($talentRequest->status !== 'pending_admin') {
            return back()->with('error', 'This request cannot be assigned a talent at this stage.');
        }

        // Ensure the selected user has the 'talent' role (optional but recommended)
        // $talentUser = \App\Models\User::find($validated['talent_id']);
        // if (!$talentUser || !$talentUser->hasRole('talent')) {
        //     return back()->with('error', 'Invalid talent selected.');
        // }

        // Assign the talent and update status
        $talentRequest->talent_id = $validated['talent_id'];
        $talentRequest->status = 'pending_talent';
        $talentRequest->save();

        // Optional: Notify the assigned talent
        // Optional: Log admin action

        return redirect()->route('admin.talent-requests.index')->with('success', 'Talent assigned successfully. Request sent to talent for review.');
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