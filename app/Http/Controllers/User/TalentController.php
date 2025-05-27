<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Competency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TalentController extends Controller
{
    /**
     * Display a listing of available talents with search and filter functionality.
     */    public function index(Request $request)
    {
        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->with(['competencies', 'roles']);

        // Get all competencies for filter dropdown
        $competencies = Competency::orderBy('name')->get();        // Get unique provinces and cities for location filters
        $provinces = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->whereNotNull('domicile_country')
          ->distinct()
          ->pluck('domicile_country')
          ->sort()
          ->values();

        $cities = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->whereNotNull('domicile_city')
          ->distinct()
          ->pluck('domicile_city')
          ->sort()
          ->values();

        // Apply competency filter
        if ($request->filled('competency_id')) {
            $query->whereHas('competencies', function ($q) use ($request) {
                $q->where('competencies.id', $request->competency_id);
            });
        }

        // Apply proficiency level filter
        if ($request->filled('min_proficiency')) {
            $query->whereHas('competencies', function ($q) use ($request) {
                $q->where('competency_user.proficiency_level', '>=', $request->min_proficiency);
            });        }

        // Apply province filter
        if ($request->filled('province')) {
            $query->where('domicile_country', $request->province);
        }

        // Apply city filter
        if ($request->filled('city')) {
            $query->where('domicile_city', $request->city);
        }

        // Apply search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Order by name and paginate
        $talents = $query->orderBy('name')->paginate(12);

        return view('user.talents.index', compact('talents', 'competencies', 'provinces', 'cities'));
    }

    /**
     * Display the specified talent profile.
     */
    public function show(User $talent)
    {
        // Ensure the user is actually a talent
        if (!$talent->hasRole('talent')) {
            abort(404, 'Talent not found.');
        }

        // Load competencies for the talent
        $talent->load('competencies');

        return view('user.talents.show', compact('talent'));
    }    /**
     * Handle AJAX search requests for talent discovery.
     */
    public function search(Request $request)
    {
        if (!$request->ajax()) {
            return redirect()->route('user.talents.index');
        }

        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->with(['competencies', 'roles']);

        // Apply competency filter
        if ($request->filled('competency_id')) {
            $query->whereHas('competencies', function ($q) use ($request) {
                $q->where('competencies.id', $request->competency_id);
            });
        }

        // Apply proficiency level filter
        if ($request->filled('min_proficiency')) {
            $query->whereHas('competencies', function ($q) use ($request) {
                $q->where('competency_user.proficiency_level', '>=', $request->min_proficiency);
            });        }

        // Apply province filter
        if ($request->filled('province')) {
            $query->where('domicile_country', $request->province);
        }

        // Apply city filter
        if ($request->filled('city')) {
            $query->where('domicile_city', $request->city);
        }

        // Apply search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Order by name and paginate
        $talents = $query->orderBy('name')->paginate(12);

        // Return partial view for AJAX requests
        $html = view('user.talents.partials.search-results', compact('talents'))->render();

        return response()->json([
            'html' => $html
        ]);
    }
}
