@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="row">
                <div class="col-md-12">

                    {{-- Alerts --}}
                    @if ($errors->any())
                        <div class="alert alert-warning d-flex align-items-center p-5">
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
                        <div class="alert alert-success d-flex align-items-center p-5">
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
                        <div class="alert alert-danger d-flex align-items-center p-5">
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
                        <div class="card-header d-flex justify-content-between align-items-center bg-light-primary">
                            <h3 class="card-title">
                                <i class="ki-duotone ki-dollar fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Create New Fuel Unit Prices
                            </h3>
                            <a href="{{ route('fuel-unit-price.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left fs-3 me-2"></i>
                                Back to List
                            </a>
                        </div>

                        <div class="card-body">
                            <form id="fuel_unit_price_form" method="POST" action="{{ route('fuel-unit-price.store') }}">
                                @csrf

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

                                            <select
                                                name="fuel_station_uuid"
                                                id="fuel_station_uuid"
                                                class="form-select form-select-solid"
                                                data-kt-validate="true"
                                                data-kt-validate-required="Fuel Station selection is required"
                                            >
                                                <option value="">Select Fuel Station</option>
                                                @foreach ($fuelStations as $fuelStation)
                                                    <option
                                                        value="{{ $fuelStation->uuid }}"
                                                        {{ old('fuel_station_uuid') == $fuelStation->uuid ? 'selected' : '' }}
                                                    >
                                                        {{ $fuelStation->name }}
                                                        @if ($fuelStation->location)
                                                            - {{ $fuelStation->location }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>

                                            <div class="form-text">Select the fuel station, then set prices for fuel types</div>
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
                                                    After selecting a station, all <b>active fuel types</b> will appear below.
                                                    You can set <b>price per unit</b> and <b>enable/disable</b> each type.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Fuel Types Table --}}
                                <div id="fuelTypesWrapper" class="mt-4" style="display:none;">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h4 class="mb-0">
                                            <i class="ki-duotone ki-gas-station fs-2 me-2 text-info">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Fuel Types & Prices
                                        </h4>
                                        <small class="text-muted">Set price and enable/disable per fuel type</small>
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
                                        Note: Disabled prices will be saved but not used for calculations (based on your business rules).
                                    </div>
                                </div>

                                <div class="text-end mt-8">
                                    <button type="reset" class="btn btn-warning me-3 btn-sm">
                                        <i class="ki-solid ki-arrows-circle fs-3 me-2"></i>
                                        Reset
                                    </button>
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">
                                        <i class="fa-regular fa-floppy-disk fs-3 me-2"></i>
                                        Save Prices
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
        /**
         * IMPORTANT:
         * This page expects a JSON endpoint:
         *  route('fuel-unit-price.station-fuel-types', stationUuid)
         *
         * Response shape (example):
         * {
         *   fuelTypes: [{uuid, name, code, rating_value}, ...],
         *   prices: [{fuel_type_uuid, price_per_unit, is_active}, ...] // optional existing records
         * }
         */

        // --- Validation helpers ---
        function clearInvalid(input) {
            input.classList.remove('is-invalid');
            let next = input.nextElementSibling;
            if (next && next.classList.contains('invalid-feedback')) next.remove();
        }

        function validateInput(input) {
            clearInvalid(input);

            const value = (input.value ?? '').trim();
            const requiredMsg = input.getAttribute('data-kt-validate-required');
            const pattern = input.getAttribute('data-kt-validate-pattern');
            const patternMsg = input.getAttribute('data-kt-validate-pattern-msg');

            let errorMsg = null;

            if (!value && requiredMsg) {
                errorMsg = requiredMsg;
            } else if (pattern && value && !(new RegExp(pattern).test(value))) {
                errorMsg = patternMsg;
            }

            if (errorMsg) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.classList.add('invalid-feedback');
                errorDiv.innerText = errorMsg;
                input.after(errorDiv);
                return false;
            }
            return true;
        }

        function validateSelect(select) {
            clearInvalid(select);

            const value = select.value;
            const requiredMsg = select.getAttribute('data-kt-validate-required');

            if (!value && requiredMsg) {
                select.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.classList.add('invalid-feedback');
                errorDiv.innerText = requiredMsg;
                select.after(errorDiv);
                return false;
            }
            return true;
        }

        // --- Fuel types rendering ---
        const stationSelect = document.getElementById('fuel_station_uuid');
        const wrapper = document.getElementById('fuelTypesWrapper');
        const tbody = document.getElementById('fuelTypesTbody');

        function renderRows(fuelTypes, pricesArr) {
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
                const isActive = existing ? !!existing.is_active : true;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <div class="fw-semibold">
                            ${ft.name} <span class="text-muted">(${ft.code})</span>
                        </div>
                        <div class="text-muted fs-8">Rating: ${ft.rating_value ?? 'N/A'}</div>
                    </td>

                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">$</span>

                            <input
                                type="number"
                                name="prices[${ft.uuid}][price_per_unit]"
                                class="form-control form-control-solid price-input"
                                placeholder="0.00"
                                value="${priceVal}"
                                step="0.01"
                                min="0"
                                required
                                data-kt-validate="true"
                                data-kt-validate-required="Price is required"
                                data-kt-validate-pattern="^\\d+(\\.\\d{1,2})?$"
                                data-kt-validate-pattern-msg="Enter a valid price with up to 2 decimal places"
                            />
                        </div>
                        <div class="form-text">Per unit price for this fuel type</div>
                    </td>

                    <td>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            {{-- Important: ensures unchecked submits 0 --}}
                            <input type="hidden" name="prices[${ft.uuid}][is_active]" value="0" />
                            <input
                                class="form-check-input status-toggle"
                                type="checkbox"
                                name="prices[${ft.uuid}][is_active]"
                                value="1"
                                ${isActive ? 'checked' : ''}
                            />
                            <label class="form-check-label text-muted">Enable</label>
                        </div>
                    </td>
                `;

                tbody.appendChild(tr);
            });

            // Attach live validation to dynamically created inputs
            document.querySelectorAll('#fuel_unit_price_form [data-kt-validate="true"]').forEach(input => {
                if (input.dataset._bound === '1') return; // prevent duplicate handlers
                input.dataset._bound = '1';

                if (input.tagName === 'SELECT') {
                    input.addEventListener('change', () => validateSelect(input));
                } else {
                    input.addEventListener('input', () => validateInput(input));
                }
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
                renderRows(data.fuelTypes || [], data.prices || []);
                wrapper.style.display = 'block';
            } catch (e) {
                wrapper.style.display = 'none';
                tbody.innerHTML = '';
                Swal.fire('Error', 'Failed to load fuel types for this station.', 'error');
            }
        }

        // station change
        stationSelect.addEventListener('change', (e) => {
            loadFuelTypes(e.target.value);
        });

        // if old station exists (validation back)
        if (stationSelect.value) {
            loadFuelTypes(stationSelect.value);
        }

        // --- Form submit with validation + Swal ---
        document.getElementById('fuel_unit_price_form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const inputs = form.querySelectorAll('[data-kt-validate="true"]');

            let firstInvalid = null;

            // Clear previous errors
            inputs.forEach(input => clearInvalid(input));

            // Validate all
            for (const input of inputs) {
                let isValid = true;
                if (input.tagName === 'SELECT') isValid = validateSelect(input);
                else isValid = validateInput(input);

                if (!isValid) {
                    firstInvalid = firstInvalid || input;
                    await Swal.fire('Validation Error', input.nextElementSibling?.innerText || 'Please check the form.', 'warning');
                    break;
                }
            }

            // extra check: must have station AND at least one row rendered
            if (!firstInvalid) {
                const stationOk = !!stationSelect.value;
                const hasRows = tbody.querySelectorAll('tr').length > 0;

                if (!stationOk) {
                    firstInvalid = stationSelect;
                    await Swal.fire('Validation Error', 'Fuel Station selection is required', 'warning');
                } else if (!hasRows) {
                    await Swal.fire('Validation Error', 'No fuel types loaded. Please select a valid station.', 'warning');
                    return;
                }
            }

            if (firstInvalid) {
                firstInvalid.focus();
                return;
            }

            const result = await Swal.fire({
                title: 'Confirm Save',
                text: 'Are you sure you want to save these fuel unit prices?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, save',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                form.submit();
            }
        });

        // --- Reset form handler ---
        document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Reset Form?',
                text: 'All entered data will be cleared.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reset it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('fuel_unit_price_form').reset();

                    // clear validation errors
                    document.querySelectorAll('#fuel_unit_price_form .is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('#fuel_unit_price_form .invalid-feedback').forEach(el => el.remove());

                    // hide table
                    wrapper.style.display = 'none';
                    tbody.innerHTML = '';
                }
            });
        });
    </script>
@endpush
