<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CostEntry extends Model
{
    use HasFactory;

    protected $table = 'cost_entries';

    protected $fillable = [
        'uuid',
        'fuel_station_uuid',
        'cost_category_uuid',
        'amount',
        'expense_date',
        'reference_no',
        'note',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
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

    public function category()
    {
        return $this->belongsTo(CostCategory::class, 'cost_category_uuid', 'uuid');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
