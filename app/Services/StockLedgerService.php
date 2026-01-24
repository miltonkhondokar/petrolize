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

    /**
     * Stock IN
     */
    public static function in(array $data): FuelStockLedger
    {
        $balance = self::getBalance($data['fuel_station_uuid'], $data['fuel_type_uuid']);

        $balance += (float) ($data['quantity'] ?? 0);

        return FuelStockLedger::create([
            'fuel_station_uuid' => $data['fuel_station_uuid'],
            'fuel_type_uuid'    => $data['fuel_type_uuid'],
            'fuel_unit_uuid'    => $data['fuel_unit_uuid'],
            'qty_in'            => $data['quantity'] ?? 0,
            'qty_out'           => 0,
            'txn_type'          => 'IN',
            'balance_after'     => $balance,
            'txn_date'          => $data['date'] ?? now(), // ✅ Must set txn_date
            'reference_type'    => $data['reference_type'] ?? null,
            'reference_uuid'    => $data['reference_uuid'] ?? null,
        ]);
    }


    /**
     * Generic ledger add
     */
    public static function add(array $data): FuelStockLedger
    {
        if (!array_key_exists('balance_after', $data) || $data['balance_after'] === null) {
            $balance = self::getBalance(
                $data['fuel_station_uuid'],
                $data['fuel_type_uuid']
            );

            $balance = $balance
                + (float)($data['qty_in'] ?? 0)
                - (float)($data['qty_out'] ?? 0);

            $data['balance_after'] = $balance;
        }

        return FuelStockLedger::create($data);
    }
}
