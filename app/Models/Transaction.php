<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'phone_number',
        'type',
        'recipient',
        'amount',
        'currency',
        'payment_method',
        'voucher_code',
        'status',
        'response_data',
    ];

    protected $casts = [
        'amount'        => 'float',
        'response_data' => 'array',
    ];
}
