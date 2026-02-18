<?php

namespace Database\Seeders;

use App\Models\VoucherProvider;
use Illuminate\Database\Seeder;

class VoucherProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            ['code' => '1voucher',    'name' => '1Voucher',       'is_enabled' => true,  'sort_order' => 1],
            ['code' => 'ott',         'name' => 'OTT Voucher',    'is_enabled' => true,  'sort_order' => 2],
            ['code' => 'bluevoucher', 'name' => 'Blue Voucher',   'is_enabled' => true,  'sort_order' => 3],
            ['code' => 'ecocash_v',   'name' => 'EcoCash Voucher','is_enabled' => false, 'sort_order' => 4],
        ];

        foreach ($providers as $provider) {
            VoucherProvider::updateOrCreate(['code' => $provider['code']], $provider);
        }
    }
}
