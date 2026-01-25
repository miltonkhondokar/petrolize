<?php

namespace App\Http\Controllers\App\Fuel;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FuelPurchaseItem;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FuelSalesDayController extends Controller
{
    // =========================================================
    // INDEX
    // =========================================================
    public function index(Request $request)
    {
        $filters = $request->only(['fuel_station_uuid', 'sale_date', 'status', 'from', 'to']);

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

        $days = $q->paginate(20);
        $stations = FuelStation::orderBy('name')->get(['uuid', 'name']);

        $breadcrumb = [
            "page_header" => "Audit Logs",
            "first_item_name" => "Users",
            "first_item_link" => route('audit.user.index'),
            "first_item_icon" => "fa-user-shield",
            "second_item_name" => "Activity Log",
            "second_item_link" => "#",
            "second_item_icon" => "fa-list-check",
        ];

        return view('application.pages.app.fuel_sales_days.index', compact('days', 'filters', 'stations', 'breadcrumb'));
    }

    // =========================================================
    // CREATE
    // =========================================================
    public function create()
    {
        $stations  = FuelStation::orderBy('name')->get(['uuid', 'name']);

        $fuelTypes = FuelType::where('is_active', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['uuid', 'name']);

        $breadcrumb = [
            "page_header" => "Fuel Sales",
            "first_item_name" => "Fuel Sales Days",
            "first_item_link" => route('fuel_sales_days.index'),
            "first_item_icon" => "fa-gas-pump",
            "second_item_name" => "Create Sales Day",
            "second_item_link" => "#",
            "second_item_icon" => "fa-plus",
        ];

        return view('application.pages.app.fuel_sales_days.create', compact('stations', 'fuelTypes', 'breadcrumb'));
    }

    // =========================================================
    // STORE (DRAFT)
    // =========================================================
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fuel_station_uuid'       => 'required|uuid',
            'sale_date'               => 'required|date',
            'note'                    => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.fuel_type_uuid'  => 'required|uuid',
            'items.*.nozzle_number'   => 'nullable|integer|min:1',
            'items.*.opening_reading' => 'required|numeric|min:0',
            'items.*.closing_reading' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request) {

            $day = FuelSalesDay::create([
                'uuid'              => Str::uuid(),
                'fuel_station_uuid' => $request->fuel_station_uuid,
                'sale_date'         => $request->sale_date,
                'note'              => $request->note,
                'status'            => 'draft',
                'user_uuid'         => Auth::user()->uuid,
            ]);

            foreach ($request->items as $row) {

                $sold_qty = $row['closing_reading'] - $row['opening_reading'];
                if ($sold_qty <= 0) {
                    abort(422, 'Closing reading must be greater than opening reading.');
                }

                $price = FuelStationPrice::where('fuel_station_uuid', $day->fuel_station_uuid)
                    ->where('fuel_type_uuid', $row['fuel_type_uuid'])
                    ->where('is_active', true)
                    ->latest('created_at')
                    ->value('price_per_unit') ?? 0;

                $line_total = $sold_qty * $price;

                FuelSalesItem::create([
                    'fuel_sales_day_uuid' => $day->uuid,
                    'fuel_type_uuid'      => $row['fuel_type_uuid'],
                    'nozzle_number'       => $row['nozzle_number'],
                    'opening_reading'     => $row['opening_reading'],
                    'closing_reading'     => $row['closing_reading'],
                    'sold_qty'            => $sold_qty,
                    'price_per_unit'      => $price,
                    'line_total'          => $line_total,
                    'is_active'           => true,
                ]);
            }

            $day->update([
                'total_amount' => $day->items()->sum('line_total'),
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => "Created fuel sales day {$day->uuid}",
                'type'       => 'fuel_sales_day_create',
                'item_id'    => $day->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        Alert::success('Success', 'Fuel sales day created as draft.');
        return redirect()->route('fuel_sales_days.index');
    }

    // =========================================================
    // SHOW
    // =========================================================
    public function show(string $uuid)
    {
        $day = FuelSalesDay::where('uuid', $uuid)
            ->with(['station', 'manager', 'items.fuelType'])
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

        return view('application.pages.app.fuel_sales_days.show', compact('day', 'breadcrumb'));
    }

    // =========================================================
    // EDIT
    // =========================================================
    public function edit(string $uuid)
    {
        $day = FuelSalesDay::where('uuid', $uuid)
            ->with(['items.fuelType', 'station'])
            ->firstOrFail();

        if ($day->status !== 'draft') {
            abort(403, 'Only draft sales day can be edited.');
        }

        $stations  = FuelStation::orderBy('name')->get(['uuid', 'name']);

        $fuelTypes = FuelType::where('is_active', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['uuid', 'name']);


        $breadcrumb = [
            "page_header" => "Fuel Sales",
            "first_item_name" => "Fuel Sales Days",
            "first_item_link" => route('fuel_sales_days.index'),
            "first_item_icon" => "fa-gas-pump",
            "second_item_name" => "Edit Sales Day",
            "second_item_link" => "#",
            "second_item_icon" => "fa-edit",
        ];

        return view('application.pages.app.fuel_sales_days.edit', compact('day', 'stations', 'fuelTypes', 'breadcrumb'));
    }

    // =========================================================
    // UPDATE (DRAFT ONLY)
    // =========================================================
    public function update(Request $request, string $uuid)
    {
        $validator = Validator::make($request->all(), [
            'fuel_station_uuid'       => 'required|uuid',
            'sale_date'               => 'required|date',
            'note'                    => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.fuel_type_uuid'  => 'required|uuid',
            'items.*.opening_reading' => 'required|numeric|min:0',
            'items.*.closing_reading' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request, $uuid) {

            $day = FuelSalesDay::where('uuid', $uuid)->lockForUpdate()->firstOrFail();

            if ($day->status !== 'draft') {
                abort(403, 'Only draft sales day can be edited.');
            }

            $day->update([
                'fuel_station_uuid' => $request->fuel_station_uuid,
                'sale_date'         => $request->sale_date,
                'note'              => $request->note,
            ]);

            FuelSalesItem::where('fuel_sales_day_uuid', $day->uuid)->delete();

            foreach ($request->items as $row) {

                $sold_qty = $row['closing_reading'] - $row['opening_reading'];
                if ($sold_qty <= 0) {
                    abort(422, 'Closing reading must be greater than opening reading.');
                }

                $price = FuelStationPrice::where('fuel_station_uuid', $day->fuel_station_uuid)
                    ->where('fuel_type_uuid', $row['fuel_type_uuid'])
                    ->where('is_active', true)
                    ->latest('created_at')
                    ->value('price_per_unit') ?? 0;

                $line_total = $sold_qty * $price;

                FuelSalesItem::create([
                    'fuel_sales_day_uuid' => $day->uuid,
                    'fuel_type_uuid'      => $row['fuel_type_uuid'],
                    'nozzle_number'       => $row['nozzle_number'],
                    'opening_reading'     => $row['opening_reading'],
                    'closing_reading'     => $row['closing_reading'],
                    'sold_qty'            => $sold_qty,
                    'price_per_unit'      => $price,
                    'line_total'          => $line_total,
                    'is_active'           => true,
                ]);
            }

            $day->update([
                'total_amount' => $day->items()->sum('line_total'),
            ]);
        });

        Alert::success('Success', 'Sales day updated.');
        return redirect()->route('fuel_sales_days.show', $uuid);
    }

    // =========================================================
    // SUBMIT (FINAL + STOCK OUT)
    // =========================================================
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
                ->with('items.fuelType')
                ->lockForUpdate()
                ->firstOrFail();

            if ($day->status !== 'draft') {
                abort(422, 'Only draft can be submitted.');
            }

            $total = (float) $day->items->sum('line_total');
            if (abs(($request->cash_amount + $request->bank_amount) - $total) > 0.01) {
                abort(422, 'Cash + Bank must equal total amount.');
            }

            foreach ($day->items as $it) {

                $balance = StockLedgerService::getBalance(
                    $day->fuel_station_uuid,
                    $it->fuel_type_uuid
                );

                if ($balance < $it->sold_qty) {
                    abort(422, 'Insufficient stock for ' . $it->fuelType->name);
                }

                StockLedgerService::add([
                    'fuel_station_uuid' => $day->fuel_station_uuid,
                    'fuel_type_uuid'    => $it->fuel_type_uuid,
                    'fuel_unit_uuid'    => $it->fuelType->fuel_unit_uuid,
                    'txn_type'          => 'sale',
                    'txn_date'          => $day->sale_date,
                    'qty_in'            => 0,
                    'qty_out'           => $it->sold_qty,
                    'reference_type'    => 'fuel_sales_day',
                    'reference_uuid'    => $day->uuid,
                    'note'              => 'Day-end sale submission',
                ]);
            }

            $day->update([
                'cash_amount'  => $request->cash_amount,
                'bank_amount'  => $request->bank_amount,
                'total_amount' => $total,
                'status'       => 'submitted',
            ]);
        });

        Alert::success('Success', 'Sales day submitted and stock updated.');
        return back();
    }

    public function getFuelPrices(FuelStation $station)
    {
        // Get all active fuel types
        $fuelTypes = DB::table('fuel_types')
            ->where('is_active', true)
            ->get();

        $prices = [];

        foreach ($fuelTypes as $ft) {
            $price = DB::table('fuel_purchase_items as fpi')
                ->join('fuel_purchases as fp', 'fpi.fuel_purchase_uuid', '=', 'fp.uuid')
                ->where('fp.fuel_station_uuid', $station->uuid)
                ->where('fp.status', 'received')           // only consider received purchases
                ->where('fpi.fuel_type_uuid', $ft->uuid)
                ->where('fpi.is_active', true)
                ->orderByDesc('fpi.created_at')
                ->value('fpi.unit_price') ?? 0;

            $prices[$ft->uuid] = (float) $price;
        }

        return response()->json($prices);
    }
}
