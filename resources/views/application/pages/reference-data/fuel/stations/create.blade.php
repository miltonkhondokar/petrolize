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
                            Create New Fuel Station
                        </h3>
                        <a href="{{ route('fuel-station.index') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-arrow-left fs-3 me-2"></i>
                            Back to List
                        </a>
                    </div>

                    <div class="card-body">
                        <form id="fuel_station_form" method="POST" action="{{ route('fuel-station.store') }}">
                            @csrf

                            <div class="row">
                                {{-- Left column --}}
                                <div class="col-md-6">

                                    {{-- Station Name --}}
                                    <div class="mb-5">
                                        <label class="form-label required">
                                            <i class="ki-duotone ki-home-3 fs-2 me-2 text-primary"></i>
                                            Station Name
                                        </label>
                                        <input type="text" name="name" class="form-control form-control-solid"
                                               placeholder="Enter fuel station name"
                                               value="{{ old('name') }}"
                                               data-kt-validate="true"
                                               data-kt-validate-required="Station name is required"
                                               data-kt-validate-pattern="^[a-zA-Z0-9\s\-&]{3,100}$"
                                               data-kt-validate-pattern-msg="Only letters, numbers, spaces, hyphens and ampersands, 3 to 100 characters" />
                                        <div class="form-text">Unique fuel station name</div>
                                    </div>

                                    {{-- Location text --}}
                                    <div class="mb-5">
                                        <label class="form-label">
                                            <i class="ki-duotone ki-location fs-2 me-2 text-info"></i>
                                            Location (text)
                                        </label>
                                        <input type="text" name="location" class="form-control form-control-solid"
                                               placeholder="Enter location (e.g., City, Street)"
                                               value="{{ old('location') }}"
                                               data-kt-validate="true"
                                               data-kt-validate-pattern="^[a-zA-Z0-9\s\-,\.]{0,255}$"
                                               data-kt-validate-pattern-msg="Only letters, numbers, spaces, commas, dots and hyphens, max 255 characters" />
                                        <div class="form-text">Optional free-text location</div>
                                    </div>

                                    {{-- Geo --}}
                                    <div class="mb-5">
                                        <label class="form-label">
                                            <i class="ki-duotone ki-geolocation fs-2 me-2 text-danger"></i>
                                            Region
                                        </label>
                                        <select name="region_uuid" id="region_uuid" class="form-select form-select-solid">
                                            <option value="">Select Region (Optional)</option>
                                            @foreach($regions as $r)
                                                <option value="{{ $r->uuid }}" {{ old('region_uuid') == $r->uuid ? 'selected' : '' }}>
                                                    {{ $r->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-5">
                                        <label class="form-label">
                                            <i class="ki-duotone ki-map fs-2 me-2 text-warning"></i>
                                            Governorate
                                        </label>
                                        <select name="governorate_uuid" id="governorate_uuid" class="form-select form-select-solid">
                                            <option value="">Select Governorate (Optional)</option>
                                            @foreach($governorates as $g)
                                                <option value="{{ $g->uuid }}"
                                                    data-region="{{ $g->region_uuid }}"
                                                    {{ old('governorate_uuid') == $g->uuid ? 'selected' : '' }}>
                                                    {{ $g->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>

                                {{-- Right column --}}
                                <div class="col-md-6">

                                    <div class="mb-5">
                                        <label class="form-label">
                                            <i class="ki-duotone ki-compass fs-2 me-2 text-primary"></i>
                                            Center
                                        </label>
                                        <select name="center_uuid" id="center_uuid" class="form-select form-select-solid">
                                            <option value="">Select Center (Optional)</option>
                                            @foreach($centers as $c)
                                                <option value="{{ $c->uuid }}"
                                                    data-governorate="{{ $c->governorate_uuid }}"
                                                    {{ old('center_uuid') == $c->uuid ? 'selected' : '' }}>
                                                    {{ $c->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-5">
                                        <label class="form-label">
                                            <i class="ki-duotone ki-route fs-2 me-2 text-info"></i>
                                            City
                                        </label>
                                        <select name="city_uuid" id="city_uuid" class="form-select form-select-solid">
                                            <option value="">Select City (Optional)</option>
                                            @foreach($cities as $ci)
                                                <option value="{{ $ci->uuid }}"
                                                    data-center="{{ $ci->center_uuid }}"
                                                    {{ old('city_uuid') == $ci->uuid ? 'selected' : '' }}>
                                                    {{ $ci->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Manager --}}
                                    <div class="mb-5">
                                        <label class="form-label">
                                            <i class="ki-duotone ki-profile-circle fs-2 me-2 text-warning"></i>
                                            Station Manager
                                        </label>
                                        <select name="user_uuid" class="form-select form-select-solid">
                                            <option value="">Select Manager (Optional)</option>
                                            @foreach ($managers as $manager)
                                                <option value="{{ $manager->uuid }}" {{ old('user_uuid') == $manager->uuid ? 'selected' : '' }}>
                                                    {{ $manager->name }} ({{ $manager->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Status --}}
                                    <div class="mb-5">
                                        <label class="form-label required">
                                            <i class="ki-duotone ki-shield-tick fs-2 me-2 text-success"></i>
                                            Status
                                        </label>
                                        <select name="is_active" class="form-select form-select-solid"
                                                data-kt-validate="true"
                                                data-kt-validate-required="Status is required">
                                            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        <div class="form-text">Active stations will be available in the system</div>
                                    </div>

                                </div>
                            </div>

                            <div class="text-end mt-8">
                                <button type="reset" class="btn btn-warning me-3 btn-sm">
                                    <i class="ki-solid ki-arrows-circle fs-3 me-2"></i>
                                    Reset
                                </button>
                                <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">
                                    <i class="fa-regular fa-floppy-disk fs-3 me-2"></i>
                                    Create Fuel Station
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
    // Dependent dropdown helpers
    const regionEl = document.getElementById('region_uuid');
    const govEl = document.getElementById('governorate_uuid');
    const centerEl = document.getElementById('center_uuid');
    const cityEl = document.getElementById('city_uuid');

    function resetSelect(selectEl, keepFirst = true) {
        [...selectEl.options].forEach((opt, idx) => {
            if (keepFirst && idx === 0) return;
            opt.hidden = false;
        });
    }

    function filterGovernorates() {
        const region = regionEl.value;
        resetSelect(govEl);
        resetSelect(centerEl);
        resetSelect(cityEl);
        if (!region) return;
        [...govEl.options].forEach((opt, idx) => {
            if (idx === 0) return;
            opt.hidden = opt.dataset.region !== region;
        });
    }

    function filterCenters() {
        const gov = govEl.value;
        resetSelect(centerEl);
        resetSelect(cityEl);
        if (!gov) return;
        [...centerEl.options].forEach((opt, idx) => {
            if (idx === 0) return;
            opt.hidden = opt.dataset.governorate !== gov;
        });
    }

    function filterCities() {
        const center = centerEl.value;
        resetSelect(cityEl);
        if (!center) return;
        [...cityEl.options].forEach((opt, idx) => {
            if (idx === 0) return;
            opt.hidden = opt.dataset.center !== center;
        });
    }

    regionEl?.addEventListener('change', () => {
        govEl.value = '';
        centerEl.value = '';
        cityEl.value = '';
        filterGovernorates();
    });

    govEl?.addEventListener('change', () => {
        centerEl.value = '';
        cityEl.value = '';
        filterCenters();
    });

    centerEl?.addEventListener('change', () => {
        cityEl.value = '';
        filterCities();
    });

    // initial apply
    filterGovernorates();
    filterCenters();
    filterCities();
</script>

{{-- keep your existing validation + swal logic (unchanged) --}}
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
