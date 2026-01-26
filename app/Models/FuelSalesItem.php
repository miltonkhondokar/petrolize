<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FuelSalesItem extends Model
{
    protected $table = 'fuel_sales_items';

    protected $fillable = [
        'uuid','fuel_sales_day_uuid','fuel_type_uuid','nozzle_number',
        'opening_reading','closing_reading','sold_qty','price_per_unit','line_total','is_active'
    ];

    protected $casts = [
        'opening_reading' => 'decimal:3',
        'closing_reading' => 'decimal:3',
        'sold_qty' => 'decimal:3',
        'price_per_unit' => 'decimal:2',
        'line_total' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($m) { if (!$m->uuid) { $m->uuid = (string) Str::uuid(); } });

        static::saving(function ($m) {
            $sold = max(0, (float)$m->closing_reading - (float)$m->opening_reading);
            $m->sold_qty = (string) $sold;
            $m->line_total = (string) ($sold * (float)$m->price_per_unit);
        });
    }

    public function salesDay()
    {
        return $this->belongsTo(FuelSalesDay::class, 'fuel_sales_day_uuid', 'uuid');
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class, 'fuel_type_uuid', 'uuid');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
