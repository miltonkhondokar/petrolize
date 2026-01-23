<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FuelStationComplaint extends Model
{
    use HasFactory;

    protected $table = 'fuel_station_complaints';

    protected $fillable = [
        'uuid',
        'fuel_station_uuid',
        'complaint_category_uuid',
        'title',
        'description',
        'status',
        'complaint_date',
        'resolved_date',
        'is_active',
    ];

    protected $casts = [
        'complaint_date' => 'date',
        'resolved_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->uuid) $model->uuid = (string) Str::uuid();
        });
    }

    // Relations
    public function fuelStation()
    {
        return $this->belongsTo(FuelStation::class, 'fuel_station_uuid', 'uuid');
    }

    public function category()
    {
        return $this->belongsTo(
            ComplaintCategory::class,
            'complaint_category_uuid',
            'uuid'
        );
    }
}
