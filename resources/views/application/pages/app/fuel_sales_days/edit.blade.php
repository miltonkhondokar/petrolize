@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        @if ($errors->any())
            <div class="alert alert-warning p-5">
                <h4 class="mb-2">Validation Errors</h4>
                <ul class="mb-0">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-light-primary d-flex justify-content-between">
                <h3 class="card-title">{{ isset($day) ? 'Edit Fuel Sales Day (Draft)' : 'Create Fuel Sales Day' }}</h3>
                <a href="{{ isset($day) ? route('fuel_sales_days.show', $day->uuid) : route('fuel_sales_days.index') }}" class="btn btn-sm btn-primary">Back</a>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ isset($day) ? route('fuel_sales_days.update', $day->uuid) : route('fuel_sales_days.store') }}">
                    @csrf
                    @if(isset($day)) @method('PUT') @endif

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label required">Station</label>
                            <select name="fuel_station_uuid" class="form-select form-select-solid" required>
                                <option value="">Select</option>
                                @foreach($stations as $st)
                                    <option value="{{ $st->uuid }}" {{ old('fuel_station_uuid', $day->fuel_station_uuid ?? '')==$st->uuid ? 'selected' : '' }}>
                                        {{ $st->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Sale Date</label>
                            <input type="date" name="sale_date" class="form-control form-control-solid"
                                   value="{{ old('sale_date', isset($day) ? $day->sale_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control form-control-solid" rows="2">{{ old('note', $day->note ?? '') }}</textarea>
                        </div>
                    </div>

                    <hr class="my-6">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Items</h4>
                        <button type="button" class="btn btn-sm btn-light-primary" onclick="addRow()">+ Add Row</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 25%">Fuel Type</th>
                                    <th style="width: 10%">Nozzle</th>
                                    <th style="width: 15%">Opening</th>
                                    <th style="width: 15%">Closing</th>
                                    <th style="width: 15%">Sold Qty</th>
                                    <th style="width: 15%">Line Total</th>
                                    <th style="width: 5%">Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($day))
                                    @foreach($day->items as $i => $it)
                                    <tr>
                                        <td>
                                            <select name="items[{{ $i }}][fuel_type_uuid]" class="form-select form-select-solid fuel-type" data-price="{{ $it->price_per_unit }}" required>
                                                <option value="">Select</option>
                                                @foreach($fuelTypes as $ft)
                                                    <option value="{{ $ft->uuid }}" {{ $it->fuel_type_uuid==$ft->uuid ? 'selected' : '' }}>{{ $ft->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="items[{{ $i }}][nozzle_number]" class="form-control form-control-solid" min="1" value="{{ $it->nozzle_number }}"></td>
                                        <td><input type="number" step="0.001" name="items[{{ $i }}][opening_reading]" class="form-control form-control-solid opening" min="0" required value="{{ $it->opening_reading }}"></td>
                                        <td><input type="number" step="0.001" name="items[{{ $i }}][closing_reading]" class="form-control form-control-solid closing" min="0" required value="{{ $it->closing_reading }}"></td>
                                        <td><input type="number" step="0.001" class="form-control form-control-solid sold-qty" readonly value="{{ $it->sold_qty }}"></td>
                                        <td><input type="number" step="0.01" class="form-control form-control-solid line-total" readonly value="{{ $it->line_total }}"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-light-danger" onclick="removeRow(this)">X</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>
                                            <select name="items[0][fuel_type_uuid]" class="form-select form-select-solid fuel-type" data-price="0" required>
                                                <option value="">Select</option>
                                                @foreach($fuelTypes as $ft)
                                                    <option value="{{ $ft->uuid }}" data-price="0">{{ $ft->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="items[0][nozzle_number]" class="form-control form-control-solid" min="1"></td>
                                        <td><input type="number" step="0.001" name="items[0][opening_reading]" class="form-control form-control-solid opening" min="0" required></td>
                                        <td><input type="number" step="0.001" name="items[0][closing_reading]" class="form-control form-control-solid closing" min="0" required></td>
                                        <td><input type="number" step="0.001" class="form-control form-control-solid sold-qty" readonly></td>
                                        <td><input type="number" step="0.01" class="form-control form-control-solid line-total" readonly></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-light-danger" onclick="removeRow(this)">X</button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end mt-6">
                        <button class="btn btn-primary">{{ isset($day) ? 'Update Draft' : 'Create Draft' }}</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
let rowIndex = {{ isset($day) ? $day->items->count() : 1 }};

function addRow() {
    const tbody = document.querySelector('#itemsTable tbody');
    const tr = document.createElement('tr');

    tr.innerHTML = `
        <td>
            <select name="items[${rowIndex}][fuel_type_uuid]" class="form-select form-select-solid fuel-type" data-price="0" required>
                <option value="">Select</option>
                @foreach($fuelTypes as $ft)
                    <option value="{{ $ft->uuid }}" data-price="0">{{ $ft->name }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="items[${rowIndex}][nozzle_number]" class="form-control form-control-solid" min="1"></td>
        <td><input type="number" step="0.001" name="items[${rowIndex}][opening_reading]" class="form-control form-control-solid opening" min="0" required></td>
        <td><input type="number" step="0.001" name="items[${rowIndex}][closing_reading]" class="form-control form-control-solid closing" min="0" required></td>
        <td><input type="number" step="0.001" class="form-control form-control-solid sold-qty" readonly></td>
        <td><input type="number" step="0.01" class="form-control form-control-solid line-total" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-light-danger" onclick="removeRow(this)">X</button>
        </td>
    `;
    tbody.appendChild(tr);
    bindRowEvents(tr);
    rowIndex++;
}

function removeRow(btn) {
    const tr = btn.closest('tr');
    const tbody = tr.parentElement;
    if (tbody.children.length === 1) return;
    tr.remove();
}

function bindRowEvents(tr) {
    const opening = tr.querySelector('.opening');
    const closing = tr.querySelector('.closing');
    const soldQty = tr.querySelector('.sold-qty');
    const lineTotal = tr.querySelector('.line-total');
    const fuelType = tr.querySelector('.fuel-type');

    function updateTotals() {
        const sold = parseFloat(closing.value || 0) - parseFloat(opening.value || 0);
        soldQty.value = sold > 0 ? sold.toFixed(3) : 0;
        const price = parseFloat(fuelType.selectedOptions[0]?.dataset.price || 0);
        lineTotal.value = (sold * price).toFixed(2);
    }

    opening.addEventListener('input', updateTotals);
    closing.addEventListener('input', updateTotals);
    fuelType.addEventListener('change', updateTotals);

    updateTotals();
}

// Bind existing rows
document.querySelectorAll('#itemsTable tbody tr').forEach(tr => bindRowEvents(tr));
</script>
@endpush
