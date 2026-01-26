<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Center extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'governorate_uuid', 'name', 'is_active'];

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

    public function governorate()
    {
        return $this->belongsTo(Governorate::class, 'governorate_uuid', 'uuid');
    }

    public function cities()
    {
        return $this->hasMany(City::class, 'center_uuid', 'uuid');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
