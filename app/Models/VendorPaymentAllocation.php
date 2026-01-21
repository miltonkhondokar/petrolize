<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VendorPaymentAllocation extends Model
{
    protected $table = 'vendor_payment_allocations';

    protected $fillable = [
        'uuid','vendor_payment_uuid','fuel_purchase_uuid','allocated_amount'
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) { if (!$m->uuid) { $m->uuid = (string) Str::uuid(); } });
    }

    public function payment()
    {
        return $this->belongsTo(VendorPayment::class, 'vendor_payment_uuid', 'uuid');
    }

    public function purchase()
    {
        return $this->belongsTo(FuelPurchase::class, 'fuel_purchase_uuid', 'uuid');
    }
}