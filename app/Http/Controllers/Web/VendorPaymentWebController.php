<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FuelPurchase;
use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Models\VendorPaymentAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class VendorPaymentWebController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['vendor_uuid','method','from','to']);

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

        $vendors = Vendor::orderBy('name')->get(['uuid','name']);


        $breadcrumb = [
            "page_header" => "Audit Logs",
            "first_item_name" => "Users",
            "first_item_link" => route('audit.user.index'),
            "first_item_icon" => "fa-user-shield",
            "second_item_name" => "Activity Log",
            "second_item_link" => "#",
            "second_item_icon" => "fa-list-check",
        ];


        return view('application.pages.vendor_payments.index', compact('payments', 'vendors', 'filters', 'breadcrumb'));
    }

    public function create()
    {
        $vendors = Vendor::orderBy('name')->get(['uuid','name']);


        $breadcrumb = [
            "page_header" => "Audit Logs",
            "first_item_name" => "Users",
            "first_item_link" => route('audit.user.index'),
            "first_item_icon" => "fa-user-shield",
            "second_item_name" => "Activity Log",
            "second_item_link" => "#",
            "second_item_icon" => "fa-list-check",
        ];


        return view('application.pages.vendor_payments.create', compact('vendors', 'breadcrumb'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_uuid'          => 'required|uuid',
            'payment_date'         => 'required|date',
            'method'               => 'required|in:cash,bank',
            'amount'               => 'required|numeric|min:0.01',
            'reference_no'         => 'nullable|string|max:255',
            'note'                 => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $payment = VendorPayment::create([
            'vendor_uuid'          => $request->vendor_uuid,
            'created_by_user_uuid' => auth()->user()->uuid ?? null,
            'payment_date'         => $request->payment_date,
            'method'               => $request->method,
            'amount'               => $request->amount,
            'reference_no'         => $request->reference_no,
            'note'                 => $request->note,
        ]);

        Alert::success('Success', 'Vendor payment created.');
        return redirect()->route('vendor_payments.show', $payment->uuid);
    }

    public function show(string $uuid)
    {
        $payment = VendorPayment::where('uuid', $uuid)
            ->with(['vendor','allocations.purchase.vendor','allocations.purchase.station'])
            ->firstOrFail();

        // list open purchases for allocation UI (optional)
        $openPurchases = FuelPurchase::where('vendor_uuid', $payment->vendor_uuid)
            ->with(['station'])
            ->orderByDesc('purchase_date')
            ->get();


        $breadcrumb = [
            "page_header" => "Audit Logs",
            "first_item_name" => "Users",
            "first_item_link" => route('audit.user.index'),
            "first_item_icon" => "fa-user-shield",
            "second_item_name" => "Activity Log",
            "second_item_link" => "#",
            "second_item_icon" => "fa-list-check",
        ];


        return view('application.pages.vendor_payments.show', compact('payment', 'openPurchases', 'breadcrumb'));
    }

    public function edit(string $uuid)
    {
        $payment = VendorPayment::where('uuid', $uuid)->firstOrFail();
        $vendors = Vendor::orderBy('name')->get(['uuid','name']);


        $breadcrumb = [
            "page_header" => "Audit Logs",
            "first_item_name" => "Users",
            "first_item_link" => route('audit.user.index'),
            "first_item_icon" => "fa-user-shield",
            "second_item_name" => "Activity Log",
            "second_item_link" => "#",
            "second_item_icon" => "fa-list-check",
        ];


        return view('application.pages.vendor_payments.edit', compact('payment', 'vendors', 'breadcrumb'));
    }

    public function update(Request $request, string $uuid)
    {
        $validator = Validator::make($request->all(), [
            'vendor_uuid'          => 'required|uuid',
            'payment_date'         => 'required|date',
            'method'               => 'required|in:cash,bank',
            'amount'               => 'required|numeric|min:0.01',
            'reference_no'         => 'nullable|string|max:255',
            'note'                 => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request, $uuid) {
            $payment = VendorPayment::where('uuid', $uuid)
                ->with('allocations')
                ->lockForUpdate()
                ->firstOrFail();

            // If allocations exist, prevent reducing amount below allocated sum
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
        });

        Alert::success('Success', 'Vendor payment updated.');
        return redirect()->route('vendor_payments.show', $uuid);
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

        DB::transaction(function () use ($request, $uuid) {
            $payment = VendorPayment::where('uuid', $uuid)->lockForUpdate()->firstOrFail();

            $sum = 0.0;
            foreach ($request->allocations as $a) {
                $sum += (float)$a['allocated_amount'];
            }

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
        });

        Alert::success('Success', 'Allocations saved.');
        return back();
    }
}
