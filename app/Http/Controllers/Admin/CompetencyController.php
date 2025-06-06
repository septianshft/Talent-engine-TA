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
        $competencies = Competency::orderBy('name')->paginate(10);
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
        $request->validate([
            'name' => 'required|string|max:255|unique:competencies',
            'description' => 'nullable|string',
        ]);

        Competency::create($request->all());

        return redirect()->route('admin.competencies.index')
                         ->with('success', 'Competency created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Competency $competency)
    {
        return view('admin.competencies.show', compact('competency'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Competency $competency)
    {
        return view('admin.competencies.edit', compact('competency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Competency $competency)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:competencies,name,'. $competency->id,
            'description' => 'nullable|string',
        ]);

        $competency->update($request->all());

        return redirect()->route('admin.competencies.index')
                         ->with('success', 'Competency updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Competency $competency)
    {
        $competency->delete();

        return redirect()->route('admin.competencies.index')
                         ->with('success', 'Competency deleted successfully.');
    }
}
