<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FuelPurchaseItem extends Model
{
    protected $table = 'fuel_purchase_items';

    protected $fillable = [
        'uuid','fuel_purchase_uuid','fuel_type_uuid','fuel_unit_uuid',
        'quantity','received_qty','unit_price','line_total','is_active'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'received_qty' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) { if (!$m->uuid) { $m->uuid = (string) Str::uuid(); } });

        static::saving(function ($m) {
            $m->line_total = (string) ((float)$m->quantity * (float)$m->unit_price);
        });
    }

    public function purchase()
    {
        return $this->belongsTo(FuelPurchase::class, 'fuel_purchase_uuid', 'uuid');
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class, 'fuel_type_uuid', 'uuid');
    }

    public function fuelUnit()
    {
        return $this->belongsTo(FuelUnit::class, 'fuel_unit_uuid', 'uuid');
    }
}