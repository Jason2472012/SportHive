<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Super Admin dashboard overview.
     */
    public function index(Request $request): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_venues' => Schema::hasTable('venues') ? DB::table('venues')->count() : 0,
            'total_sports' => Schema::hasTable('sports') ? DB::table('sports')->count() : 0,
            'total_reports' => Schema::hasTable('reports') ? DB::table('reports')->count() : 0,
        ];

        $recentUsers = User::latest('created_at')->take(5)->get();

        return view('superadmin.dashboard', [
            'stats' => $stats,
            'recent_users' => $recentUsers,
        ]);
    }
}
