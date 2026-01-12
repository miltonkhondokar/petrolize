<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PumpFuelStock extends Model
{
    use HasFactory;

    protected $table = 'pump_fuel_stocks';

    protected $fillable = [
        'uuid',
        'pump_uuid',
        'fuel_type_uuid',
        'fuel_unit_uuid',
        'quantity',
        'purchase_price',
        'total_cost',
        'reference_no',
        'stock_date',
        'is_initial_stock',
        'is_active',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'purchase_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'is_initial_stock' => 'boolean',
        'is_active' => 'boolean',
        'stock_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Relations
    public function pump()
    {
        return $this->belongsTo(Pump::class, 'pump_uuid', 'uuid');
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class, 'fuel_type_uuid', 'uuid');
    }

    public function fuelUnit()
    {
        return $this->belongsTo(FuelUnit::class, 'fuel_unit_uuid', 'uuid');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_uuid', 'uuid');
    }
}
