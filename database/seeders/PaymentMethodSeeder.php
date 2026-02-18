<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'code'        => 'voucher',
                'name'        => '1Voucher / OTT Voucher',
                'description' => 'Pay using a 1Voucher, OTT, or prepaid voucher code',
                'countries'   => ['ZW', 'ZA', 'BW'],
                'is_enabled'  => true,
                'sort_order'  => 1,
            ],
            [
                'code'        => 'ecocash',
                'name'        => 'EcoCash',
                'description' => 'Pay using your EcoCash mobile money wallet',
                'countries'   => ['ZW'],
                'is_enabled'  => true,
                'sort_order'  => 2,
            ],
            [
                'code'        => 'mpesa',
                'name'        => 'M-Pesa',
                'description' => 'Pay using M-Pesa mobile money',
                'countries'   => ['ZA', 'BW'],
                'is_enabled'  => true,
                'sort_order'  => 3,
            ],
            [
                'code'        => 'orangemoney',
                'name'        => 'Orange Money',
                'description' => 'Pay using Orange Money mobile wallet',
                'countries'   => ['BW'],
                'is_enabled'  => true,
                'sort_order'  => 4,
            ],
            [
                'code'        => 'express',
                'name'        => 'Instant EFT / Credit Card',
                'description' => 'Pay via instant EFT or credit card',
                'countries'   => ['ZA', 'ZW', 'BW'],
                'is_enabled'  => true,
                'sort_order'  => 5,
            ],
            [
                'code'        => 'onemoney',
                'name'        => 'OneMoney',
                'description' => 'Pay using your OneMoney mobile money wallet',
                'countries'   => ['ZW'],
                'is_enabled'  => false,
                'sort_order'  => 6,
            ],
            [
                'code'        => 'innbucks',
                'name'        => 'InnBucks',
                'description' => 'Pay using your InnBucks wallet',
                'countries'   => ['ZW'],
                'is_enabled'  => false,
                'sort_order'  => 7,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(['code' => $method['code']], $method);
        }
    }
}
