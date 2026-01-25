<?php

namespace App\Http\Controllers\App\Vendor;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FuelPurchase;
use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Models\VendorPaymentAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;

class VendorPaymentController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['vendor_uuid', 'method', 'from', 'to']);

        $q = VendorPayment::with('vendor')->latest();

        if (!empty($filters['vendor_uuid'])) {
            $q->where('vendor_uuid', $filters['vendor_uuid']);
        }
        if (!empty($filters['method'])) {
            $q->where('method', $filters['method']);
        }
        if (!empty($filters['from'])) {
            $q->whereDate('payment_date', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $q->whereDate('payment_date', '<=', $filters['to']);
        }

        $payments = $q->paginate(20);
        $vendors  = Vendor::orderBy('name')->get(['uuid', 'name']);

        $breadcrumb = [
            "page_header" => "Vendor Payments",
            "first_item_name" => "Dashboard",
            "first_item_link" => route(('/')),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Payments",
            "second_item_link" => "#",
            "second_item_icon" => "fa-money-bill-wave",
        ];

        return view('application.pages.app.vendor_payments.index', compact('payments', 'vendors', 'filters', 'breadcrumb'));
    }

    public function create()
    {
        $vendors = Vendor::orderBy('name')->get(['uuid', 'name']);

        $breadcrumb = [
            "page_header" => "Vendor Payments",
            "first_item_name" => "Dashboard",
            "first_item_link" => route(('/')),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Payments",
            "second_item_link" => "#",
            "second_item_icon" => "fa-money-bill-wave",
        ];

        return view('application.pages.app.vendor_payments.create', compact('vendors', 'breadcrumb'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_uuid'  => 'required|uuid',
            'payment_date' => 'required|date',
            'method'       => 'required|in:cash,bank',
            'amount'       => 'required|numeric|min:0.01',
            'reference_no' => 'nullable|string|max:255',
            'note'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, &$payment) {

                $payment = VendorPayment::create([
                    'vendor_uuid'          => $request->vendor_uuid,
                    'created_by_user_uuid' => Auth::user()->uuid ?? null,
                    'payment_date'         => $request->payment_date,
                    'method'               => $request->method,
                    'amount'               => $request->amount,
                    'reference_no'         => $request->reference_no,
                    'note'                 => $request->note,
                ]);

                // Audit log
                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => "Created Vendor Payment {$payment->uuid}",
                    'type'       => 'vendor_payment',
                    'item_id'    => $payment->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });

            Alert::success('Success', 'Vendor payment created.');
            return redirect()->route('vendor_payments.show', $payment->uuid);
        } catch (\Throwable $e) {
            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => "Failed to create vendor payment: " . $e->getMessage(),
                'type'       => 'vendor_payment_error',
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
        $payment = VendorPayment::where('uuid', $uuid)
            ->with([
                'vendor',
                'allocations.purchase.vendor',
                'allocations.purchase.station',
                'allocations.purchase.items',
            ])
            ->firstOrFail();

        $openPurchases = FuelPurchase::where('vendor_uuid', $payment->vendor_uuid)
            ->with(['station'])
            ->orderByDesc('purchase_date')
            ->get();

        $breadcrumb = [
            "page_header" => "Vendor Payments",
            "first_item_name" => "Dashboard",
            "first_item_link" => route(('/')),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Payments",
            "second_item_link" => "#",
            "second_item_icon" => "fa-money-bill-wave",
        ];

        return view('application.pages.app.vendor_payments.show', compact('payment', 'openPurchases', 'breadcrumb'));
    }

    public function edit(string $uuid)
    {
        $payment = VendorPayment::where('uuid', $uuid)
            ->with(['allocations.purchase.station']) // eager load allocations
            ->firstOrFail();

        $vendors = Vendor::orderBy('name')->get(['uuid', 'name']);

        $breadcrumb = [
            "page_header" => "Vendor Payments",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'), // make sure this route exists
            "first_item_icon" => "fa-home",
            "second_item_name" => "Payments",
            "second_item_link" => "#",
            "second_item_icon" => "fa-money-bill-wave",
        ];

        return view('application.pages.app.vendor_payments.edit', compact('payment', 'vendors', 'breadcrumb'));
    }


    public function update(Request $request, string $uuid)
    {
        $validator = Validator::make($request->all(), [
            'vendor_uuid'  => 'required|uuid',
            'payment_date' => 'required|date',
            'method'       => 'required|in:cash,bank',
            'amount'       => 'required|numeric|min:0.01',
            'reference_no' => 'nullable|string|max:255',
            'note'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $uuid, &$payment) {

                $payment = VendorPayment::where('uuid', $uuid)
                    ->with('allocations')
                    ->lockForUpdate()
                    ->firstOrFail();

                $allocated = (float)$payment->allocations->sum('allocated_amount');
                if ((float)$request->amount + 0.0001 < $allocated) {
                    abort(422, "Cannot set amount less than already allocated amount ($allocated).");
                }

                $payment->update([
                    'vendor_uuid'  => $request->vendor_uuid,
                    'payment_date' => $request->payment_date,
                    'method'       => $request->method,
                    'amount'       => $request->amount,
                    'reference_no' => $request->reference_no,
                    'note'         => $request->note,
                ]);

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => "Updated Vendor Payment {$payment->uuid}",
                    'type'       => 'vendor_payment',
                    'item_id'    => $payment->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });

            Alert::success('Success', 'Vendor payment updated.');
            return redirect()->route('vendor_payments.show', $uuid);
        } catch (\Throwable $e) {
            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => "Failed to update Vendor Payment {$uuid}: " . $e->getMessage(),
                'type'       => 'vendor_payment_error',
                'item_id'    => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', $e->getMessage());
            return back()->withInput();
        }
    }

    public function allocate(Request $request, string $uuid)
    {
        $validator = Validator::make($request->all(), [
            'allocations' => 'required|array|min:1',
            'allocations.*.fuel_purchase_uuid' => 'required|uuid',
            'allocations.*.allocated_amount'   => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $uuid) {

                $payment = VendorPayment::where('uuid', $uuid)->lockForUpdate()->firstOrFail();

                $sum = collect($request->allocations)->sum(fn ($a) => (float)$a['allocated_amount']);
                if ($sum - (float)$payment->amount > 0.01) {
                    abort(422, "Allocated amount exceeds payment amount.");
                }

                foreach ($request->allocations as $a) {
                    $purchase = FuelPurchase::where('uuid', $a['fuel_purchase_uuid'])->firstOrFail();
                    if ($purchase->vendor_uuid !== $payment->vendor_uuid) {
                        abort(422, "Purchase does not belong to this vendor.");
                    }

                    VendorPaymentAllocation::updateOrCreate(
                        [
                            'vendor_payment_uuid' => $payment->uuid,
                            'fuel_purchase_uuid'  => $purchase->uuid,
                        ],
                        [
                            'allocated_amount' => (float)$a['allocated_amount'],
                        ]
                    );
                }

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => "Allocated Vendor Payment {$payment->uuid}",
                    'type'       => 'vendor_payment_allocation',
                    'item_id'    => $payment->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });

            Alert::success('Success', 'Allocations saved.');
            return back();
        } catch (\Throwable $e) {
            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => "Failed to allocate Vendor Payment {$uuid}: " . $e->getMessage(),
                'type'       => 'vendor_payment_error',
                'item_id'    => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', $e->getMessage());
            return back()->withInput();
        }
    }

    public function unpaidPurchases(string $vendor_uuid)
    {
        // Get unpaid fuel purchases for the vendor
        $purchases = FuelPurchase::where('vendor_uuid', $vendor_uuid)
            ->with('station')
            ->get()
            ->map(fn ($p) => [
                'uuid'          => $p->uuid,
                'invoice_no'    => $p->invoice_no ?: $p->uuid,
                'purchase_date' => $p->purchase_date->toDateString(),
                'total_amount'  => $p->total_amount,
                'paid_amount'   => $p->paid_amount,
                'balance'       => $p->balance_amount,
                'station_name'  => $p->station->name ?? '-',
            ]);

        return response()->json($purchases);
    }
}
