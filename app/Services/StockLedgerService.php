<?php

namespace App\Services;

use App\Models\FuelStockLedger;

class StockLedgerService
{
    public static function getBalance(string $stationUuid, string $fuelTypeUuid): float
    {
        $in = (float) FuelStockLedger::where('fuel_station_uuid', $stationUuid)
            ->where('fuel_type_uuid', $fuelTypeUuid)
            ->sum('qty_in');

        $out = (float) FuelStockLedger::where('fuel_station_uuid', $stationUuid)
            ->where('fuel_type_uuid', $fuelTypeUuid)
            ->sum('qty_out');

        return $in - $out;
    }

    public static function add(array $data): FuelStockLedger
    {
        // balance_after optional auto-calc
        if (!array_key_exists('balance_after', $data) || $data['balance_after'] === null) {
            $balance = self::getBalance($data['fuel_station_uuid'], $data['fuel_type_uuid']);
            $balance = $balance + (float)($data['qty_in'] ?? 0) - (float)($data['qty_out'] ?? 0);
            $data['balance_after'] = $balance;
        }

        return FuelStockLedger::create($data);
    }
}