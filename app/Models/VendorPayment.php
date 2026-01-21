<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VendorPayment extends Model
{
    protected $table = 'vendor_payments';

    protected $fillable = [
        'uuid','vendor_uuid','created_by_user_uuid','payment_date','method',
        'amount','reference_no','note'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) { if (!$m->uuid) { $m->uuid = (string) Str::uuid(); } });
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_uuid', 'uuid');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_uuid', 'uuid');
    }

    public function allocations()
    {
        return $this->hasMany(VendorPaymentAllocation::class, 'vendor_payment_uuid', 'uuid');
    }
}