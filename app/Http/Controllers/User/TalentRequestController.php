<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TalentRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Competency;
use Illuminate\Http\Request;
use App\Http\Requests\TalentRequestRequest;
use Illuminate\Support\Facades\DB;

class TalentRequestController extends Controller
{
    /**
     * Display a listing of the talent requests created by the user.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Eager load the assignedTalents relationship
        // This will fetch all talents assigned to each request through the pivot table.
        $requests = $user->createdRequests()
                         ->with(['assignedTalents' => function ($query) {
                             // Optionally, you can select specific fields from the pivot table or related talent model
                             $query->withPivot('status', 'created_at'); // Load pivot status and assignment time
                         }])
                         ->latest()
                         ->paginate(10);

        return view('user.requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new talent request (goes through admin approval).
     */
    public function create()
    {
        // Fetch competencies that are possessed by at least one talent
        $competencies = Competency::whereHas('users') // 'users' is the relationship name in Competency model
                                  ->orderBy('name')
                                  ->get();

        return view('user.requests.create', compact('competencies'));
    }

    /**
     * Show the form for creating a direct talent request (bypasses admin approval).
     */
    public function createDirect(User $talent)
    {
        // Ensure the selected user is actually a talent
        if (!$talent->hasRole('talent')) {
            abort(404, 'Talent not found.');
        }

        // Fetch competencies that this specific talent possesses
        $competencies = $talent->competencies()->orderBy('name')->get();

        // If talent has no competencies, fetch all competencies
        if ($competencies->isEmpty()) {
            $competencies = Competency::orderBy('name')->get();
        }

        return view('user.requests.create-direct', compact('competencies', 'talent'));
    }

    /**
     * Store a newly created talent request in storage.
     */
    public function store(TalentRequestRequest $request)
    {

        // Get validated data from the enhanced request
        $validated = $request->validated();

        // Check if this is a direct talent request
        $isDirect = $request->has('talent_id');

        // Determine status and talent assignment based on request type
        if ($isDirect) {
            // Verify the selected user is actually a talent
            $talent = User::find($validated['talent_id']);
            if (!$talent || !$talent->hasRole('talent')) {
                return back()->withErrors(['talent_id' => 'Selected user is not a valid talent.'])->withInput();
            }

            $status = 'pending_talent';
            $successMessage = 'Direct talent request sent successfully. The talent will be notified to respond.';
        } else {
            $status = 'pending_admin';
            $successMessage = 'Talent request submitted successfully. It will be reviewed by an administrator.';
        }

        // Create the talent request
        $talentRequest = TalentRequest::create([
            'user_id' => Auth::id(),
            'details' => $validated['details'],
            'status' => $status,
            'work_location_type' => $validated['work_location_type'],
            'work_location_country' => $validated['work_location_type'] === 'remote' ? null : $validated['work_location_country'],
            'work_location_city' => $validated['work_location_type'] === 'remote' ? null : $validated['work_location_city'],
        ]);

        // If this is a direct request, assign the talent immediately
        if ($isDirect) {
            $talentRequest->assignedTalents()->attach($validated['talent_id'], [
                'status' => 'pending_assignment_response'
            ]);
        }

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

        return redirect()->route('user.requests.index')->with('success', $successMessage);
    }

    /**
     * Store a newly created direct talent request in storage.
     * This is a simplified version for direct requests to specific talents.
     */
    public function storeDirect(Request $request, User $talent)
    {
        // Ensure the selected user is actually a talent
        if (!$talent->hasRole('talent')) {
            abort(404, 'Talent not found.');
        }

        // Validate the request
        $validated = $request->validate([
            'details' => 'required|string|max:1000',
            'work_location_type' => 'required|string|in:on_site,remote,hybrid',
            'work_location_country' => 'nullable|string|max:255|required_if:work_location_type,on_site,hybrid',
            'work_location_city' => 'nullable|string|max:255|required_if:work_location_type,on_site,hybrid',
            'competencies' => 'nullable|array', // Nullable if talent has no skills
            'competencies.*.id' => 'required_with:competencies|integer|exists:competencies,id',
            'competencies.*.level' => 'required_with:competencies|integer|in:1,2,3,4',
            'competencies.*.weight' => 'required_with:competencies|integer|in:1,2,3,4,5',
        ], [
            'work_location_type.required' => 'Please select a work location type.',
            'work_location_type.in' => 'Invalid work location type selected.',
            'work_location_country.required_if' => 'Country is required for on-site or hybrid work locations.',
            'work_location_city.required_if' => 'City is required for on-site or hybrid work locations.',
            'competencies.*.id.required_with' => 'Competency ID is missing.',
            'competencies.*.id.exists' => 'Invalid competency selected.',
            'competencies.*.level.required_with' => 'Competency level is missing.',
            'competencies.*.level.in' => 'Invalid competency level selected.',
            'competencies.*.weight.required_with' => 'Competency weight is missing.',
            'competencies.*.weight.in' => 'Invalid competency weight selected.',
        ]);

        // Create the talent request with status 'pending_talent' (overall request status)
        $talentRequest = TalentRequest::create([
            'user_id' => Auth::id(), // The user creating the request
            'details' => $validated['details'],
            'status' => 'pending_talent', // Status of the TalentRequest itself
            'work_location_type' => $validated['work_location_type'],
            'work_location_country' => $validated['work_location_type'] === 'remote' ? null : $validated['work_location_country'],
            'work_location_city' => $validated['work_location_type'] === 'remote' ? null : $validated['work_location_city'],
        ]);

        // Assign the talent immediately with direct offer status
        $talentRequest->assignedTalents()->attach($talent->id, [
            'status' => 'direct_offer_pending', // Status of this specific assignment
            'assignment_type' => 'direct_offer',
            'assigned_by' => Auth::id(), // The user who initiated this direct offer
        ]);

        // Attach competencies from the request
        $competenciesToAttach = [];
        if (!empty($validated['competencies'])) {
            foreach ($validated['competencies'] as $compData) {
                $competenciesToAttach[$compData['id']] = [
                    'required_proficiency_level' => $compData['level'],
                    'weight' => $compData['weight']
                ];
            }
        }

        if (!empty($competenciesToAttach)) {
            $talentRequest->competencies()->attach($competenciesToAttach);
        }

        return redirect()->route('user.requests.index')->with('success', 'Direct talent request sent successfully. The talent will be notified to respond.');
    }

    /**
     * Display the specified talent request.
     */
    public function show(TalentRequest $talentRequest)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ensure the user owns this request
        if ($talentRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Eager load assigned talents and their pivot status, and also the competencies for the request
        $talentRequest->load(['assignedTalents' => function ($query) {
            $query->withPivot('status', 'created_at', 'updated_at');
        }, 'competencies']);

        return view('user.requests.show', compact('talentRequest'));
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

        // Optional: Add check to only allow deletion if status is 'pending_admin' or similar non-active states
        // if (!in_array($talentRequest->status, ['pending_admin', 'draft'])) { // Example states
        //     return back()->with('error', 'Cannot delete a request that is already being processed or is finalized.');
        // }

        // Detach related competencies and talent assignments before deleting the request to maintain data integrity if needed,
        // or rely on database cascade rules if set up.
        // $talentRequest->competencies()->detach();
        // $talentRequest->assignedTalents()->detach(); // This will clear entries from the pivot table

        $talentRequest->delete();

        return redirect()->route('user.requests.index')->with('success', 'Talent request deleted successfully.');
    }
}
