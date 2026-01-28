<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FuelStationFinanceLedger extends Model
{
    protected $table = 'fuel_station_finance_ledgers';

    protected $fillable = [
        'uuid',
        'fuel_station_uuid',
        'txn_type',
        'txn_date',
        'debit_amount',
        'credit_amount',
        'ref_table',
        'ref_uuid',
        'note',
        'created_by',
    ];

    protected $casts = [
        'txn_date' => 'date',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
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

    /* ===================== Relations ===================== */

    public function fuelStation()
    {
        return $this->belongsTo(FuelStation::class, 'fuel_station_uuid', 'uuid');
    }
}
