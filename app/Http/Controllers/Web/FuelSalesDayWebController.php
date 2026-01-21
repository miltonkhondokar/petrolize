<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FuelSalesDay;
use App\Models\FuelSalesItem;
use App\Models\FuelStation;
use App\Models\FuelStationPrice;
use App\Models\FuelType;
use App\Services\StockLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class FuelSalesDayWebController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['fuel_station_uuid','sale_date','status','from','to']);

        $q = FuelSalesDay::with(['station','manager'])
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

        $days = $q->paginate(20);

        $stations = FuelStation::orderBy('name')->get(['uuid','name']);


        $breadcrumb = [
            "page_header" => "Audit Logs",
            "first_item_name" => "Users",
            "first_item_link" => route('audit.user.index'),
            "first_item_icon" => "fa-user-shield",
            "second_item_name" => "Activity Log",
            "second_item_link" => "#",
            "second_item_icon" => "fa-list-check",
        ];


        return view('application.pages.fuel_sales_days.index', compact('days', 'filters', 'stations', 'breadcrumb'));
    }

    public function create()
    {
        $stations  = FuelStation::orderBy('name')->get(['uuid','name']);
        $fuelTypes = FuelType::where('is_active', true)->orderBy('name')->get(['uuid','name']);


        $breadcrumb = [
            "page_header" => "Audit Logs",
            "first_item_name" => "Users",
            "first_item_link" => route('audit.user.index'),
            "first_item_icon" => "fa-user-shield",
            "second_item_name" => "Activity Log",
            "second_item_link" => "#",
            "second_item_icon" => "fa-list-check",
        ];


        return view('application.pages.fuel_sales_days.create', compact('stations', 'fuelTypes', 'breadcrumb'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fuel_station_uuid' => 'required|uuid',
            'sale_date'         => 'required|date',
            'note'              => 'nullable|string',

            'items'             => 'required|array|min:1',
            'items.*.fuel_type_uuid'  => 'required|uuid',
            'items.*.nozzle_number'   => 'nullable|integer|min:1',
            'items.*.opening_reading' => 'required|numeric|min:0',
            'items.*.closing_reading' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request) {

                // station+date unique (your migration has unique)
                // handle friendly message
                $exists = FuelSalesDay::where('fuel_station_uuid', $request->fuel_station_uuid)
                    ->where('sale_date', $request->sale_date)
                    ->exists();
                if ($exists) {
                    abort(422, 'Sales day already exists for this station and date.');
                }

                $day = FuelSalesDay::create([
                    'fuel_station_uuid' => $request->fuel_station_uuid,
                    'user_uuid'         => auth()->user()->uuid ?? null,
                    'sale_date'         => $request->sale_date,
                    'status'            => 'draft',
                    'cash_amount'       => 0,
                    'bank_amount'       => 0,
                    'total_amount'      => 0,
                    'note'              => $request->note,
                ]);

                foreach ($request->items as $it) {

                    $price = FuelStationPrice::where('fuel_station_uuid', $request->fuel_station_uuid)
                        ->where('fuel_type_uuid', $it['fuel_type_uuid'])
                        ->where('is_active', true)
                        ->latest('created_at')
                        ->value('price_per_unit') ?? 0;

                    FuelSalesItem::create([
                        'fuel_sales_day_uuid' => $day->uuid,
                        'fuel_type_uuid'      => $it['fuel_type_uuid'],
                        'nozzle_number'       => $it['nozzle_number'] ?? null,
                        'opening_reading'     => $it['opening_reading'],
                        'closing_reading'     => $it['closing_reading'],
                        'price_per_unit'      => $price,
                        'is_active'           => true,
                    ]);
                    // sold_qty + line_total auto computed in model saving()
                }

                $day->refresh();
                $total = (float) $day->items()->sum('line_total');
                $day->update(['total_amount' => $total]);
            });

            Alert::success('Success', 'Sales day created (Draft).');
            return redirect()->route('fuel_sales_days.index');

        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(string $uuid)
    {
        $day = FuelSalesDay::where('uuid', $uuid)
            ->with(['station','manager','items.fuelType'])
            ->firstOrFail();


        $breadcrumb = [
            "page_header" => "Audit Logs",
            "first_item_name" => "Users",
            "first_item_link" => route('audit.user.index'),
            "first_item_icon" => "fa-user-shield",
            "second_item_name" => "Activity Log",
            "second_item_link" => "#",
            "second_item_icon" => "fa-list-check",
        ];


        return view('application.pages.fuel_sales_days.show', compact('day', 'breadcrumb'));
    }

    public function edit(string $uuid)
    {
        $day = FuelSalesDay::where('uuid', $uuid)
            ->with(['items.fuelType','station'])
            ->firstOrFail();

        if ($day->status !== 'draft') {
            abort(403, 'Only draft sales day can be edited.');
        }

        $stations  = FuelStation::orderBy('name')->get(['uuid','name']);
        $fuelTypes = FuelType::where('is_active', true)->orderBy('name')->get(['uuid','name']);


        $breadcrumb = [
            "page_header" => "Audit Logs",
            "first_item_name" => "Users",
            "first_item_link" => route('audit.user.index'),
            "first_item_icon" => "fa-user-shield",
            "second_item_name" => "Activity Log",
            "second_item_link" => "#",
            "second_item_icon" => "fa-list-check",
        ];


        return view('application.pages.fuel_sales_days.edit', compact('day', 'stations', 'fuelTypes', 'breadcrumb'));
    }

    public function update(Request $request, string $uuid)
    {
        $validator = Validator::make($request->all(), [
            'fuel_station_uuid' => 'required|uuid',
            'sale_date'         => 'required|date',
            'note'              => 'nullable|string',

            'items'             => 'required|array|min:1',
            'items.*.item_uuid'       => 'nullable|uuid', // existing row
            'items.*.fuel_type_uuid'  => 'required|uuid',
            'items.*.nozzle_number'   => 'nullable|integer|min:1',
            'items.*.opening_reading' => 'required|numeric|min:0',
            'items.*.closing_reading' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request, $uuid) {

            $day = FuelSalesDay::where('uuid', $uuid)
                ->with('items')
                ->lockForUpdate()
                ->firstOrFail();

            if ($day->status !== 'draft') {
                abort(403, 'Only draft sales day can be edited.');
            }

            // protect unique (station+date)
            $exists = FuelSalesDay::where('fuel_station_uuid', $request->fuel_station_uuid)
                ->where('sale_date', $request->sale_date)
                ->where('uuid', '!=', $day->uuid)
                ->exists();
            if ($exists) {
                abort(422, 'Another sales day already exists for this station and date.');
            }

            $day->update([
                'fuel_station_uuid' => $request->fuel_station_uuid,
                'sale_date'         => $request->sale_date,
                'note'              => $request->note,
            ]);

            // simplest: delete & reinsert (safe because draft only)
            FuelSalesItem::where('fuel_sales_day_uuid', $day->uuid)->delete();

            foreach ($request->items as $it) {

                $price = FuelStationPrice::where('fuel_station_uuid', $request->fuel_station_uuid)
                    ->where('fuel_type_uuid', $it['fuel_type_uuid'])
                    ->where('is_active', true)
                    ->latest('created_at')
                    ->value('price_per_unit') ?? 0;

                FuelSalesItem::create([
                    'fuel_sales_day_uuid' => $day->uuid,
                    'fuel_type_uuid'      => $it['fuel_type_uuid'],
                    'nozzle_number'       => $it['nozzle_number'] ?? null,
                    'opening_reading'     => $it['opening_reading'],
                    'closing_reading'     => $it['closing_reading'],
                    'price_per_unit'      => $price,
                    'is_active'           => true,
                ]);
            }

            $day->refresh();
            $total = (float) $day->items()->sum('line_total');
            $day->update(['total_amount' => $total]);
        });

        Alert::success('Success', 'Sales day updated.');
        return redirect()->route('fuel_sales_days.show', $uuid);
    }

    // Submit from web (same as API submit)
    public function submit(Request $request, string $uuid)
    {
        $validator = Validator::make($request->all(), [
            'cash_amount' => 'required|numeric|min:0',
            'bank_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request, $uuid) {

            $day = FuelSalesDay::where('uuid', $uuid)
                ->with(['items.fuelType'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($day->status !== 'draft') {
                abort(422, 'Only draft can be submitted.');
            }

            $total = (float)$day->items->sum('line_total');
            $cash  = (float)$request->cash_amount;
            $bank  = (float)$request->bank_amount;

            if (abs(($cash + $bank) - $total) > 0.01) {
                abort(422, "Cash + Bank must equal total. total={$total}, got=" . ($cash + $bank));
            }

            foreach ($day->items as $it) {
                $sold = (float)$it->sold_qty;
                if ($sold <= 0) {
                    continue;
                }

                $balance = StockLedgerService::getBalance($day->fuel_station_uuid, $it->fuel_type_uuid);
                if ($balance + 0.0001 < $sold) {
                    abort(422, "Insufficient stock for fuel type {$it->fuel_type_uuid}. balance={$balance}, sold={$sold}");
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
                    'note'              => 'Day-end sale submission (Web)',
                ]);
            }

            $day->update([
                'cash_amount'  => $cash,
                'bank_amount'  => $bank,
                'total_amount' => $total,
                'status'       => 'submitted',
            ]);
        });

        Alert::success('Success', 'Sales day submitted and stock ledger updated.');
        return back();
    }
}
