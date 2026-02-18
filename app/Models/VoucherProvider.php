<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherProvider extends Model
{
    protected $fillable = ['code', 'name', 'is_enabled', 'sort_order'];

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true)->orderBy('sort_order');
    }
}
