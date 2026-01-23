<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FuelSalesDay;
use App\Models\FuelSalesItem;
use App\Models\FuelStationPrice;
use App\Models\FuelType;
use App\Services\StockLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class FuelSalesController extends Controller
{
    public function index(Request $request)
    {
        $q = FuelSalesDay::with(['station', 'manager', 'items.fuelType']);

        if ($request->filled('fuel_station_uuid')) {
            $q->where('fuel_station_uuid', $request->fuel_station_uuid);
        }
        if ($request->filled('sale_date')) {
            $q->where('sale_date', $request->sale_date);
        }
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        return response()->json([
            'success' => true,
            'data' => $q->latest()->paginate(20)
        ]);
    }

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
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            return DB::transaction(function () use ($request) {

                // Duplicate check: station+date
                $exists = FuelSalesDay::where('fuel_station_uuid', $request->fuel_station_uuid)
                    ->where('sale_date', $request->sale_date)
                    ->exists();

                if ($exists) {
                    throw new \Exception('Sales day already exists for this station and date.');
                }

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
                    $price = FuelStationPrice::where('fuel_station_uuid', $request->fuel_station_uuid)
                        ->where('fuel_type_uuid', $it['fuel_type_uuid'])
                        ->where('is_active', true)
                        ->latest('created_at')
                        ->value('price_per_unit') ?? 0;

                    $sold_qty = $it['closing_reading'] - $it['opening_reading'];
                    $line_total = $sold_qty * $price;

                    FuelSalesItem::create([
                        'fuel_sales_day_uuid' => $day->uuid,
                        'fuel_type_uuid'      => $it['fuel_type_uuid'],
                        'nozzle_number'       => $it['nozzle_number'] ?? null,
                        'opening_reading'     => $it['opening_reading'],
                        'closing_reading'     => $it['closing_reading'],
                        'sold_qty'            => $sold_qty,
                        'price_per_unit'      => $price,
                        'line_total'          => $line_total,
                        'is_active'           => true,
                    ]);

                    $total += $line_total;
                }

                $day->update(['total_amount' => $total]);

                // Audit log
                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => "Created draft fuel sales day {$day->uuid}",
                    'type'       => 'fuel_sales_day',
                    'item_id'    => $day->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return response()->json(['success' => true, 'data' => $day->load('items')], 201);
            });
        } catch (\Throwable $e) {
            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => "Failed to create fuel sales day: " . $e->getMessage(),
                'type'       => 'fuel_sales_day_error',
                'item_id'    => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function submit(Request $request, string $salesDayUuid)
    {
        $validator = Validator::make($request->all(), [
            'cash_amount' => 'required|numeric|min:0',
            'bank_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            return DB::transaction(function () use ($request, $salesDayUuid) {

                $day = FuelSalesDay::where('uuid', $salesDayUuid)
                    ->with('items.fuelType')
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($day->status !== 'draft') {
                    throw new \Exception('Only draft can be submitted.');
                }

                $total = (float)$day->items->sum('line_total');
                $cash  = (float)$request->cash_amount;
                $bank  = (float)$request->bank_amount;

                if (abs(($cash + $bank) - $total) > 0.01) {
                    throw new \Exception("Cash + Bank must equal total. total={$total}, got=" . ($cash + $bank));
                }

                foreach ($day->items as $it) {
                    $sold = (float) $it->sold_qty;
                    if ($sold <= 0) continue;

                    $balance = StockLedgerService::getBalance($day->fuel_station_uuid, $it->fuel_type_uuid);
                    if ($balance + 0.0001 < $sold) {
                        throw new \Exception("Insufficient stock for fuel type {$it->fuel_type_uuid}. balance={$balance}, sold={$sold}");
                    }

                    $fuelUnitUuid = $it->fuelType->fuel_unit_uuid
                        ?? FuelType::where('uuid', $it->fuel_type_uuid)->value('fuel_unit_uuid');

                    StockLedgerService::add([
                        'fuel_station_uuid' => $day->fuel_station_uuid,
                        'fuel_type_uuid'    => $it->fuel_type_uuid,
                        'fuel_unit_uuid'    => $fuelUnitUuid,
                        'txn_type'          => 'sale',
                        'ref_uuid'          => $day->uuid,
                        'txn_date'          => $day->sale_date,
                        'qty_in'            => 0,
                        'qty_out'           => $sold,
                        'note'              => 'Day-end sale submission (API)',
                    ]);
                }

                $day->update([
                    'cash_amount'  => $cash,
                    'bank_amount'  => $bank,
                    'total_amount' => $total,
                    'status'       => 'submitted',
                ]);

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => "Submitted fuel sales day {$day->uuid}",
                    'type'       => 'fuel_sales_day',
                    'item_id'    => $day->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return response()->json(['success' => true, 'data' => $day->fresh()->load('items')]);
            });
        } catch (\Throwable $e) {
            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => "Failed to submit fuel sales day {$salesDayUuid}: " . $e->getMessage(),
                'type'       => 'fuel_sales_day_error',
                'item_id'    => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function show(string $salesDayUuid)
    {
        $day = FuelSalesDay::where('uuid', $salesDayUuid)
            ->with(['station', 'manager', 'items.fuelType'])
            ->firstOrFail();

        return response()->json(['success' => true, 'data' => $day]);
    }
}
