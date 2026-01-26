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
                                <i class="ki-duotone ki-water fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Create Fuel Station Capacity
                            </h3>
                            <a href="{{ route('fuel-capacity.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left fs-3 me-2"></i>
                                Back to List
                            </a>
                        </div>

                        <div class="card-body">
                            <form id="fuel_capacity_form" method="POST" action="{{ route('fuel-capacity.store') }}">
                                @csrf

                                {{-- Effective From Date --}}
                                <div class="row mb-6">
                                    <div class="col-md-6">
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-calendar-8 fs-2 me-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Effective From Date
                                            </label>
                                            <input
                                                type="date"
                                                name="effective_from"
                                                id="effective_from"
                                                class="form-control form-control-solid"
                                                value="{{ old('effective_from', now()->format('Y-m-d')) }}"
                                                required
                                                data-kt-validate="true"
                                                data-kt-validate-required="Effective date is required"
                                            />
                                            <div class="form-text">Date when this capacity becomes effective</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-information-5 fs-2 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Note (Optional)
                                            </label>
                                            <textarea
                                                name="note"
                                                id="note"
                                                class="form-control form-control-solid"
                                                rows="2"
                                                placeholder="Add any notes about this capacity..."
                                            >{{ old('note') }}</textarea>
                                            <div class="form-text">Additional information about this capacity record</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Station Selection --}}
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

                                            <div class="form-text">Select the fuel station, then set capacities for fuel types</div>
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
                                                    You can set <b>capacity in liters</b> and <b>enable/disable</b> each type.
                                                    Capacity with 3 decimal places precision.
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
                                            Fuel Types & Capacities
                                        </h4>
                                        <small class="text-muted">Set capacity and enable/disable per fuel type</small>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3">
                                            <thead>
                                                <tr class="fw-bold text-muted">
                                                    <th>Fuel Type</th>
                                                    <th style="width:280px;">Capacity (Liters)</th>
                                                    <th style="width:140px;">Enabled</th>
                                                </tr>
                                            </thead>
                                            <tbody id="fuelTypesTbody"></tbody>
                                        </table>
                                    </div>

                                    <div class="text-muted fs-8 mt-2">
                                        Note: Disabled capacities will be saved but not used for calculations.
                                    </div>
                                </div>

                                <div class="text-end mt-8">
                                    <button type="reset" class="btn btn-warning me-3 btn-sm">
                                        <i class="ki-solid ki-arrows-circle fs-3 me-2"></i>
                                        Reset
                                    </button>
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">
                                        <i class="fa-regular fa-floppy-disk fs-3 me-2"></i>
                                        Save Capacities
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
         *  route('fuel-capacity.station-fuel-types', {stationUuid: stationUuid})
         *
         * Response shape (example):
         * {
         *   fuelTypes: [{uuid, name, code, rating_value}, ...],
         *   capacities: [{fuel_type_uuid, capacity_liters, is_active}, ...] // optional existing records for this effective date
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
        const effectiveDateInput = document.getElementById('effective_from');
        const wrapper = document.getElementById('fuelTypesWrapper');
        const tbody = document.getElementById('fuelTypesTbody');

        function renderRows(fuelTypes, capacitiesArr) {
            const capacitiesMap = {};
            (capacitiesArr || []).forEach(p => capacitiesMap[p.fuel_type_uuid] = p);

            tbody.innerHTML = '';

            if (!fuelTypes || fuelTypes.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="3" class="text-center text-muted py-5">No active fuel types found.</td>`;
                tbody.appendChild(tr);
                return;
            }

            fuelTypes.forEach(ft => {
                const existing = capacitiesMap[ft.uuid] || null;

                const capacityVal = existing ? (existing.capacity_liters ?? '') : '';
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
                            <span class="input-group-text">L</span>

                            <input
                                type="number"
                                name="capacities[${ft.uuid}][capacity_liters]"
                                class="form-control form-control-solid capacity-input"
                                placeholder="0.000"
                                value="${capacityVal}"
                                step="0.001"
                                min="0"
                                required
                                data-kt-validate="true"
                                data-kt-validate-required="Capacity is required"
                                data-kt-validate-pattern="^\\d+(\\.\\d{1,3})?$"
                                data-kt-validate-pattern-msg="Enter a valid capacity with up to 3 decimal places"
                            />
                        </div>
                        <div class="form-text">Capacity in liters for this fuel type</div>
                    </td>

                    <td>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input type="hidden" name="capacities[${ft.uuid}][is_active]" value="0" />
                            <input
                                class="form-check-input status-toggle"
                                type="checkbox"
                                name="capacities[${ft.uuid}][is_active]"
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
            document.querySelectorAll('#fuel_capacity_form [data-kt-validate="true"]').forEach(input => {
                if (input.dataset._bound === '1') return; // prevent duplicate handlers
                input.dataset._bound = '1';

                if (input.tagName === 'SELECT') {
                    input.addEventListener('change', () => validateSelect(input));
                } else {
                    input.addEventListener('input', () => validateInput(input));
                }
            });
        }

        async function loadFuelTypes(stationUuid, effectiveDate) {
            if (!stationUuid || !effectiveDate) {
                wrapper.style.display = 'none';
                tbody.innerHTML = '';
                return;
            }

            const url = "{{ route('fuel-capacity.station-fuel-types') }}";
            const params = new URLSearchParams({
                station_uuid: stationUuid,
                effective_from: effectiveDate
            });

            try {
                const res = await fetch(`${url}?${params}`, { 
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('Failed to load fuel types');

                const data = await res.json();
                renderRows(data.fuelTypes || [], data.capacities || []);
                wrapper.style.display = 'block';
            } catch (e) {
                wrapper.style.display = 'none';
                tbody.innerHTML = '';
                Swal.fire('Error', 'Failed to load fuel types for this station.', 'error');
            }
        }

        // station change
        stationSelect.addEventListener('change', (e) => {
            if (effectiveDateInput.value) {
                loadFuelTypes(e.target.value, effectiveDateInput.value);
            }
        });

        // effective date change
        effectiveDateInput.addEventListener('change', (e) => {
            if (stationSelect.value) {
                loadFuelTypes(stationSelect.value, e.target.value);
            }
        });

        // if old station exists (validation back)
        if (stationSelect.value && effectiveDateInput.value) {
            loadFuelTypes(stationSelect.value, effectiveDateInput.value);
        }

        // --- Form submit with validation + Swal ---
        document.getElementById('fuel_capacity_form').addEventListener('submit', async function(e) {
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

            // extra check: must have station, effective date AND at least one row rendered
            if (!firstInvalid) {
                const stationOk = !!stationSelect.value;
                const dateOk = !!effectiveDateInput.value;
                const hasRows = tbody.querySelectorAll('tr').length > 0;

                if (!stationOk) {
                    firstInvalid = stationSelect;
                    await Swal.fire('Validation Error', 'Fuel Station selection is required', 'warning');
                } else if (!dateOk) {
                    firstInvalid = effectiveDateInput;
                    await Swal.fire('Validation Error', 'Effective date is required', 'warning');
                } else if (!hasRows) {
                    await Swal.fire('Validation Error', 'No fuel types loaded. Please select a valid station and date.', 'warning');
                    return;
                }
            }

            if (firstInvalid) {
                firstInvalid.focus();
                return;
            }

            const result = await Swal.fire({
                title: 'Confirm Save',
                text: 'Are you sure you want to save these fuel capacities?',
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
                    document.getElementById('fuel_capacity_form').reset();

                    // clear validation errors
                    document.querySelectorAll('#fuel_capacity_form .is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('#fuel_capacity_form .invalid-feedback').forEach(el => el.remove());

                    // hide table
                    wrapper.style.display = 'none';
                    tbody.innerHTML = '';
                }
            });
        });
    </script>
@endpush