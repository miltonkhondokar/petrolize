<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendors';

    protected $fillable = [
        'uuid',
        'name',
        'phone',
        'email',
        'address',
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

    public function purchases()
    {
        return $this->hasMany(FuelPurchase::class, 'vendor_uuid', 'uuid');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function purchaseItems()
    {
        return $this->hasManyThrough(
            FuelPurchaseItem::class,
            FuelPurchase::class,
            'vendor_uuid',        // FK on fuel_purchases
            'fuel_purchase_uuid', // FK on fuel_purchase_items
            'uuid',
            'uuid'
        );
    }

    public function totalFuelSupplied()
    {
        return $this->purchaseItems()->sum('quantity_ltr');
    }
}
