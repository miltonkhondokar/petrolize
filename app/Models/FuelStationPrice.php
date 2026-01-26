<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FuelStationPrice extends Model
{
    use HasFactory;

    protected $table = 'fuel_station_prices';

    protected $fillable = [
        'uuid',
        'fuel_station_uuid',
        'fuel_type_uuid',
        'price_per_unit',
        'is_active',
    ];

    protected $casts = [
        'price_per_unit' => 'decimal:2',
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

    public function fuelStation()
    {
        return $this->belongsTo(FuelStation::class, 'fuel_station_uuid', 'uuid');
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
