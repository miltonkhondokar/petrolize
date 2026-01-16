<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FuelUnit extends Model
{
    use HasFactory;

    protected $table = 'fuel_units';

    protected $fillable = ['uuid', 'name', 'abbreviation', 'description', 'is_active'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function fuelTypes()
    {
        return $this->hasMany(FuelType::class, 'fuel_unit_uuid', 'uuid');
    }

    public function pumpFuelStocks()
    {
        return $this->hasMany(FuelStationStock::class, 'fuel_unit_uuid', 'uuid');
    }
}
