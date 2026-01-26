<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'name', 'code', 'is_active'];

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

    public function governorates()
    {
        return $this->hasMany(Governorate::class, 'region_uuid', 'uuid');
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
