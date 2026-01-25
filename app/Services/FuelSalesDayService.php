<?php

namespace App\Services;

use App\Models\FuelSalesDay;
use App\Models\FuelSalesItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FuelSalesDayService
{
    // =========================================================
    // PRICE SOURCE (Single Truth)
    // Latest received purchase price for station+fuel
    // =========================================================
    public function latestPurchasePrice(string $stationUuid, string $fuelTypeUuid): float
    {
        return (float) (DB::table('fuel_purchase_items as fpi')
            ->join('fuel_purchases as fp', 'fpi.fuel_purchase_uuid', '=', 'fp.uuid')
            ->where('fp.fuel_station_uuid', $stationUuid)
            ->where('fp.status', 'received')           // only consider received purchases
            ->where('fpi.fuel_type_uuid', $fuelTypeUuid)
            ->where('fpi.is_active', true)
            ->orderByDesc('fpi.created_at')
            ->value('fpi.unit_price')) ?? 0;
    }

    // =========================================================
    // LIST (API)
    // =========================================================
    public function paginate(array $filters, int $perPage = 20)
    {
        $q = FuelSalesDay::with(['station', 'manager'])
            ->withCount('items')
            ->latest();

        if (!empty($filters['fuel_station_uuid'])) {
            $q->where('fuel_station_uuid', $filters['fuel_station_uuid']);
        }
        if (!empty($filters['sale_date'])) {
            $q->where('sale_date', $filters['sale_date']);
        }
        if (!empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        if (!empty($filters['from'])) {
            $q->whereDate('sale_date', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $q->whereDate('sale_date', '<=', $filters['to']);
        }

        return $q->paginate($perPage);
    }

    // =========================================================
    // SHOW
    // =========================================================
    public function findOrFail(string $uuid): FuelSalesDay
    {
        return FuelSalesDay::where('uuid', $uuid)
            ->with(['station', 'manager', 'items.fuelType'])
            ->firstOrFail();
    }

    // =========================================================
    // CREATE DRAFT
    // =========================================================
    public function createDraft(array $payload): FuelSalesDay
    {
        return DB::transaction(function () use ($payload) {

            $day = FuelSalesDay::create([
                'uuid'              => (string) Str::uuid(),
                'fuel_station_uuid' => $payload['fuel_station_uuid'],
                'sale_date'         => $payload['sale_date'],
                'note'              => $payload['note'] ?? null,
                'status'            => 'draft',
                'user_uuid'         => Auth::user()->uuid,
            ]);

            foreach ($payload['items'] as $row) {
                $opening = (float) ($row['opening_reading'] ?? 0);
                $closing = (float) ($row['closing_reading'] ?? 0);

                $soldQty = $closing - $opening;
                if ($soldQty <= 0) {
                    abort(422, 'Closing reading must be greater than opening reading.');
                }

                $price = $this->latestPurchasePrice($day->fuel_station_uuid, $row['fuel_type_uuid']);
                $lineTotal = $soldQty * $price;

                FuelSalesItem::create([
                    'uuid'                => (string) Str::uuid(),
                    'fuel_sales_day_uuid' => $day->uuid,
                    'fuel_type_uuid'      => $row['fuel_type_uuid'],
                    'nozzle_number'       => $row['nozzle_number'] ?? null,
                    'opening_reading'     => $opening,
                    'closing_reading'     => $closing,
                    'sold_qty'            => $soldQty,
                    'price_per_unit'      => $price,
                    'line_total'          => $lineTotal,
                    'is_active'           => true,
                ]);
            }

            $day->update([
                'total_amount' => (float) $day->items()->sum('line_total'),
            ]);

            // Reload relationships for API output
            return $day->fresh()->load(['station', 'manager', 'items.fuelType']);
        });
    }

    // =========================================================
    // UPDATE DRAFT ONLY (delete & recreate items like your web logic)
    // =========================================================
    public function updateDraft(string $uuid, array $payload): FuelSalesDay
    {
        return DB::transaction(function () use ($uuid, $payload) {

            $day = FuelSalesDay::where('uuid', $uuid)->lockForUpdate()->firstOrFail();

            if ($day->status !== 'draft') {
                abort(403, 'Only draft sales day can be edited.');
            }

            $day->update([
                'fuel_station_uuid' => $payload['fuel_station_uuid'],
                'sale_date'         => $payload['sale_date'],
                'note'              => $payload['note'] ?? null,
            ]);

            FuelSalesItem::where('fuel_sales_day_uuid', $day->uuid)->delete();

            foreach ($payload['items'] as $row) {
                $opening = (float) ($row['opening_reading'] ?? 0);
                $closing = (float) ($row['closing_reading'] ?? 0);

                $soldQty = $closing - $opening;
                if ($soldQty <= 0) {
                    abort(422, 'Closing reading must be greater than opening reading.');
                }

                $price = $this->latestPurchasePrice($day->fuel_station_uuid, $row['fuel_type_uuid']);
                $lineTotal = $soldQty * $price;

                FuelSalesItem::create([
                    'uuid'                => (string) Str::uuid(),
                    'fuel_sales_day_uuid' => $day->uuid,
                    'fuel_type_uuid'      => $row['fuel_type_uuid'],
                    'nozzle_number'       => $row['nozzle_number'] ?? null,
                    'opening_reading'     => $opening,
                    'closing_reading'     => $closing,
                    'sold_qty'            => $soldQty,
                    'price_per_unit'      => $price,
                    'line_total'          => $lineTotal,
                    'is_active'           => true,
                ]);
            }

            $day->update([
                'total_amount' => (float) $day->items()->sum('line_total'),
            ]);

            return $day->fresh()->load(['station', 'manager', 'items.fuelType']);
        });
    }

    // =========================================================
    // SUBMIT (final + stock out)
    // =========================================================
    public function submit(string $uuid, float $cashAmount, float $bankAmount): FuelSalesDay
    {
        return DB::transaction(function () use ($uuid, $cashAmount, $bankAmount) {

            $day = FuelSalesDay::where('uuid', $uuid)
                ->with('items.fuelType')
                ->lockForUpdate()
                ->firstOrFail();

            if ($day->status !== 'draft') {
                abort(422, 'Only draft can be submitted.');
            }

            $total = (float) $day->items->sum('line_total');

            if (abs(($cashAmount + $bankAmount) - $total) > 0.01) {
                abort(422, 'Cash + Bank must equal total amount.');
            }

            foreach ($day->items as $it) {
                $balance = StockLedgerService::getBalance(
                    $day->fuel_station_uuid,
                    $it->fuel_type_uuid
                );

                if ($balance < (float)$it->sold_qty) {
                    abort(422, 'Insufficient stock for ' . ($it->fuelType->name ?? 'Fuel'));
                }

                StockLedgerService::add([
                    'fuel_station_uuid' => $day->fuel_station_uuid,
                    'fuel_type_uuid'    => $it->fuel_type_uuid,
                    'fuel_unit_uuid'    => $it->fuelType->fuel_unit_uuid,
                    'txn_type'          => 'sale',
                    'txn_date'          => $day->sale_date,
                    'qty_in'            => 0,
                    'qty_out'           => (float)$it->sold_qty,
                    'reference_type'    => 'fuel_sales_day',
                    'reference_uuid'    => $day->uuid,
                    'note'              => 'Day-end sale submission',
                ]);
            }

            $day->update([
                'cash_amount'  => $cashAmount,
                'bank_amount'  => $bankAmount,
                'total_amount' => $total,
                'status'       => 'submitted',
            ]);

            return $day->fresh()->load(['station', 'manager', 'items.fuelType']);
        });
    }

    // =========================================================
    // Fuel prices for a station (AJAX equivalent for mobile)
    // =========================================================
    public function stationFuelPrices(string $stationUuid): array
    {
        $fuelTypes = DB::table('fuel_types')->where('is_active', true)->get();

        $prices = [];
        foreach ($fuelTypes as $ft) {
            $prices[$ft->uuid] = $this->latestPurchasePrice($stationUuid, $ft->uuid);
        }
        return $prices;
    }
}
