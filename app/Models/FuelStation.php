<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FuelStation extends Model
{
    use HasFactory;

    protected $table = 'fuel_stations';

    protected $fillable = [
        'uuid',
        'user_uuid',
        'name',
        'location',
        'is_active',
        'region_uuid',
        'governorate_uuid',
        'center_uuid',
        'city_uuid',
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


    public function region()
    {
        return $this->belongsTo(Region::class, 'region_uuid', 'uuid');
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class, 'governorate_uuid', 'uuid');
    }

    public function center()
    {
        return $this->belongsTo(Center::class, 'center_uuid', 'uuid');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_uuid', 'uuid');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function fuelPrices()
    {
        return $this->hasMany(FuelStationPrice::class, 'fuel_station_uuid', 'uuid');
    }

    public function fuelStocks()
    {
        return $this->hasMany(FuelStationStock::class, 'fuel_station_uuid', 'uuid');
    }

    public function fuelReadings()
    {
        return $this->hasMany(FuelStationReading::class, 'fuel_station_uuid', 'uuid');
    }

    public function costs()
    {
        return $this->hasMany(CostEntry::class, 'fuel_station_uuid', 'uuid');
    }

    public function complaints()
    {
        return $this->hasMany(FuelStationComplaint::class, 'fuel_station_uuid', 'uuid');
    }

    public function latestFuelPrice($fuelTypeUuid)
    {
        return $this->hasOne(FuelStationPrice::class, 'fuel_station_uuid', 'uuid')
            ->where('fuel_type_uuid', $fuelTypeUuid)
            ->latest('created_at');
    }

    public function fuelTypes()
    {
        return $this->belongsToMany(
            FuelType::class,
            'pump_fuel_type',            // Pivot table
            'fuel_station_uuid',                 // Foreign key on pivot for Fuel Station
            'fuel_type_uuid',            // Foreign key on pivot for FuelType
            'uuid',                      // Local key on Fuel Station
            'uuid'                       // Local key on FuelType
        )->withTimestamps(); // optional, keeps track of pivot created_at / updated_at
    }
}
