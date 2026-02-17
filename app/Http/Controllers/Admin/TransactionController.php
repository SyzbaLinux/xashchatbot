<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::latest()
            ->paginate(20)
            ->through(fn($t) => [
                'id'             => $t->id,
                'phone_number'   => $t->phone_number,
                'type'           => ucfirst($t->type),
                'recipient'      => $t->recipient,
                'amount'         => $t->amount,
                'currency'       => $t->currency,
                'payment_method' => $t->payment_method,
                'voucher_code'   => $t->voucher_code,
                'status'         => $t->status,
                'created_at'     => $t->created_at->format('M d, Y H:i'),
            ]);

        $stats = [
            'total'      => Transaction::count(),
            'successful' => Transaction::where('status', 'success')->count(),
            'failed'     => Transaction::where('status', 'failed')->count(),
            'volume'     => Transaction::where('status', 'success')->sum('amount'),
        ];

        return Inertia::render('Admin/Transactions', compact('transactions', 'stats'));
    }
}
