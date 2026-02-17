<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'countries',
        'is_enabled',
        'sort_order',
    ];

    protected $casts = [
        'countries'  => 'array',
        'is_enabled' => 'boolean',
    ];

    /**
     * Scope to only enabled payment methods, ordered by sort_order.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true)->orderBy('sort_order');
    }
}
