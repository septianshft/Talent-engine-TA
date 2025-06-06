<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TalentRequest;
use App\Models\Competency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $totalUsers = User::count();
        
        // Assuming 'pending_admin' and 'pending_talent' are the statuses for pending requests
        $pendingRequests = TalentRequest::whereIn('status', ['pending_admin', 'pending_talent'])->count(); 
        
        // Count users specifically assigned the 'talent' role
        $activeTalents = User::whereHas('roles', function ($query) {
            $query->where('name', 'talent');
        })->count();

        // Fetch competency data for the chart
        $competencies = Competency::withCount(['users' => function ($query) {
            $query->whereHas('roles', function ($subQuery) {
                $subQuery->where('name', 'talent');
            });
        }])->get();

        $competencyLabels = $competencies->pluck('name');
        $competencyCounts = $competencies->pluck('users_count');

        return view('admin.dashboard', compact(
            'totalUsers', 
            'pendingRequests', 
            'activeTalents', 
            'competencyLabels', 
            'competencyCounts'
        ));
    }
}