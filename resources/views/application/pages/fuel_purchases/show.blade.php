@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <div class="card shadow-sm">
            <div class="card-header bg-light-primary d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="ki-duotone ki-basket fs-2 text-primary me-2"><i class="path1"></i><i class="path2"></i></i>
                    Fuel Purchase Details
                </h3>
                <div class="d-flex gap-2">
                    <a href="{{ route('fuel_purchases.edit', $purchase->uuid) }}" class="btn btn-sm btn-warning">
                        <i class="ki-duotone ki-pencil fs-3 me-1"><i class="path1"></i><i class="path2"></i></i>
                        Edit
                    </a>
                    <a href="{{ route('fuel_purchases.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-left fs-3 me-2"></i>
                        Back
                    </a>
                </div>
            </div>

            <div class="card-body">
                @php
                    $badge = 'secondary';
                    if ($purchase->status === 'received_full') $badge = 'success';
                    elseif ($purchase->status === 'received_partial') $badge = 'warning';
                    elseif ($purchase->status === 'draft') $badge = 'info';
                @endphp

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex flex-column gap-4">
                            <div><span class="fw-bold text-gray-600">Purchase Date:</span>
                                <span class="text-muted">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</span>
                            </div>
                            <div><span class="fw-bold text-gray-600">Station:</span>
                                <span class="fw-semibold">{{ $purchase->station->name ?? '-' }}</span>
                            </div>
                            <div><span class="fw-bold text-gray-600">Vendor:</span>
                                <span class="fw-semibold">{{ $purchase->vendor->name ?? '-' }}</span>
                            </div>
                            <div><span class="fw-bold text-gray-600">Invoice No:</span>
                                <span class="text-muted">{{ $purchase->invoice_no ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex flex-column gap-4">
                            <div><span class="fw-bold text-gray-600">Transport By:</span>
                                <span class="text-muted">{{ ucfirst($purchase->transport_by ?? '-') }}</span>
                            </div>
                            <div><span class="fw-bold text-gray-600">Truck No:</span>
                                <span class="text-muted">{{ $purchase->truck_no ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="fw-bold text-gray-600">Status:</span>
                                <span class="badge badge-light-{{ $badge }}">{{ str_replace('_',' ', ucfirst($purchase->status)) }}</span>
                            </div>
                            <div><span class="fw-bold text-gray-600">Total Amount:</span>
                                <span class="fw-bold">{{ number_format((float)$purchase->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div><span class="fw-bold text-gray-600">Note:</span>
                            <span class="text-muted">{{ $purchase->note ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                <hr class="my-7"/>

                <h4 class="mb-4">Purchase Items</h4>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="bg-light-dark">
                            <tr class="fw-bold text-gray-700">
                                <th>#</th>
                                <th>Fuel Type</th>
                                <th>Unit</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Received</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($purchase->items ?? []) as $i => $it)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="fw-semibold">{{ $it->fuelType->name ?? $it->fuel_type_uuid }}</td>
                                    <td>{{ $it->fuelUnit->abbreviation ?? $it->fuelUnit->name ?? '-' }}</td>
                                    <td class="text-end">{{ number_format((float)$it->quantity, 3) }}</td>
                                    <td class="text-end">
                                        <span class="badge badge-light-{{ ((float)$it->received_qty >= (float)$it->quantity) ? 'success' : 'warning' }}">
                                            {{ number_format((float)$it->received_qty, 3) }}
                                        </span>
                                    </td>
                                    <td class="text-end">{{ number_format((float)$it->unit_price, 2) }}</td>
                                    <td class="text-end fw-bold">{{ number_format((float)$it->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-end">Grand Total</th>
                                <th class="text-end">{{ number_format((float)$purchase->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <hr class="my-7"/>

                {{-- Receive stock section --}}
                <div class="card card-flush bg-light-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ki-duotone ki-delivery fs-2 text-info me-2"><i class="path1"></i><i class="path2"></i></i>
                            Receive Stock (creates ledger IN)
                        </h3>
                    </div>

                    <div class="card-body">
                        <form id="receive_form" method="POST" action="{{ route('fuel_purchases.receive', $purchase->uuid) }}">
                            @csrf

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="bg-light-dark">
                                        <tr class="fw-bold text-gray-700">
                                            <th>Fuel Type</th>
                                            <th class="text-end">Remaining</th>
                                            <th class="text-end">Receive Now</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(($purchase->items ?? []) as $idx => $it)
                                            @php
                                                $remaining = max(0, (float)$it->quantity - (float)$it->received_qty);
                                            @endphp
                                            <tr>
                                                <td class="fw-semibold">{{ $it->fuelType->name ?? '-' }}</td>
                                                <td class="text-end">{{ number_format($remaining, 3) }}</td>
                                                <td class="text-end">
                                                    <input type="hidden" name="items[{{ $idx }}][fuel_type_uuid]" value="{{ $it->fuel_type_uuid }}">
                                                    <input type="number" step="0.001" min="0" max="{{ $remaining }}"
                                                           name="items[{{ $idx }}][received_qty]"
                                                           class="form-control form-control-solid text-end"
                                                           value="0">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-info btn-sm">
                                    <i class="ki-duotone ki-check fs-3 me-2"><i class="path1"></i><i class="path2"></i></i>
                                    Receive
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('receive_form').addEventListener('submit', async function(e){
        e.preventDefault();

        const inputs = [...document.querySelectorAll('input[name$="[received_qty]"]')];
        const totalReceive = inputs.reduce((sum, el) => sum + (parseFloat(el.value || '0')), 0);

        if (totalReceive <= 0) {
            await Swal.fire('Validation Error', 'Please enter receive quantity for at least one item.', 'warning');
            return;
        }

        const result = await Swal.fire({
            title: 'Confirm Receive',
            text: 'This will add stock into ledger. Continue?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, receive stock',
            cancelButtonText: 'Cancel'
        });

        if (result.isConfirmed) e.target.submit();
    });
</script>
@endpush
