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
        // Corrected relationship name from 'talent' to 'assignedTalent'
        $requests = $user->createdRequests()->with('assignedTalent')->latest()->paginate(10);

        return view('user.requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new talent request.
     */
    public function create()
    {
        // Fetch competencies for the form
        $competencies = Competency::orderBy('name')->get();

        return view('user.requests.create', compact('competencies'));
    }

    /**
     * Store a newly created talent request in storage.
     */
    public function store(Request $request)
    {
        // Define proficiency levels and weights for validation
        $proficiencyLevels = [1, 2, 3, 4]; // Completion, Intermediate, Advanced, Expert
        $weights = [1, 2, 3, 4, 5]; // Example: 1 (Least Important) to 5 (Most Important)

        // Validate the request
        // Assumes 'competencies' is an array of objects, e.g., [{id: 1, level: 3, weight: 5}, ...]
        $validated = $request->validate([
            'competencies' => 'required|array|min:1', // Must select at least one competency
            'competencies.*.id' => 'required|integer|exists:competencies,id',
            'competencies.*.level' => ['required', 'integer', 'in:' . implode(',', $proficiencyLevels)],
            'competencies.*.weight' => ['required', 'integer', 'in:' . implode(',', $weights)],
            'details' => 'required|string|max:1000',
        ], [
            'competencies.required' => 'Please select and configure at least one competency.',
            'competencies.min' => 'Please select and configure at least one competency.',
            'competencies.*.id.required' => 'A competency ID is missing for one of your selections.',
            'competencies.*.id.exists' => 'An invalid competency was selected.',
            'competencies.*.level.required' => 'Please select a proficiency level for each chosen competency.',
            'competencies.*.level.in' => 'Invalid proficiency level selected for a competency.',
            'competencies.*.weight.required' => 'Please set a weight for each chosen competency.',
            'competencies.*.weight.in' => 'Invalid weight selected for a competency.',
        ]);

        // Create the talent request
        $talentRequest = TalentRequest::create([
            'user_id' => Auth::id(),
            'details' => $validated['details'],
            'status' => 'pending_admin',
        ]);

        // Prepare data for attaching competencies with proficiency levels and weights
        $competenciesToAttach = [];
        foreach ($validated['competencies'] as $compData) {
            $competenciesToAttach[$compData['id']] = [
                'required_proficiency_level' => $compData['level'],
                'weight' => $compData['weight'] // Add weight here
            ];
        }

        // Attach the required competencies with their proficiency levels and weights
        // The 'min:1' validation for 'competencies' array ensures $competenciesToAttach will not be empty if validation passes.
        $talentRequest->competencies()->attach($competenciesToAttach);

        return redirect()->route('user.requests.index')->with('success', 'Talent request submitted successfully. It will be reviewed by an administrator.');
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
