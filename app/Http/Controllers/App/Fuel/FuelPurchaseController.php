<?php

namespace App\Http\Controllers\App\Fuel;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FuelPurchase;
use App\Models\FuelPurchaseItem;
use App\Models\FuelStation;
use App\Models\FuelType;
use App\Models\FuelUnit;
use App\Models\Vendor;
use App\Services\StockLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;

class FuelPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['fuel_station_uuid', 'vendor_uuid', 'status', 'from', 'to']);

        $q = FuelPurchase::with(['vendor', 'station'])->latest();

        if (!empty($filters['fuel_station_uuid'])) {
            $q->where('fuel_station_uuid', $filters['fuel_station_uuid']);
        }
        if (!empty($filters['vendor_uuid'])) {
            $q->where('vendor_uuid', $filters['vendor_uuid']);
        }
        if (!empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        if (!empty($filters['from'])) {
            $q->whereDate('purchase_date', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $q->whereDate('purchase_date', '<=', $filters['to']);
        }

        $purchases = $q->paginate(20);

        $stations = FuelStation::orderBy('name')->get(['uuid', 'name']);
        $vendors  = Vendor::orderBy('name')->get(['uuid', 'name']);

        $breadcrumb = [
            "page_header" => "Fuel Purchases",
            "first_item_name" => "Dashboard",
            "first_item_link" => route(('/')),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Purchases",
            "second_item_link" => "#",
            "second_item_icon" => "fa-boxes",
        ];

        return view('application.pages.app.fuel_purchases.index', compact('purchases', 'filters', 'stations', 'vendors', 'breadcrumb'));
    }

    public function create()
    {
        $stations  = FuelStation::orderBy('name')->get(['uuid', 'name']);
        $vendors   = Vendor::orderBy('name')->get(['uuid', 'name']);
        $fuelTypes = FuelType::where('is_active', true)->orderBy('name')->get(['uuid', 'name']);
        $fuelUnits = FuelUnit::where('is_active', true)->orderBy('name')->get(['uuid', 'name', 'abbreviation']);

        $breadcrumb = [
            "page_header" => "Fuel Purchases",
            "first_item_name" => "Dashboard",
            "first_item_link" => route(('/')),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Purchases",
            "second_item_link" => "#",
            "second_item_icon" => "fa-boxes",
        ];

        return view('application.pages.app.fuel_purchases.create', compact('stations', 'vendors', 'fuelTypes', 'fuelUnits', 'breadcrumb'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fuel_station_uuid' => 'required|uuid',
            'vendor_uuid'       => 'required|uuid',
            'purchase_date'     => 'required|date',
            'invoice_no'        => 'nullable|string|max:255',
            'transport_by'      => 'nullable|in:vendor,owner',
            'truck_no'          => 'nullable|string|max:255',
            'note'              => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.fuel_type_uuid' => 'required|uuid',
            'items.*.fuel_unit_uuid' => 'required|uuid',
            'items.*.quantity'       => 'required|numeric|min:0.001',
            'items.*.unit_price'     => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, &$purchase) {

                $purchase = FuelPurchase::create([
                    'fuel_station_uuid' => $request->fuel_station_uuid,
                    'vendor_uuid'       => $request->vendor_uuid,
                    'purchase_date'     => $request->purchase_date,
                    'invoice_no'        => $request->invoice_no,
                    'transport_by'      => $request->transport_by ?? 'vendor',
                    'truck_no'          => $request->truck_no,
                    'status'            => 'draft',
                    'note'              => $request->note,
                    'total_amount'      => 0,
                ]);

                $total = 0.0;

                foreach ($request->items as $it) {
                    $qty   = (float)$it['quantity'];
                    $price = (float)$it['unit_price'];

                    FuelPurchaseItem::create([
                        'fuel_purchase_uuid' => $purchase->uuid,
                        'fuel_type_uuid'     => $it['fuel_type_uuid'],
                        'fuel_unit_uuid'     => $it['fuel_unit_uuid'],
                        'quantity'           => $qty,
                        'received_qty'       => 0,
                        'unit_price'         => $price,
                        'is_active'          => true,
                    ]);

                    $total += ($qty * $price);
                }

                $purchase->update(['total_amount' => $total]);

                // Audit log
                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => "Created Fuel Purchase {$purchase->uuid}",
                    'type'       => 'fuel_purchase',
                    'item_id'    => $purchase->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });

            Alert::success('Success', 'Fuel Purchase created successfully.');
            return redirect()->route('fuel_purchases.index');
        } catch (\Throwable $e) {
            // log error to audit
            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => "Failed to create Fuel Purchase: " . $e->getMessage(),
                'type'       => 'fuel_purchase_error',
                'item_id'    => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', $e->getMessage());
            return back()->withInput();
        }
    }

    public function show(string $uuid)
    {
        $purchase = FuelPurchase::where('uuid', $uuid)
            ->with(['vendor', 'station', 'items.fuelType', 'items.fuelUnit'])
            ->firstOrFail();

        $breadcrumb = [
            "page_header" => "Fuel Purchases",
            "first_item_name" => "Dashboard",
            "first_item_link" => route(('/')),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Purchases",
            "second_item_link" => "#",
            "second_item_icon" => "fa-boxes",
        ];

        return view('application.pages.app.fuel_purchases.show', compact('purchase', 'breadcrumb'));
    }

    public function edit(string $uuid)
    {
        $purchase = FuelPurchase::where('uuid', $uuid)
            ->with('items')
            ->firstOrFail();

        $stations  = FuelStation::orderBy('name')->get(['uuid', 'name']);
        $vendors   = Vendor::orderBy('name')->get(['uuid', 'name']);
        $fuelTypes = FuelType::where('is_active', true)->orderBy('name')->get(['uuid', 'name']);
        $fuelUnits = FuelUnit::where('is_active', true)->orderBy('name')->get(['uuid', 'name', 'abbreviation']);

        $breadcrumb = [
            "page_header" => "Fuel Purchases",
            "first_item_name" => "Dashboard",
            "first_item_link" => route(('/')),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Purchases",
            "second_item_link" => "#",
            "second_item_icon" => "fa-boxes",
        ];

        return view('application.pages.app.fuel_purchases.edit', compact(
            'purchase',
            'stations',
            'vendors',
            'fuelTypes',
            'fuelUnits',
            'breadcrumb'
        ));
    }

    public function update(Request $request, string $uuid)
    {
        $validator = Validator::make($request->all(), [
            'fuel_station_uuid' => 'required|uuid',
            'vendor_uuid'       => 'required|uuid',
            'purchase_date'     => 'required|date',
            'invoice_no'        => 'nullable|string|max:255',
            'transport_by'      => 'nullable|in:vendor,owner',
            'truck_no'          => 'nullable|string|max:255',
            'note'              => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.fuel_type_uuid' => 'required|uuid',
            'items.*.fuel_unit_uuid' => 'required|uuid',
            'items.*.quantity'       => 'required|numeric|min:0.001',
            'items.*.unit_price'     => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $uuid, &$purchase) {

                $purchase = FuelPurchase::where('uuid', $uuid)
                    ->with('items')
                    ->lockForUpdate()
                    ->firstOrFail();

                $anyReceived = $purchase->items->some(fn($it) => (float)$it->received_qty > 0);
                if ($anyReceived) {
                    abort(422, 'Cannot edit purchase items after receiving has started.');
                }

                $purchase->update([
                    'fuel_station_uuid' => $request->fuel_station_uuid,
                    'vendor_uuid'       => $request->vendor_uuid,
                    'purchase_date'     => $request->purchase_date,
                    'invoice_no'        => $request->invoice_no,
                    'transport_by'      => $request->transport_by ?? 'vendor',
                    'truck_no'          => $request->truck_no,
                    'note'              => $request->note,
                ]);

                FuelPurchaseItem::where('fuel_purchase_uuid', $purchase->uuid)->delete();

                $total = 0.0;
                foreach ($request->items as $it) {
                    $qty   = (float)$it['quantity'];
                    $price = (float)$it['unit_price'];

                    FuelPurchaseItem::create([
                        'fuel_purchase_uuid' => $purchase->uuid,
                        'fuel_type_uuid'     => $it['fuel_type_uuid'],
                        'fuel_unit_uuid'     => $it['fuel_unit_uuid'],
                        'quantity'           => $qty,
                        'received_qty'       => 0,
                        'unit_price'         => $price,
                        'is_active'          => true,
                    ]);

                    $total += ($qty * $price);
                }

                $purchase->update([
                    'total_amount' => $total,
                    'status'       => 'draft',
                ]);

                // Audit log
                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => "Updated Fuel Purchase {$purchase->uuid}",
                    'type'       => 'fuel_purchase',
                    'item_id'    => $purchase->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });

            Alert::success('Success', 'Fuel Purchase updated successfully.');
            return redirect()->route('fuel_purchases.show', $uuid);
        } catch (\Throwable $e) {
            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => "Failed to update Fuel Purchase {$uuid}: " . $e->getMessage(),
                'type'       => 'fuel_purchase_error',
                'item_id'    => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', $e->getMessage());
            return back()->withInput();
        }
    }
}
