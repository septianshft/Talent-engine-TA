<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TalentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Although admin, might be useful for logging actions

class TalentRequestController extends Controller
{
    /**
     * Display a listing of all talent requests.
     */
    public function index(Request $request)
    {
        // Add filtering/sorting logic if needed (e.g., by status)
        $query = TalentRequest::with(['user', 'talent'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(15);
        $statuses = ['pending_admin', 'pending_talent', 'approved', 'rejected_admin', 'rejected_talent', 'completed']; // Define possible statuses for filtering

        return view('admin.talent-requests.index', compact('requests', 'statuses'));
    }

    /**
     * Display the specified talent request.
     */
    public function show(TalentRequest $talentRequest)
    {
        $talentRequest->load(['user', 'talent']); // Ensure relationships are loaded
        return view('admin.talent-requests.show', compact('talentRequest'));
    }

    /**
     * Update the specified talent request status (Admin approval/rejection).
     */
    public function update(Request $request, TalentRequest $talentRequest)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            // Add validation for admin comments if needed
        ]);

        // Ensure the request is pending admin approval
        if ($talentRequest->status !== 'pending_admin') {
            return back()->with('error', 'This request is not awaiting admin approval.');
        }

        if ($validated['action'] === 'approve') {
            $newStatus = 'pending_talent'; // Move to talent for their review
            // Optional: Notify the talent
        } else { // 'reject'
            $newStatus = 'rejected_admin';
            // Optional: Notify the requesting user
        }

        $talentRequest->update(['status' => $newStatus]);

        // Optional: Log admin action

        return redirect()->route('admin.talent-requests.index')->with('success', 'Request status updated successfully.');
    }
}