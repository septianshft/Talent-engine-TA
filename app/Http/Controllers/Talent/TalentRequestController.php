<?php

namespace App\Http\Controllers\Talent;

use App\Http\Controllers\Controller;
use App\Models\TalentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TalentRequestController extends Controller
{
    /**
     * Display a listing of the talent requests received by the talent.
     */
    public function index()
    {
        /** @var \App\Models\User $talent */
        $talent = Auth::user();
        $requests = $talent->receivedRequests()
                           ->where('status', 'pending_talent') // Only show requests ready for talent review
                           ->with('requestingUser') // Eager load the requesting user
                           ->latest()
                           ->paginate(10);

        return view('talent.requests.index', compact('requests'));
    }

    /**
     * Display the specified talent request.
     */
    public function show(TalentRequest $talentRequest)
    {
        /** @var \App\Models\User $talent */
        $talent = Auth::user();

        // Ensure the logged-in talent is the recipient of this request
        if ($talentRequest->talent_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure the request is actually meant for the talent to see
        if (!in_array($talentRequest->status, ['pending_talent', 'approved', 'rejected_talent'])) { // Add other relevant statuses if needed
             abort(404); // Or redirect with an error
        }

        $talentRequest->load('requestingUser'); // Load the requesting user details

        return view('talent.requests.show', compact('talentRequest'));
    }

    /**
     * Update the specified talent request in storage (talent's response).
     */
    public function update(Request $request, TalentRequest $talentRequest)
    {
        /** @var \App\Models\User $talent */
        $talent = Auth::user();

        // Ensure the logged-in talent is the recipient of this request
        if ($talentRequest->talent_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure the request is currently pending talent action
        if ($talentRequest->status !== 'pending_talent') {
            return back()->with('error', 'This request is not awaiting your response.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject', // Expecting 'approve' or 'reject'
            // Add validation for comments if talents can add comments
        ]);

        $newStatus = $validated['action'] === 'approve' ? 'approved' : 'rejected_talent';

        $talentRequest->update(['status' => $newStatus]);

        // Optional: Add notification for the requesting user and/or admin here

        return redirect()->route('talent.requests.index')->with('success', 'Request ' . $validated['action'] . 'd successfully.');
    }
    //
}