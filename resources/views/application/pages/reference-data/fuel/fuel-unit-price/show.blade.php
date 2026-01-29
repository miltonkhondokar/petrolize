@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="row">
                <div class="col-md-12">

                    {{-- Alerts (same as create/edit) --}}
                    @if ($errors->any())
                        <div class="alert alert-warning d-flex align-items-center p-5 mb-5">
                            <i class="ki-duotone ki-warning fs-2hx text-warning me-4">
                                <i class="path1"></i><i class="path2"></i>
                            </i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-warning">Validation Errors</h4>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success d-flex align-items-center p-5 mb-5">
                            <i class="ki-duotone ki-check fs-2hx text-success me-4">
                                <i class="path1"></i><i class="path2"></i>
                            </i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-success">Success!</h4>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger d-flex align-items-center p-5 mb-5">
                            <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                                <i class="path1"></i><i class="path2"></i>
                            </i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-danger">Error!</h4>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="card card-flush shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light-danger">
                            <h3 class="card-title">
                                <i class="ki-duotone ki-eye fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Fuel Unit Prices Details
                            </h3>

                            <div class="d-flex gap-2">
                                <a href="{{ route('fuel-unit-price.edit', $stationUuid) }}" class="btn btn-sm btn-warning">
                                    <i class="ki-duotone ki-pencil fs-3 me-2">
                                        <i class="path1"></i><i class="path2"></i>
                                    </i>
                                    Edit Station Prices
                                </a>

                                <a href="{{ route('fuel-unit-price.index') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-arrow-left fs-3 me-2"></i>
                                    Back to List
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            {{-- Station --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-5">
                                        <label class="form-label required">
                                            <i class="ki-duotone ki-home-3 fs-2 me-2 text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Fuel Station
                                        </label>

                                        {{-- Read-only station selector (still matches create style) --}}
                                        <select
                                            name="fuel_station_uuid"
                                            id="fuel_station_uuid"
                                            class="form-select form-select-solid"
                                            disabled
                                        >
                                            <option value="">Select Fuel Station</option>
                                            @foreach ($fuelStations as $fuelStation)
                                                <option
                                                    value="{{ $fuelStation->uuid }}"
                                                    {{ ($stationUuid == $fuelStation->uuid) ? 'selected' : '' }}
                                                >
                                                    {{ $fuelStation->name }}
                                                    @if ($fuelStation->location)
                                                        - {{ $fuelStation->location }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>

                                        <div class="form-text">
                                            Viewing prices for the selected station.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-5">
                                        <label class="form-label">
                                            <i class="ki-duotone ki-information-5 fs-2 me-2 text-info">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Info
                                        </label>
                                        <div class="p-4 rounded bg-light">
                                            <div class="text-muted">
                                                This page shows all <b>active fuel types</b> and their saved prices for this station.
                                                To modify, click <b>Edit Station Prices</b>.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Fuel Types Table (Read-only, like create) --}}
                            <div id="fuelTypesWrapper" class="mt-4" style="display:none;">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h4 class="mb-0">
                                        <i class="ki-duotone ki-gas-station fs-2 me-2 text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Fuel Types & Prices
                                    </h4>
                                    <small class="text-muted">Read-only view</small>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3">
                                        <thead>
                                            <tr class="fw-bold text-muted">
                                                <th>Fuel Type</th>
                                                <th style="width:260px;">Price Per Unit</th>
                                                <th style="width:140px;">Enabled</th>
                                            </tr>
                                        </thead>
                                        <tbody id="fuelTypesTbody"></tbody>
                                    </table>
                                </div>

                                <div class="text-muted fs-8 mt-2">
                                    Note: If a fuel type has no saved record, it will show as <b>Not Set</b>.
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    /**
     * Uses your existing endpoint:
     *  route('fuel-unit-price.station-fuel-types', stationUuid)
     *
     * Response:
     * {
     *   fuelTypes: [{uuid, name, code, rating_value}, ...],
     *   prices: [{fuel_type_uuid, price_per_unit, is_active}, ...]
     * }
     */

    const stationUuid = @json($stationUuid);
    const wrapper = document.getElementById('fuelTypesWrapper');
    const tbody = document.getElementById('fuelTypesTbody');

    function renderRowsReadOnly(fuelTypes, pricesArr) {
        const pricesMap = {};
        (pricesArr || []).forEach(p => pricesMap[p.fuel_type_uuid] = p);

        tbody.innerHTML = '';

        if (!fuelTypes || fuelTypes.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td colspan="3" class="text-center text-muted py-5">No active fuel types found.</td>`;
            tbody.appendChild(tr);
            return;
        }

        fuelTypes.forEach(ft => {
            const existing = pricesMap[ft.uuid] || null;

            const priceVal = existing ? (existing.price_per_unit ?? '') : '';
            const isActive = existing ? !!existing.is_active : false;

            const priceHtml = priceVal !== ''
                ? `<span class="badge badge-light-primary fs-6">
                        <i class="ki-duotone ki-dollar fs-4 me-1"></i>
                        ${Number(priceVal).toFixed(2)}
                   </span>`
                : `<span class="badge badge-light-warning fs-6">Not Set</span>`;

            const statusHtml = existing
                ? `<span class="badge badge-light-${isActive ? 'success' : 'danger'}">
                        <i class="ki-duotone ${isActive ? 'ki-check-circle' : 'ki-cross'} fs-5 me-1"></i>
                        ${isActive ? 'Enabled' : 'Disabled'}
                   </span>`
                : `<span class="badge badge-light-warning">Not Set</span>`;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="fw-semibold">
                        ${ft.name} <span class="text-muted">(${ft.code})</span>
                    </div>
                    <div class="text-muted fs-8">Rating: ${ft.rating_value ?? 'N/A'}</div>
                </td>

                <td>${priceHtml}</td>

                <td>${statusHtml}</td>
            `;

            tbody.appendChild(tr);
        });
    }

    async function loadFuelTypes(stationUuid) {
        if (!stationUuid) {
            wrapper.style.display = 'none';
            tbody.innerHTML = '';
            return;
        }

        const url = "{{ route('fuel-unit-price.station-fuel-types', ':uuid') }}".replace(':uuid', stationUuid);

        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
            if (!res.ok) throw new Error('Failed to load fuel types');

            const data = await res.json();
            renderRowsReadOnly(data.fuelTypes || [], data.prices || []);
            wrapper.style.display = 'block';
        } catch (e) {
            wrapper.style.display = 'none';
            tbody.innerHTML = '';
            Swal.fire('Error', 'Failed to load fuel types for this station.', 'error');
        }
    }

    // load on page init
    loadFuelTypes(stationUuid);
</script>
@endpush
