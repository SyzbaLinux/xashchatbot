<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $user  = Auth::user();
        $phone = $user->phone_number;

        $transactions = $phone
            ? Transaction::where('phone_number', $phone)->latest()->get()
            : collect();

        $recent = $transactions->take(5)->map(fn($t) => [
            'id'         => $t->id,
            'type'       => ucfirst($t->type),
            'recipient'  => $t->recipient,
            'amount'     => $t->amount,
            'currency'   => $t->currency,
            'status'     => $t->status,
            'method'     => $t->payment_method,
            'created_at' => $t->created_at->format('M d, Y H:i'),
        ]);

        return Inertia::render('User/Dashboard', [
            'user' => [
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'created_at' => $user->created_at->format('M d, Y'),
            ],
            'stats' => [
                'total_transactions'      => $transactions->count(),
                'successful_transactions' => $transactions->where('status', 'success')->count(),
                'failed_transactions'     => $transactions->where('status', 'failed')->count(),
                'total_spent'             => $transactions->where('status', 'success')->sum('amount'),
            ],
            'recent_transactions' => $recent,
        ]);
    }
}
