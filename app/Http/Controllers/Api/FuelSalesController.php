<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FuelSalesDay;
use App\Models\FuelSalesItem;
use App\Models\FuelStationPrice;
use App\Services\StockLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FuelSalesController extends Controller
{
    public function index(Request $request)
    {
        $q = FuelSalesDay::query()->with(['station','manager','items.fuelType']);

        if ($request->filled('fuel_station_uuid')) {
            $q->where('fuel_station_uuid', $request->fuel_station_uuid);
        }
        if ($request->filled('sale_date')) {
            $q->where('sale_date', $request->sale_date);
        }
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        return response()->json(['success' => true,'data' => $q->latest()->paginate(20)]);
    }

    // Create day + items (manager entry)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fuel_station_uuid' => 'required|uuid',
            'user_uuid'         => 'nullable|uuid',
            'sale_date'         => 'required|date',
            'note'              => 'nullable|string',

            'items'             => 'required|array|min:1',
            'items.*.fuel_type_uuid'    => 'required|uuid',
            'items.*.nozzle_number'     => 'nullable|integer|min:1',
            'items.*.opening_reading'   => 'required|numeric|min:0',
            'items.*.closing_reading'   => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false,'errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request) {

            $day = FuelSalesDay::create([
                'fuel_station_uuid' => $request->fuel_station_uuid,
                'user_uuid'         => $request->user_uuid,
                'sale_date'         => $request->sale_date,
                'status'            => 'draft',
                'cash_amount'       => 0,
                'bank_amount'       => 0,
                'total_amount'      => 0,
                'note'              => $request->note,
            ]);

            $total = 0;

            foreach ($request->items as $it) {
                // price snapshot: latest active price for station+fueltype
                $price = FuelStationPrice::where('fuel_station_uuid', $request->fuel_station_uuid)
                    ->where('fuel_type_uuid', $it['fuel_type_uuid'])
                    ->where('is_active', true)
                    ->latest('created_at')
                    ->value('price_per_unit') ?? 0;

                $item = FuelSalesItem::create([
                    'fuel_sales_day_uuid' => $day->uuid,
                    'fuel_type_uuid'      => $it['fuel_type_uuid'],
                    'nozzle_number'       => $it['nozzle_number'] ?? null,
                    'opening_reading'     => $it['opening_reading'],
                    'closing_reading'     => $it['closing_reading'],
                    'price_per_unit'      => $price,
                    'is_active'           => true,
                ]);

                $total += (float) $item->line_total;
            }

            $day->update(['total_amount' => $total]);

            return response()->json(['success' => true,'data' => $day->load('items')], 201);
        });
    }

    // Submit day: manager enters cash/bank and stock is deducted
    public function submit(Request $request, string $salesDayUuid)
    {
        $validator = Validator::make($request->all(), [
            'cash_amount' => 'required|numeric|min:0',
            'bank_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false,'errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request, $salesDayUuid) {

            $day = FuelSalesDay::where('uuid', $salesDayUuid)
                ->with('items')
                ->lockForUpdate()
                ->firstOrFail();

            if ($day->status !== 'draft') {
                return response()->json(['success' => false,'message' => 'Only draft can be submitted'], 422);
            }

            $total = (float)$day->items->sum('line_total');
            $cash  = (float)$request->cash_amount;
            $bank  = (float)$request->bank_amount;

            // strict validation (you can relax later)
            if (abs(($cash + $bank) - $total) > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => "Cash + Bank must equal total. total={$total}, got=" . ($cash + $bank)
                ], 422);
            }

            // Deduct stock for each item
            foreach ($day->items as $it) {
                // Validate stock is enough
                $balance = StockLedgerService::getBalance($day->fuel_station_uuid, $it->fuel_type_uuid);
                if ($balance + 0.0001 < (float)$it->sold_qty) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for fuel_type={$it->fuel_type_uuid}. balance={$balance}, sold={$it->sold_qty}"
                    ], 422);
                }

                // Deduct from ledger: qty_out
                // Unit: get fuel_type default unit (optional). For now use station fuel type default unit if exists.
                // If you want strict unit mapping, enforce in request.
                $fuelUnitUuid = optional($it->fuelType)->fuel_unit_uuid; // may be null if relation not loaded
                $fuelUnitUuid = $fuelUnitUuid ?: \App\Models\FuelType::where('uuid', $it->fuel_type_uuid)->value('fuel_unit_uuid');

                StockLedgerService::add([
                    'fuel_station_uuid' => $day->fuel_station_uuid,
                    'fuel_type_uuid'    => $it->fuel_type_uuid,
                    'fuel_unit_uuid'    => $fuelUnitUuid,
                    'txn_type'          => 'sale',
                    'ref_uuid'          => $day->uuid,
                    'txn_date'          => $day->sale_date,
                    'qty_in'            => 0,
                    'qty_out'           => (float)$it->sold_qty,
                    'note'              => 'Day-end sale submission',
                ]);
            }

            $day->update([
                'cash_amount'  => $cash,
                'bank_amount'  => $bank,
                'total_amount' => $total,
                'status'       => 'submitted',
            ]);

            return response()->json(['success' => true,'data' => $day->fresh()->load('items')]);
        });
    }

    public function show(string $salesDayUuid)
    {
        $day = FuelSalesDay::where('uuid', $salesDayUuid)
            ->with(['station','manager','items.fuelType'])
            ->firstOrFail();

        return response()->json(['success' => true,'data' => $day]);
    }
}