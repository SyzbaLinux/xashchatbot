<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        return Inertia::render('User/Dashboard', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at->format('M d, Y'),
            ],
            'stats' => [
                'total_tasks' => 0, // Add real data based on your business logic
                'completed_tasks' => 0,
                'pending_tasks' => 0,
                'total_errands' => 0,
            ],
            'recent_activity' => [], // Add real data
        ]);
    }
}
