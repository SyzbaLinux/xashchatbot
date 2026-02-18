<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VoucherProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VoucherProviderController extends Controller
{
    public function index()
    {
        $providers = VoucherProvider::orderBy('sort_order')->get();

        return Inertia::render('Admin/VoucherProviders', ['providers' => $providers]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'       => 'required|string|max:50|unique:voucher_providers,code|alpha_dash',
            'name'       => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        VoucherProvider::create([
            'code'       => strtolower($data['code']),
            'name'       => $data['name'],
            'is_enabled' => false,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return back()->with('success', "Voucher provider '{$data['name']}' added successfully.");
    }

    public function update(Request $request, VoucherProvider $voucherProvider)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $voucherProvider->update([
            'name'       => $data['name'],
            'sort_order' => $data['sort_order'] ?? $voucherProvider->sort_order,
        ]);

        return back()->with('success', "Voucher provider '{$voucherProvider->name}' updated.");
    }

    public function toggle(VoucherProvider $voucherProvider)
    {
        $voucherProvider->update(['is_enabled' => !$voucherProvider->is_enabled]);

        $state = $voucherProvider->is_enabled ? 'enabled' : 'disabled';

        return back()->with('success', "'{$voucherProvider->name}' has been {$state}.");
    }

    public function destroy(VoucherProvider $voucherProvider)
    {
        $name = $voucherProvider->name;
        $voucherProvider->delete();

        return back()->with('success', "Voucher provider '{$name}' deleted.");
    }
}
