<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competency;
use Illuminate\Http\Request;

class CompetencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $competencies = Competency::orderBy('name')->paginate(10); // Get competencies, ordered by name
        return view('admin.competencies.index', compact('competencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.competencies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:competencies,name',
        ]);

        Competency::create($validated);

        return redirect()->route('admin.competencies.index')->with('success', 'Competency created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Competency $competency) // Route model binding
    {
        return view('admin.competencies.edit', compact('competency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Competency $competency) // Route model binding
    {
        $validated = $request->validate([
            // Ensure unique name, ignoring the current competency's name
            'name' => 'required|string|max:255|unique:competencies,name,' . $competency->id,
        ]);

        $competency->update($validated);

        return redirect()->route('admin.competencies.index')->with('success', 'Competency updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Competency $competency) // Route model binding
    {
        // Optional: Add check if competency is in use before deleting
        // if ($competency->users()->exists()) {
        //     return back()->with('error', 'Cannot delete competency as it is assigned to users.');
        // }

        $competency->delete();

        return redirect()->route('admin.competencies.index')->with('success', 'Competency deleted successfully.');
    }
}
