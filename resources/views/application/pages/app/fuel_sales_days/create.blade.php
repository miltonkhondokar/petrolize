@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-warning p-5">
                    <h4 class="mb-2">Validation Errors</h4>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-light-primary d-flex justify-content-between">
                    <h3 class="card-title">{{ isset($day) ? 'Edit Fuel Sales Day (Draft)' : 'Create Fuel Sales Day' }}</h3>
                    <a href="{{ isset($day) ? route('fuel_sales_days.show', $day->uuid) : route('fuel_sales_days.index') }}"
                        class="btn btn-sm btn-primary">Back</a>
                </div>

                <div class="card-body">
                    <form method="POST"
                        action="{{ isset($day) ? route('fuel_sales_days.update', $day->uuid) : route('fuel_sales_days.store') }}">
                        @csrf
                        @if (isset($day))
                            @method('PUT')
                        @endif

                        {{-- Station & Sale Date --}}
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label required">Station</label>
                                <select name="fuel_station_uuid" class="form-select form-select-solid" required>
                                    <option value="">Select</option>
                                    @foreach ($stations as $st)
                                        <option value="{{ $st->uuid }}"
                                            {{ old('fuel_station_uuid', $day->fuel_station_uuid ?? '') == $st->uuid ? 'selected' : '' }}>
                                            {{ $st->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Sale Date</label>
                                <input type="date" name="sale_date" class="form-control form-control-solid"
                                    value="{{ old('sale_date', isset($day) ? $day->sale_date->format('Y-m-d') : date('Y-m-d')) }}"
                                    required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Note</label>
                                <textarea name="note" class="form-control form-control-solid" rows="2">{{ old('note', $day->note ?? '') }}</textarea>
                            </div>
                        </div>

                        <hr class="my-6">

                        {{-- Items Table --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Items</h4>
                            <button type="button" class="btn btn-sm btn-light-primary" onclick="addRow()">+ Add
                                Row</button>
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
                                    @if (isset($day) && $day->items->count())
                                        @foreach ($day->items as $i => $it)
                                            <tr>
                                                <td>
                                                    <select name="items[{{ $i }}][fuel_type_uuid]"
                                                        class="form-select form-select-solid fuel-type"
                                                        data-price="{{ $it->price_per_unit }}" required>
                                                        <option value="">Select</option>
                                                        @foreach ($fuelTypes as $ft)
                                                            <option value="{{ $ft->uuid }}"
                                                                {{ $it->fuel_type_uuid == $ft->uuid ? 'selected' : '' }}>
                                                                {{ $ft->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" name="items[{{ $i }}][nozzle_number]"
                                                        class="form-control form-control-solid" min="1"
                                                        value="{{ $it->nozzle_number }}"></td>
                                                <td><input type="number" step="0.001"
                                                        name="items[{{ $i }}][opening_reading]"
                                                        class="form-control form-control-solid opening" min="0"
                                                        required value="{{ $it->opening_reading }}"></td>
                                                <td><input type="number" step="0.001"
                                                        name="items[{{ $i }}][closing_reading]"
                                                        class="form-control form-control-solid closing" min="0"
                                                        required value="{{ $it->closing_reading }}"></td>
                                                <td><input type="number" step="0.001"
                                                        class="form-control form-control-solid sold-qty" readonly
                                                        value="{{ $it->sold_qty }}"></td>
                                                <td><input type="number" step="0.01"
                                                        class="form-control form-control-solid line-total" readonly
                                                        value="{{ $it->line_total }}"></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-light-danger"
                                                        onclick="removeRow(this)">X</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        {{-- Default empty row for create --}}
                                        <tr>
                                            <td>
                                                <select name="items[0][fuel_type_uuid]"
                                                    class="form-select form-select-solid fuel-type" data-price="0" required>
                                                    <option value="">Select</option>
                                                    @foreach ($fuelTypes as $ft)
                                                        <option value="{{ $ft->uuid }}" data-price="0">
                                                            {{ $ft->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" name="items[0][nozzle_number]"
                                                    class="form-control form-control-solid" min="1"></td>
                                            <td><input type="number" step="0.001" name="items[0][opening_reading]"
                                                    class="form-control form-control-solid opening" min="0" required>
                                            </td>
                                            <td><input type="number" step="0.001" name="items[0][closing_reading]"
                                                    class="form-control form-control-solid closing" min="0"
                                                    required></td>
                                            <td><input type="number" step="0.001"
                                                    class="form-control form-control-solid sold-qty" readonly></td>
                                            <td><input type="number" step="0.01"
                                                    class="form-control form-control-solid line-total" readonly></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-light-danger"
                                                    onclick="removeRow(this)">X</button>
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
  // ==========================
  // Fuel Sales Day - Items JS
  // (No data loss + no duplicate listeners)
  // ==========================

  let rowIndex = {{ isset($day) ? $day->items->count() : 1 }};

  // --- Helpers ---
  function getStationSelect() {
    return document.querySelector('select[name="fuel_station_uuid"]');
  }

  function recalcRow(tr) {
    const opening = tr.querySelector('.opening');
    const closing = tr.querySelector('.closing');
    const soldQty  = tr.querySelector('.sold-qty');
    const lineTotal = tr.querySelector('.line-total');
    const fuelType = tr.querySelector('.fuel-type');

    const sold = parseFloat(closing?.value || 0) - parseFloat(opening?.value || 0);
    soldQty.value = sold > 0 ? sold.toFixed(3) : "0.000";

    // ✅ price is stored on selected OPTION (dataset.price)
    const selectedOpt = fuelType?.selectedOptions?.[0];
    const price = parseFloat((selectedOpt && selectedOpt.dataset.price) || 0);

    lineTotal.value = sold > 0 ? (sold * price).toFixed(2) : "0.00";
  }

  function bindRowEvents(tr) {
    // ✅ prevent duplicate bindings (important!)
    if (tr.dataset.bound === "1") return;
    tr.dataset.bound = "1";

    const opening = tr.querySelector('.opening');
    const closing = tr.querySelector('.closing');
    const fuelType = tr.querySelector('.fuel-type');

    const handler = () => recalcRow(tr);

    opening.addEventListener('input', handler);
    closing.addEventListener('input', handler);
    fuelType.addEventListener('change', handler);

    // store handler so we can recalc later without rebinding
    tr._recalc = handler;

    // initial calc (won’t lose any existing values)
    handler();
  }

  function bindAllRows() {
    document.querySelectorAll('#itemsTable tbody tr').forEach(tr => bindRowEvents(tr));
  }

  function recalcAllRows() {
    document.querySelectorAll('#itemsTable tbody tr').forEach(tr => {
      if (typeof tr._recalc === 'function') tr._recalc();
      else recalcRow(tr);
    });
  }

  function applyPricesToAllFuelSelects(prices) {
    document.querySelectorAll('.fuel-type').forEach(select => {
      select.querySelectorAll('option').forEach(opt => {
        if (!opt.value) return; // skip "Select"
        opt.dataset.price = prices[opt.value] ?? 0;
      });
    });
  }

  // --- Row add/remove ---
  function addRow() {
    const tbody = document.querySelector('#itemsTable tbody');
    const tr = document.createElement('tr');

    // NOTE: we keep data-price on OPTIONs (will be updated by station fetch)
    tr.innerHTML = `
      <td>
        <select name="items[${rowIndex}][fuel_type_uuid]" class="form-select form-select-solid fuel-type" required>
          <option value="">Select</option>
          @foreach ($fuelTypes as $ft)
            <option value="{{ $ft->uuid }}" data-price="0">{{ $ft->name }}</option>
          @endforeach
        </select>
      </td>
      <td><input type="number" name="items[${rowIndex}][nozzle_number]" class="form-control form-control-solid" min="1"></td>
      <td><input type="number" step="0.001" name="items[${rowIndex}][opening_reading]" class="form-control form-control-solid opening" min="0" required></td>
      <td><input type="number" step="0.001" name="items[${rowIndex}][closing_reading]" class="form-control form-control-solid closing" min="0" required></td>
      <td><input type="number" step="0.001" class="form-control form-control-solid sold-qty" readonly value="0.000"></td>
      <td><input type="number" step="0.01" class="form-control form-control-solid line-total" readonly value="0.00"></td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-light-danger" onclick="removeRow(this)">X</button>
      </td>
    `;

    tbody.appendChild(tr);

    // bind listeners once
    bindRowEvents(tr);

    // if a station is selected, fetch prices and apply to this new row too
    const stationId = getStationSelect()?.value;
    if (stationId) {
      fetch(`/fuel-station/${stationId}/prices`)
        .then(res => res.json())
        .then(prices => {
          applyPricesToAllFuelSelects(prices);
          recalcRow(tr);
        })
        .catch(() => {});
    }

    rowIndex++;
  }

  function removeRow(btn) {
    const tr = btn.closest('tr');
    const tbody = tr.parentElement;
    if (tbody.children.length === 1) return; // prevent removing all rows
    tr.remove();
  }

  // expose for inline onclick
  window.addRow = addRow;
  window.removeRow = removeRow;

  // --- Station change: fetch prices, update options, recalc (NO rebinding) ---
  getStationSelect()?.addEventListener('change', function () {
    const stationId = this.value;
    if (!stationId) return;

    fetch(`/fuel-station/${stationId}/prices`)
      .then(res => res.json())
      .then(prices => {
        applyPricesToAllFuelSelects(prices);
        recalcAllRows(); // ✅ recalc only, no data loss, no duplicate listeners
      })
      .catch(err => console.error('Price fetch failed', err));
  });

  // --- On page load ---
  document.addEventListener('DOMContentLoaded', () => {
    // bind all existing rows once
    bindAllRows();

    // if station already selected (edit/old input), auto-load prices + recalc
    const st = getStationSelect();
    if (st && st.value) {
      st.dispatchEvent(new Event('change'));
    }
  });
</script>
@endpush

