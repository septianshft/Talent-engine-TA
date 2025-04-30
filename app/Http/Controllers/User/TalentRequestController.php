<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TalentRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Competency;
use Illuminate\Http\Request;

class TalentRequestController extends Controller
{
    /**
     * Display a listing of the talent requests created by the user.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $requests = $user->createdRequests()->with('talent')->latest()->paginate(10);

        return view('user.requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new talent request.
     */
    public function create(Request $request)
    {
        $competencies = Competency::orderBy('name')->get();
        $selectedCompetencyId = $request->input('competency_id');

        // Get talents, optionally filtered by selected competency
        $talentsQuery = User::where('role', 'talent');
        if ($selectedCompetencyId) {
            $talentsQuery->whereHas('competencies', function ($query) use ($selectedCompetencyId) {
                $query->where('competencies.id', $selectedCompetencyId);
            });
        }
        $talents = $talentsQuery->orderBy('name')->get(['id', 'name']);

        return view('user.requests.create', compact('competencies', 'talents', 'selectedCompetencyId'));
    }

    /**
     * Store a newly created talent request in storage.
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'talent_id' => 'required|exists:users,id,role,talent', // Ensure talent exists and has the 'talent' role
            'details' => 'required|string|max:1000',
        ]);

        $user->createdRequests()->create([
            'talent_id' => $validated['talent_id'],
            'details' => $validated['details'],
            'status' => 'pending_admin', // Initial status
        ]);

        // Optional: Add notification for admin here

        return redirect()->route('user.requests.index')->with('success', 'Talent request submitted successfully.');
    }

    /**
     * Remove the specified talent request from storage.
     */
    public function destroy(TalentRequest $talentRequest)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ensure the user owns this request
        if ($talentRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Optional: Add check to only allow deletion if status is 'pending_admin'
        // if ($talentRequest->status !== 'pending_admin') {
        //     return back()->with('error', 'Cannot delete a request that is already being processed.');
        // }

        $talentRequest->delete();

        return redirect()->route('user.requests.index')->with('success', 'Talent request deleted successfully.');
    }
}
