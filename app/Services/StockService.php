<?php


// App/Services/StockService.php

namespace App\Services;

use App\Models\FuelStockLedger;

class StockService
{
    public static function balance(string $stationUuid, string $fuelTypeUuid): float
    {
        $in = FuelStockLedger::where('fuel_station_uuid', $stationUuid)
            ->where('fuel_type_uuid', $fuelTypeUuid)
            ->sum('qty_in');

        $out = FuelStockLedger::where('fuel_station_uuid', $stationUuid)
            ->where('fuel_type_uuid', $fuelTypeUuid)
            ->sum('qty_out');

        return (float)$in - (float)$out;
    }
}

// usages

// $balance = StockService::balance($stationUuid, $fuelTypeUuid);