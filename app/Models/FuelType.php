<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FuelType extends Model
{
    use HasFactory;

    protected $table = 'fuel_types';

    protected $fillable = [
        'uuid',
        'name',
        'code',
        'rating_value',
        'fuel_unit_uuid',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    public function pumpFuelPrices()
    {
        return $this->hasMany(FuelStationPrice::class, 'fuel_type_uuid', 'uuid');
    }

    public function pumpFuelStocks()
    {
        return $this->hasMany(FuelStationStock::class, 'fuel_type_uuid', 'uuid');
    }

    public function pumpFuelReadings()
    {
        return $this->hasMany(PumpFuelReading::class, 'fuel_type_uuid', 'uuid');
    }

    public function defaultUnit()
    {
        return $this->belongsTo(FuelUnit::class, 'fuel_unit_uuid', 'uuid');
    }

    public function pumps()
    {
        return $this->belongsToMany(
            FuelStation::class,
            'pump_fuel_prices',
            'fuel_type_uuid',
            'fuel_station_uuid',
            'uuid',
            'uuid'
        );
    }
}
