<?php

namespace App\Services;

use App\Models\FuelStockLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockLedgerService
{
    /**
     * Get current balance for station + fuel
     */
    public static function getBalance(string $stationUuid, string $fuelTypeUuid): float
    {
        return (float) FuelStockLedger::where('fuel_station_uuid', $stationUuid)
            ->where('fuel_type_uuid', $fuelTypeUuid)
            ->latest('id')
            ->value('balance_after') ?? 0;
    }

    /**
     * -----------------------------
     * STOCK IN (Fuel Purchase)
     * -----------------------------
     * USED BY:
     * - Fuel Purchase Receive
     */
    public static function in(array $data): FuelStockLedger
    {
        return DB::transaction(function () use ($data) {

            $qty = (float) ($data['quantity'] ?? 0);
            if ($qty <= 0) {
                throw new \Exception('Quantity must be greater than zero.');
            }

            $balanceBefore = self::getBalance(
                $data['fuel_station_uuid'],
                $data['fuel_type_uuid']
            );

            $balanceAfter = $balanceBefore + $qty;

            return FuelStockLedger::create([
                'uuid'               => (string) Str::uuid(),
                'fuel_station_uuid'  => $data['fuel_station_uuid'],
                'fuel_type_uuid'     => $data['fuel_type_uuid'],
                'fuel_unit_uuid'     => $data['fuel_unit_uuid'],
                'txn_type'           => 'purchase',
                'txn_date'           => $data['date'] ?? now(),
                'qty_in'             => $qty,
                'qty_out'            => 0,
                'balance_after'      => $balanceAfter,
                'reference_type'     => $data['reference_type'] ?? null,
                'reference_uuid'     => $data['reference_uuid'] ?? null,
                'note'               => $data['note'] ?? null,
            ]);
        });
    }

    /**
     * ---------------------------------
     * GENERIC LEDGER ADD (IN / OUT)
     * ---------------------------------
     * USED BY:
     * - Sales submit
     * - Adjustments
     * - Any future transaction
     */
    public static function add(array $data): FuelStockLedger
    {
        return DB::transaction(function () use ($data) {

            $qtyIn  = (float) ($data['qty_in']  ?? 0);
            $qtyOut = (float) ($data['qty_out'] ?? 0);

            if ($qtyIn <= 0 && $qtyOut <= 0) {
                throw new \Exception('Either qty_in or qty_out must be greater than zero.');
            }

            $balanceBefore = self::getBalance(
                $data['fuel_station_uuid'],
                $data['fuel_type_uuid']
            );

            $balanceAfter = $balanceBefore + $qtyIn - $qtyOut;

            if ($balanceAfter < 0) {
                throw new \Exception('Insufficient stock. Operation would result in negative balance.');
            }

            return FuelStockLedger::create([
                'uuid'               => $data['uuid'] ?? (string) Str::uuid(),
                'fuel_station_uuid'  => $data['fuel_station_uuid'],
                'fuel_type_uuid'     => $data['fuel_type_uuid'],
                'fuel_unit_uuid'     => $data['fuel_unit_uuid'],
                'txn_type'           => $data['txn_type'],     // sale | purchase | adjustment
                'txn_date'           => $data['txn_date'] ?? now(),
                'qty_in'             => $qtyIn,
                'qty_out'            => $qtyOut,
                'balance_after'      => $balanceAfter,
                'reference_type'     => $data['reference_type'] ?? null,
                'reference_uuid'     => $data['reference_uuid'] ?? null,
                'note'               => $data['note'] ?? null,
            ]);
        });
    }
}
