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
        // Define proficiency levels for validation
        $proficiencyLevels = [1, 2, 3, 4]; // Completion, Intermediate, Advanced, Expert

        // Validate the request
        $validated = $request->validate([
            // Keep 'competencies' required to ensure the array exists, even if empty initially from the form perspective
            'competencies' => 'present|array', 
            // Make proficiency level nullable, we'll filter empty ones later
            'competencies.*' => ['nullable', 'integer', 'in:' . implode(',', $proficiencyLevels)], 
            'details' => 'required|string|max:1000',
        ], [
            'competencies.required' => 'Please select at least one competency and its required proficiency level.',
            'competencies.*.required' => 'Please select a proficiency level for each chosen competency.',
            'competencies.*.in' => 'Invalid proficiency level selected.',
        ]);

        // Create the talent request (without talent_id initially)
        $talentRequest = TalentRequest::create([
            'user_id' => Auth::id(),
            // 'talent_id' will be assigned later, perhaps by admin or DSS
            'details' => $validated['details'],
            'status' => 'pending_admin', // Initial status, admin needs to review/assign
        ]);

        // Prepare data for attaching competencies with proficiency levels
        $competenciesToAttach = [];
        // Filter out competencies where no proficiency level was selected
        $selectedCompetencies = array_filter($validated['competencies'] ?? [], function($level) {
            return !is_null($level) && $level !== '';
        });

        // Ensure at least one competency was actually selected with a level
        if (empty($selectedCompetencies)) {
            // Redirect back with an error if no competencies were properly selected
            return back()->withErrors(['competencies' => 'Please select at least one competency and specify its required proficiency level.'])->withInput();
        }

        foreach ($selectedCompetencies as $competencyId => $proficiencyLevel) {
            // Check if competency exists (optional, but good practice)
            if (Competency::find($competencyId)) { 
                $competenciesToAttach[$competencyId] = ['required_proficiency_level' => $proficiencyLevel];
            }
        }

        // Attach the required competencies with their proficiency levels
        if (!empty($competenciesToAttach)) {
            $talentRequest->competencies()->attach($competenciesToAttach);
        }

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
