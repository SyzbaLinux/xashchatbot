<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::orderBy('sort_order')->get();

        return Inertia::render('Admin/PaymentMethods', ['methods' => $methods]);
    }

    public function toggle(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update(['is_enabled' => !$paymentMethod->is_enabled]);

        return back()->with('success', "Payment method '{$paymentMethod->name}' " . ($paymentMethod->is_enabled ? 'enabled' : 'disabled') . '.');
    }
}
