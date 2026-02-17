<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalRegularUsers = User::where('role', 'user')->count();

        return Inertia::render('Admin/Dashboard', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'stats' => [
                'total_users' => $totalUsers,
                'total_admins' => $totalAdmins,
                'total_regular_users' => $totalRegularUsers,
                'verified_users' => User::whereNotNull('email_verified_at')->count(),
                'unverified_users' => User::whereNull('email_verified_at')->count(),
            ],
            'recent_users' => User::latest('created_at')
                ->limit(5)
                ->get(['id', 'name', 'email', 'role', 'created_at'])
                ->map(function ($u) {
                    return [
                        'id' => $u->id,
                        'name' => $u->name,
                        'email' => $u->email,
                        'role' => $u->role,
                        'created_at' => $u->created_at->format('M d, Y H:i'),
                        'verified' => !is_null($u->email_verified_at),
                    ];
                }),
        ]);
    }
}
