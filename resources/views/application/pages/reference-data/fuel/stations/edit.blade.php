@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="row">
                <div class="col-md-12">
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

                    <div class="card card-flush shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light-primary">
                            <h3 class="card-title">
                                <i class="ki-duotone ki-home-3 fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Edit Fuel Station
                            </h3>
                            <a href="{{ route('fuel-station.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left fs-3 me-2"></i>
                                Back to List
                            </a>
                        </div>

                        <div class="card-body">
                            <form id="fuel_station_form" method="POST"
                                action="{{ route('fuel-station.update', $fuelStation->uuid) }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <!-- Left column -->
                                    <div class="col-md-6">
                                        <!-- Station Name -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-home-3 fs-2 me-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Station Name
                                            </label>
                                            <input type="text" name="name" class="form-control form-control-solid"
                                                placeholder="Enter fuel station name"
                                                value="{{ old('name', $fuelStation->name) }}"
                                                data-kt-validate="true"
                                                data-kt-validate-required="Station name is required"
                                                data-kt-validate-pattern="^[a-zA-Z0-9\s\-&]{3,100}$"
                                                data-kt-validate-pattern-msg="Only letters, numbers, spaces, hyphens and ampersands, 3 to 100 characters" />
                                            <div class="form-text">Unique fuel station name</div>
                                        </div>

                                        <!-- Location Text (Optional) -->
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-location fs-2 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Location (Optional)
                                            </label>
                                            <input type="text" name="location" class="form-control form-control-solid"
                                                placeholder="Enter location (e.g., Street, Landmark)"
                                                value="{{ old('location', $fuelStation->location) }}"
                                                data-kt-validate="true"
                                                data-kt-validate-pattern="^[a-zA-Z0-9\s\-,\.]{0,255}$"
                                                data-kt-validate-pattern-msg="Only letters, numbers, spaces, commas, dots and hyphens, max 255 characters" />
                                            <div class="form-text">Free text address (optional)</div>
                                        </div>

                                        <!-- Region -->
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-geolocation fs-2 me-2 text-danger">
                                                    <span class="path1"></span><span class="path2"></span>
                                                </i>
                                                Region
                                            </label>
                                            <select id="region_uuid" name="region_uuid" class="form-select form-select-solid">
                                                <option value="">Select Region</option>
                                                @foreach ($regions as $r)
                                                    <option value="{{ $r->uuid }}"
                                                        {{ old('region_uuid', $fuelStation->region_uuid) == $r->uuid ? 'selected' : '' }}>
                                                        {{ $r->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Governorate -->
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-map fs-2 me-2 text-warning">
                                                    <span class="path1"></span><span class="path2"></span>
                                                </i>
                                                Governorate
                                            </label>
                                            <select id="governorate_uuid" name="governorate_uuid" class="form-select form-select-solid">
                                                <option value="">Select Governorate</option>
                                                {{-- options filled by JS --}}
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Right column -->
                                    <div class="col-md-6">
                                        <!-- Center -->
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-flag fs-2 me-2 text-primary">
                                                    <span class="path1"></span><span class="path2"></span>
                                                </i>
                                                Center
                                            </label>
                                            <select id="center_uuid" name="center_uuid" class="form-select form-select-solid">
                                                <option value="">Select Center</option>
                                                {{-- options filled by JS --}}
                                            </select>
                                        </div>

                                        <!-- City -->
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-building fs-2 me-2 text-info">
                                                    <span class="path1"></span><span class="path2"></span>
                                                </i>
                                                City
                                            </label>
                                            <select id="city_uuid" name="city_uuid" class="form-select form-select-solid">
                                                <option value="">Select City</option>
                                                {{-- options filled by JS --}}
                                            </select>
                                        </div>

                                        <!-- Manager -->
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-profile-circle fs-2 me-2 text-warning">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Station Manager
                                            </label>
                                            <select name="user_uuid" class="form-select form-select-solid">
                                                <option value="">Select Manager (Optional)</option>
                                                @foreach ($managers as $manager)
                                                    <option value="{{ $manager->uuid }}"
                                                        {{ old('user_uuid', $fuelStation->user_uuid) == $manager->uuid ? 'selected' : '' }}>
                                                        {{ $manager->name }} ({{ $manager->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Assign a manager to this station</div>
                                        </div>

                                        <!-- Status -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-shield-tick fs-2 me-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Status
                                            </label>
                                            <select name="is_active" class="form-select form-select-solid"
                                                data-kt-validate="true" data-kt-validate-required="Status is required">
                                                <option value="1" {{ old('is_active', $fuelStation->is_active) == '1' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="0" {{ old('is_active', $fuelStation->is_active) == '0' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                            <div class="form-text">Active stations will be available in the system</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mt-8">
                                    <a href="{{ route('fuel-station.index') }}" class="btn btn-light me-3 btn-sm">
                                        <i class="bi bi-x-circle fs-3 me-2"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">
                                        <i class="fa-regular fa-floppy-disk fs-3 me-2"></i>
                                        Update Fuel Station
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
        // Preloaded geo data (fast, no requests)
        const GOVERNORATES = @json($governorates);
        const CENTERS = @json($centers);
        const CITIES = @json($cities);

        const regionSelect = document.getElementById('region_uuid');
        const governorateSelect = document.getElementById('governorate_uuid');
        const centerSelect = document.getElementById('center_uuid');
        const citySelect = document.getElementById('city_uuid');

        const preSelected = {
            governorate_uuid: @json(old('governorate_uuid', $fuelStation->governorate_uuid)),
            center_uuid: @json(old('center_uuid', $fuelStation->center_uuid)),
            city_uuid: @json(old('city_uuid', $fuelStation->city_uuid)),
        };

        function clearOptions(select, placeholder) {
            select.innerHTML = '';
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = placeholder;
            select.appendChild(opt);
        }

        function fillGovernorates(regionUuid) {
            clearOptions(governorateSelect, 'Select Governorate');
            clearOptions(centerSelect, 'Select Center');
            clearOptions(citySelect, 'Select City');

            if (!regionUuid) return;

            const list = GOVERNORATES.filter(g => g.region_uuid === regionUuid);
            list.forEach(g => {
                const opt = document.createElement('option');
                opt.value = g.uuid;
                opt.textContent = g.name;
                if (preSelected.governorate_uuid && preSelected.governorate_uuid === g.uuid) opt.selected = true;
                governorateSelect.appendChild(opt);
            });

            if (preSelected.governorate_uuid) {
                fillCenters(preSelected.governorate_uuid);
            }
        }

        function fillCenters(governorateUuid) {
            clearOptions(centerSelect, 'Select Center');
            clearOptions(citySelect, 'Select City');

            if (!governorateUuid) return;

            const list = CENTERS.filter(c => c.governorate_uuid === governorateUuid);
            list.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.uuid;
                opt.textContent = c.name;
                if (preSelected.center_uuid && preSelected.center_uuid === c.uuid) opt.selected = true;
                centerSelect.appendChild(opt);
            });

            if (preSelected.center_uuid) {
                fillCities(preSelected.center_uuid);
            }
        }

        function fillCities(centerUuid) {
            clearOptions(citySelect, 'Select City');

            if (!centerUuid) return;

            const list = CITIES.filter(ci => ci.center_uuid === centerUuid);
            list.forEach(ci => {
                const opt = document.createElement('option');
                opt.value = ci.uuid;
                opt.textContent = ci.name;
                if (preSelected.city_uuid && preSelected.city_uuid === ci.uuid) opt.selected = true;
                citySelect.appendChild(opt);
            });
        }

        // Change handlers
        regionSelect.addEventListener('change', function() {
            preSelected.governorate_uuid = null;
            preSelected.center_uuid = null;
            preSelected.city_uuid = null;
            fillGovernorates(this.value);
        });

        governorateSelect.addEventListener('change', function() {
            preSelected.center_uuid = null;
            preSelected.city_uuid = null;
            fillCenters(this.value);
        });

        centerSelect.addEventListener('change', function() {
            preSelected.city_uuid = null;
            fillCities(this.value);
        });

        // Init on load
        document.addEventListener('DOMContentLoaded', function() {
            const regionUuid = regionSelect.value;
            fillGovernorates(regionUuid);
        });

        // ================= Existing validation + Swal (kept) =================

        function validateInput(input) {
            input.classList.remove('is-invalid');
            let next = input.nextElementSibling;
            if (next && next.classList.contains('invalid-feedback')) next.remove();

            const value = input.value.trim();
            const requiredMsg = input.getAttribute('data-kt-validate-required');
            const pattern = input.getAttribute('data-kt-validate-pattern');
            const patternMsg = input.getAttribute('data-kt-validate-pattern-msg');

            let errorMsg = null;

            if (!value && requiredMsg) errorMsg = requiredMsg;
            else if (pattern && value && !new RegExp(pattern).test(value)) errorMsg = patternMsg;

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
            select.classList.remove('is-invalid');
            let next = select.nextElementSibling;
            if (next && next.classList.contains('invalid-feedback')) next.remove();

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

        document.querySelectorAll('#fuel_station_form [data-kt-validate="true"]').forEach(input => {
            if (input.tagName === 'SELECT') input.addEventListener('change', () => validateSelect(input));
            else input.addEventListener('input', () => validateInput(input));
        });

        document.getElementById('fuel_station_form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const inputs = form.querySelectorAll('[data-kt-validate="true"]');
            let valid = true;
            let firstInvalid = null;

            inputs.forEach(input => {
                input.classList.remove('is-invalid');
                let next = input.nextElementSibling;
                if (next && next.classList.contains('invalid-feedback')) next.remove();
            });

            for (const input of inputs) {
                const isValid = (input.tagName === 'SELECT') ? validateSelect(input) : validateInput(input);
                if (!isValid) {
                    valid = false;
                    firstInvalid = firstInvalid || input;
                    await Swal.fire('Validation Error', input.nextElementSibling.innerText, 'warning');
                    break;
                }
            }

            if (!valid) {
                if (firstInvalid) firstInvalid.focus();
                return;
            }

            const result = await Swal.fire({
                title: 'Confirm Update',
                text: 'Are you sure you want to update this fuel station?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, update fuel station',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) form.submit();
        });
    </script>
@endpush

@push('scripts')
<script>
    const regionEl = document.getElementById('region_uuid');
    const govEl    = document.getElementById('governorate_uuid');
    const centerEl = document.getElementById('center_uuid');
    const cityEl   = document.getElementById('city_uuid');

    function reset(select, label) {
        select.innerHTML = `<option value="">${label}</option>`;
    }

    async function fetchAndFill(url, select, label, selected = null) {
        reset(select, label);
        const res = await fetch(url);
        const data = await res.json();
        data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.uuid;
            opt.textContent = item.name;
            if (selected && selected === item.uuid) opt.selected = true;
            select.appendChild(opt);
        });
    }

    regionEl.addEventListener('change', async () => {
        reset(govEl, 'Select Governorate');
        reset(centerEl, 'Select Center');
        reset(cityEl, 'Select City');
        if (regionEl.value) {
            await fetchAndFill(`/ajax/geo/governorates/${regionEl.value}`, govEl, 'Select Governorate');
        }
    });

    govEl.addEventListener('change', async () => {
        reset(centerEl, 'Select Center');
        reset(cityEl, 'Select City');
        if (govEl.value) {
            await fetchAndFill(`/ajax/geo/centers/${govEl.value}`, centerEl, 'Select Center');
        }
    });

    centerEl.addEventListener('change', async () => {
        reset(cityEl, 'Select City');
        if (centerEl.value) {
            await fetchAndFill(`/ajax/geo/cities/${centerEl.value}`, cityEl, 'Select City');
        }
    });

    // 🔁 Preload existing values (EDIT PAGE)
    document.addEventListener('DOMContentLoaded', async () => {
        const pre = {
            region: "{{ $fuelStation->region_uuid }}",
            gov: "{{ $fuelStation->governorate_uuid }}",
            center: "{{ $fuelStation->center_uuid }}",
            city: "{{ $fuelStation->city_uuid }}"
        };

        if (pre.region) {
            await fetchAndFill(`/ajax/geo/governorates/${pre.region}`, govEl, 'Select Governorate', pre.gov);
        }
        if (pre.gov) {
            await fetchAndFill(`/ajax/geo/centers/${pre.gov}`, centerEl, 'Select Center', pre.center);
        }
        if (pre.center) {
            await fetchAndFill(`/ajax/geo/cities/${pre.center}`, cityEl, 'Select City', pre.city);
        }
    });
</script>
@endpush

