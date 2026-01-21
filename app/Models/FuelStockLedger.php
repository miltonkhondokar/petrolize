<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FuelStockLedger extends Model
{
    protected $table = 'fuel_stock_ledgers';

    protected $fillable = [
        'uuid','fuel_station_uuid','fuel_type_uuid','fuel_unit_uuid',
        'txn_type','ref_uuid','txn_date','qty_in','qty_out','balance_after','note'
    ];

    protected $casts = [
        'txn_date' => 'date',
        'qty_in' => 'decimal:3',
        'qty_out' => 'decimal:3',
        'balance_after' => 'decimal:3',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) { if (!$m->uuid) { $m->uuid = (string) Str::uuid(); } });
    }
}