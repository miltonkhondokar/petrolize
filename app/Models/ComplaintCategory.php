<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ComplaintCategory extends Model
{
    protected $table = 'complaint_categories';

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'description',
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
    public function complaints()
    {
        return $this->hasMany(
            FuelStationComplaint::class,
            'complaint_category_uuid',
            'uuid'
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
