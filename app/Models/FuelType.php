<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use phpseclib3\File\ASN1\Maps\ExtKeyUsageSyntax;

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

    public function stationPrices()
    {
        return $this->hasMany(FuelStationPrice::class, 'fuel_type_uuid', 'uuid');
    }

    public function stationStocks()
    {
        return $this->hasMany(FuelStationStock::class, 'fuel_type_uuid', 'uuid');
    }

    public function stationReadings()
    {
        return $this->hasMany(FuelStationReading::class, 'fuel_type_uuid', 'uuid');
    }

    public function defaultUnit()
    {
        return $this->belongsTo(FuelUnit::class, 'fuel_unit_uuid', 'uuid');
    }

    public function stockLedgers()
    {
        return $this->hasMany(FuelStockLedger::class, 'fuel_type_uuid', 'uuid');
    }

    public function stations()
    {
        return $this->belongsToMany(
            FuelStation::class,
            'fuel_station_fuel_type',
            'fuel_type_uuid',
            'fuel_station_uuid',
            'uuid',
            'uuid'
        )->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}

// usages

// $in  = $fuelType->stockLedgers()->sum('qty_in');
// $out = $fuelType->stockLedgers()->sum('qty_out');

// $currentStock = $in - $out;
