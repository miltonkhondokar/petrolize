<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FuelPurchase extends Model
{
    protected $table = 'fuel_purchases';

    protected $fillable = [
        'uuid','fuel_station_uuid','vendor_uuid','purchase_date','invoice_no',
        'transport_by','truck_no','status','total_amount','note'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) { if (!$m->uuid) { $m->uuid = (string) Str::uuid(); } });
    }

    public function station()
    {
        return $this->belongsTo(FuelStation::class, 'fuel_station_uuid', 'uuid');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_uuid', 'uuid');
    }

    public function items()
    {
        return $this->hasMany(FuelPurchaseItem::class, 'fuel_purchase_uuid', 'uuid');
    }

    public function allocations()
    {
        return $this->hasMany(VendorPaymentAllocation::class, 'fuel_purchase_uuid', 'uuid');
    }

    public function paymentAllocations()
    {
        return $this->hasMany(
            VendorPaymentAllocation::class,
            'fuel_purchase_uuid',
            'uuid'
        );
    }

    /**
     * Convenience accessor (optional but VERY useful)
     */
    public function getPaidAmountAttribute(): float
    {
        return (float) $this->paymentAllocations()->sum('allocated_amount');
    }

    public function getBalanceAmountAttribute(): float
    {
        return (float) ($this->total_amount - $this->paid_amount);
    }
}