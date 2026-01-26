<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FuelStationFuelType extends Model
{
    use HasFactory;

    protected $table = 'fuel_station_fuel_type';

    protected $fillable = [
        'uuid',
        'fuel_station_uuid',
        'fuel_type_uuid',
        'is_active'
    ];

    protected static function boot()
    {
        parent::boot();

        // Automatically generate UUID when creating
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relation: FuelStationFuelType belongs to Fuel Station
     */
    public function fuelStation()
    {
        return $this->belongsTo(FuelStation::class, 'fuel_station_uuid', 'uuid');
    }

    /**
     * Relation: FuelStationFuelType belongs to FuelType
     */
    public function fuelType()
    {
        return $this->belongsTo(FuelType::class, 'fuel_type_uuid', 'uuid');
    }

    /**
     * Optional helper: get the latest price of this fuel for this fuel station
     */
    public function latestPrice()
    {
        return $this->hasOne(FuelStationPrice::class, 'fuel_station_uuid', 'fuel_station_uuid')
            ->where('fuel_type_uuid', $this->fuel_type_uuid)
            ->latest('created_at');
    }

    /**
     * Optional helper: get the current stock of this fuel for this fuel station
     */
    public function latestStock()
    {
        return $this->hasOne(FuelStationStock::class, 'fuel_type_uuid', 'fuel_type_uuid')
            ->whereColumn('fuel_station_uuid', 'fuel_station_fuel_type.fuel_station_uuid')
            ->latest('stock_date'); // or ->latest('id')
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
