<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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

class FuelPurchaseWebController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['fuel_station_uuid','vendor_uuid','status','from','to']);

        $q = FuelPurchase::with(['vendor','station'])->latest();

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

        $stations = FuelStation::orderBy('name')->get(['uuid','name']);
        $vendors  = Vendor::orderBy('name')->get(['uuid','name']);

        // If you use breadcrumb in layout
        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => route('/')],
            ['title' => 'Fuel Purchases', 'url' => route('fuel_purchases.index')],
        ];

        return view('fuel_purchases.index', compact('purchases', 'filters', 'stations', 'vendors', 'breadcrumb'));
    }

    public function create()
    {
        $stations  = FuelStation::orderBy('name')->get(['uuid','name']);
        $vendors   = Vendor::orderBy('name')->get(['uuid','name']);
        $fuelTypes = FuelType::where('is_active', true)->orderBy('name')->get(['uuid','name']);
        $fuelUnits = FuelUnit::where('is_active', true)->orderBy('name')->get(['uuid','name','abbreviation']);

        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => route('/')],
            ['title' => 'Fuel Purchases', 'url' => route('fuel_purchases.index')],
            ['title' => 'Create', 'url' => route('fuel_purchases.create')],
        ];

        return view('fuel_purchases.create', compact('stations', 'vendors', 'fuelTypes', 'fuelUnits', 'breadcrumb'));
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

            'items' => 'required|array|min:1',
            'items.*.fuel_type_uuid' => 'required|uuid',
            'items.*.fuel_unit_uuid' => 'required|uuid',
            'items.*.quantity'       => 'required|numeric|min:0.001',
            'items.*.unit_price'     => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request) {

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

                // don't depend on line_total column/accessor
                $total += ($qty * $price);
            }

            $purchase->update(['total_amount' => $total]);
        });

        Alert::success('Success', 'Purchase created successfully');
        return redirect()->route('fuel_purchases.index');
    }

    public function show(string $uuid)
    {
        $purchase = FuelPurchase::where('uuid', $uuid)
            ->with(['vendor','station','items.fuelType','items.fuelUnit'])
            ->firstOrFail();

        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => route('/')],
            ['title' => 'Fuel Purchases', 'url' => route('fuel_purchases.index')],
            ['title' => 'Details', 'url' => route('fuel_purchases.show', $purchase->uuid)],
        ];

        return view('fuel_purchases.show', compact('purchase', 'breadcrumb'));
    }

    public function edit(string $uuid)
    {
        $purchase = FuelPurchase::where('uuid', $uuid)
            ->with(['items'])
            ->firstOrFail();

        $stations  = FuelStation::orderBy('name')->get(['uuid','name']);
        $vendors   = Vendor::orderBy('name')->get(['uuid','name']);
        $fuelTypes = FuelType::where('is_active', true)->orderBy('name')->get(['uuid','name']);
        $fuelUnits = FuelUnit::where('is_active', true)->orderBy('name')->get(['uuid','name','abbreviation']);

        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => route('/')],
            ['title' => 'Fuel Purchases', 'url' => route('fuel_purchases.index')],
            ['title' => 'Edit', 'url' => route('fuel_purchases.edit', $purchase->uuid)],
        ];

        return view('fuel_purchases.edit', compact('purchase', 'stations', 'vendors', 'fuelTypes', 'fuelUnits', 'breadcrumb'));
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

            'items' => 'required|array|min:1',
            'items.*.fuel_type_uuid' => 'required|uuid',
            'items.*.fuel_unit_uuid' => 'required|uuid',
            'items.*.quantity'       => 'required|numeric|min:0.001',
            'items.*.unit_price'     => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request, $uuid) {

            $purchase = FuelPurchase::where('uuid', $uuid)
                ->with('items')
                ->lockForUpdate()
                ->firstOrFail();

            // If already received any qty, block editing to avoid mismatch with ledger
            $anyReceived = $purchase->items->some(fn ($it) => (float)$it->received_qty > 0);
            if ($anyReceived) {
                abort(422, 'Cannot edit purchase items after receiving has started. Create a new purchase or adjust via ledger.');
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

            // Replace items (simple approach)
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
        });

        Alert::success('Success', 'Purchase updated successfully');
        return redirect()->route('fuel_purchases.show', $uuid);
    }

    public function receive(Request $request, string $uuid)
    {
        // allow 0, but require at least one positive qty
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.item_uuid'      => 'required|uuid',
            'items.*.received_qty'   => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $hasPositive = collect($request->items)->some(fn ($x) => (float)($x['received_qty'] ?? 0) > 0);
        if (!$hasPositive) {
            return back()->withErrors(['items' => 'Please enter receive quantity for at least one item.'])->withInput();
        }

        DB::transaction(function () use ($request, $uuid) {

            $purchase = FuelPurchase::where('uuid', $uuid)
                ->with('items')
                ->lockForUpdate()
                ->firstOrFail();

            foreach ($request->items as $r) {
                $qtyNow = (float)$r['received_qty'];
                if ($qtyNow <= 0) {
                    continue;
                }

                $item = $purchase->items->firstWhere('uuid', $r['item_uuid']);
                if (!$item) {
                    abort(422, "Purchase item not found.");
                }

                $newReceived = (float)$item->received_qty + $qtyNow;
                if ($newReceived > (float)$item->quantity + 0.0001) {
                    abort(422, "Received qty exceeds purchased qty.");
                }

                $item->update(['received_qty' => $newReceived]);

                StockLedgerService::add([
                    'fuel_station_uuid' => $purchase->fuel_station_uuid,
                    'fuel_type_uuid'    => $item->fuel_type_uuid,
                    'fuel_unit_uuid'    => $item->fuel_unit_uuid,
                    'txn_type'          => 'purchase_receive',
                    'ref_uuid'          => $purchase->uuid,
                    'txn_date'          => $purchase->purchase_date,
                    'qty_in'            => $qtyNow,
                    'qty_out'           => 0,
                    'note'              => 'Purchase received (Web)',
                ]);
            }

            $purchase->refresh();

            $allReceived = $purchase->items->every(fn ($it) => (float)$it->received_qty >= (float)$it->quantity);
            $anyReceived = $purchase->items->some(fn ($it) => (float)$it->received_qty > 0);

            $purchase->update([
                'status' => $allReceived ? 'received_full' : ($anyReceived ? 'received_partial' : 'draft')
            ]);
        });

        Alert::success('Success', 'Stock received and ledger updated.');
        return back();
    }
}