<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()
            ->paginate(20)
            ->through(fn($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'email'      => $u->email,
                'phone'      => $u->phone_number,
                'role'       => $u->role,
                'verified'   => !is_null($u->email_verified_at),
                'created_at' => $u->created_at->format('M d, Y'),
            ]);

        $stats = [
            'total'    => User::count(),
            'admins'   => User::where('role', 'admin')->count(),
            'users'    => User::where('role', 'user')->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
        ];

        return Inertia::render('Admin/Users', compact('users', 'stats'));
    }
}
