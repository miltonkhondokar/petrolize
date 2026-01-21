@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        @if ($errors->any())
            <div class="alert alert-warning d-flex align-items-center p-5">
                <i class="ki-duotone ki-warning fs-2hx text-warning me-4"><i class="path1"></i><i class="path2"></i></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-warning">Validation Errors</h4>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="card card-flush shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-light-primary">
                <h3 class="card-title">
                    <i class="ki-duotone ki-basket fs-2 text-primary me-2"><i class="path1"></i><i class="path2"></i></i>
                    Create Fuel Purchase
                </h3>
                <a href="{{ route('fuel_purchases.index') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-arrow-left fs-3 me-2"></i> Back to List
                </a>
            </div>

            <div class="card-body">
                <form id="purchase_form" method="POST" action="{{ route('fuel_purchases.store') }}">
                    @csrf

                    <div class="row g-4">

                        <div class="col-md-4">
                            <label class="form-label required">Fuel Station</label>
                            <select name="fuel_station_uuid" class="form-select form-select-solid" required>
                                <option value="">Select station</option>
                                @foreach(($stations ?? []) as $st)
                                    <option value="{{ $st->uuid }}" {{ old('fuel_station_uuid')===$st->uuid ? 'selected' : '' }}>
                                        {{ $st->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label required">Vendor</label>
                            <select name="vendor_uuid" class="form-select form-select-solid" required>
                                <option value="">Select vendor</option>
                                @foreach(($vendors ?? []) as $v)
                                    <option value="{{ $v->uuid }}" {{ old('vendor_uuid')===$v->uuid ? 'selected' : '' }}>
                                        {{ $v->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label required">Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control form-control-solid"
                                   value="{{ old('purchase_date', now()->toDateString()) }}" required />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Invoice No</label>
                            <input type="text" name="invoice_no" class="form-control form-control-solid"
                                   value="{{ old('invoice_no') }}" placeholder="Optional invoice no" />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Transport By</label>
                            <select name="transport_by" class="form-select form-select-solid">
                                <option value="vendor" {{ old('transport_by','vendor')==='vendor' ? 'selected' : '' }}>Vendor</option>
                                <option value="owner" {{ old('transport_by')==='owner' ? 'selected' : '' }}>Owner</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Truck No</label>
                            <input type="text" name="truck_no" class="form-control form-control-solid"
                                   value="{{ old('truck_no') }}" placeholder="Optional truck no" />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Note</label>
                            <input type="text" name="note" class="form-control form-control-solid"
                                   value="{{ old('note') }}" placeholder="Optional note" />
                        </div>

                    </div>

                    <hr class="my-7"/>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Purchase Items</h4>
                        <button type="button" class="btn btn-sm btn-light-primary" onclick="addRow()">
                            <i class="ki-outline ki-plus fs-2 me-1"></i> Add Row
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="items_table">
                            <thead class="bg-light-dark">
                                <tr class="fw-bold text-gray-700">
                                    <th style="width: 28%">Fuel Type</th>
                                    <th style="width: 18%">Unit</th>
                                    <th style="width: 14%">Qty</th>
                                    <th style="width: 18%">Unit Price</th>
                                    <th style="width: 18%" class="text-end">Line Total</th>
                                    <th style="width: 4%" class="text-center">X</th>
                                </tr>
                            </thead>
                            <tbody id="items_body">
                                {{-- first row --}}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Grand Total</th>
                                    <th class="text-end" id="grand_total">0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="text-end mt-8">
                        <button type="reset" class="btn btn-warning me-3 btn-sm">
                            <i class="ki-solid ki-arrows-circle fs-3 me-2"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa-regular fa-floppy-disk fs-3 me-2"></i> Create Purchase
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const fuelTypes = @json(($fuelTypes ?? [])->map(fn($x)=>['uuid'=>$x->uuid,'name'=>$x->name])->values());
    const fuelUnits = @json(($fuelUnits ?? [])->map(fn($x)=>['uuid'=>$x->uuid,'name'=>$x->name,'abbr'=>$x->abbreviation])->values());

    function rowTemplate(index) {
        const fuelOptions = ['<option value="">Select fuel</option>']
            .concat(fuelTypes.map(f => `<option value="${f.uuid}">${f.name}</option>`)).join('');
        const unitOptions = ['<option value="">Select unit</option>']
            .concat(fuelUnits.map(u => `<option value="${u.uuid}">${u.name}${u.abbr ? ' ('+u.abbr+')' : ''}</option>`)).join('');

        return `
        <tr>
            <td>
                <select name="items[${index}][fuel_type_uuid]" class="form-select form-select-solid" required onchange="recalc()">
                    ${fuelOptions}
                </select>
            </td>
            <td>
                <select name="items[${index}][fuel_unit_uuid]" class="form-select form-select-solid" required onchange="recalc()">
                    ${unitOptions}
                </select>
            </td>
            <td>
                <input type="number" step="0.001" min="0.001" name="items[${index}][quantity]"
                       class="form-control form-control-solid" required oninput="recalc()" />
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${index}][unit_price]"
                       class="form-control form-control-solid" required oninput="recalc()" />
            </td>
            <td class="text-end fw-bold line_total">0.00</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-light-danger" onclick="removeRow(this)">
                    <i class="ki-duotone ki-trash fs-3"></i>
                </button>
            </td>
        </tr>`;
    }

    function addRow() {
        const body = document.getElementById('items_body');
        const index = body.children.length;
        body.insertAdjacentHTML('beforeend', rowTemplate(index));
    }

    function removeRow(btn) {
        btn.closest('tr').remove();
        reindexRows();
        recalc();
    }

    function reindexRows() {
        const rows = [...document.querySelectorAll('#items_body tr')];
        rows.forEach((tr, idx) => {
            tr.querySelectorAll('select, input').forEach(el => {
                el.name = el.name.replace(/items\[\d+\]/, `items[${idx}]`);
            });
        });
    }

    function recalc() {
        let grand = 0;
        document.querySelectorAll('#items_body tr').forEach(tr => {
            const qty = parseFloat(tr.querySelector('input[name*="[quantity]"]').value || '0');
            const price = parseFloat(tr.querySelector('input[name*="[unit_price]"]').value || '0');
            const total = qty * price;
            tr.querySelector('.line_total').innerText = total.toFixed(2);
            grand += total;
        });
        document.getElementById('grand_total').innerText = grand.toFixed(2);
    }

    // init first row
    addRow();

    document.getElementById('purchase_form').addEventListener('submit', async function(e){
        e.preventDefault();

        const rows = document.querySelectorAll('#items_body tr');
        if (!rows.length) {
            await Swal.fire('Validation Error', 'Please add at least one purchase item.', 'warning');
            return;
        }

        const result = await Swal.fire({
            title: 'Confirm Creation',
            text: 'Are you sure you want to create this fuel purchase?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, create purchase',
            cancelButtonText: 'Cancel'
        });

        if (result.isConfirmed) e.target.submit();
    });
</script>
@endpush
