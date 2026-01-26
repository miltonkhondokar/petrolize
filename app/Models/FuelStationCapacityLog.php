<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FuelStationCapacityLog extends Model
{
    protected $table = 'fuel_station_capacity_logs';

    protected $fillable = [
        'uuid',
        'fuel_station_uuid',
        'fuel_type_uuid',
        'capacity_liters',
        'effective_from',
        'note',
        'is_active',
    ];

    protected $casts = [
        'capacity_liters' => 'decimal:3',
        'effective_from'  => 'datetime',
        'is_active'       => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (!$m->uuid) {
                $m->uuid = (string) Str::uuid();
            }
        });
    }

    public function station()
    {
        return $this->belongsTo(FuelStation::class, 'fuel_station_uuid', 'uuid');
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class, 'fuel_type_uuid', 'uuid');
    }

    // latest active capacity
    public function scopeLatest($q)
    {
        return $q->where('is_active', true)
                 ->orderByDesc('effective_from')
                 ->orderByDesc('id');
    }
}
