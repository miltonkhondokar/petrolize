<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FuelSalesDay extends Model
{
    protected $table = 'fuel_sales_days';

    protected $fillable = [
        'uuid','fuel_station_uuid','user_uuid','sale_date','status',
        'cash_amount','bank_amount','total_amount','note'
    ];

    protected $casts = [
        'sale_date' => 'date',
        'cash_amount' => 'decimal:2',
        'bank_amount' => 'decimal:2',
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

    public function manager()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function items()
    {
        return $this->hasMany(FuelSalesItem::class, 'fuel_sales_day_uuid', 'uuid');
    }
}