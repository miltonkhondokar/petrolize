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
                                <i class="ki-duotone ki-link fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Assign Fuel Types to Fuel Station
                            </h3>
                            <a href="{{ route('fuel-station-fuel-type.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left fs-3 me-2"></i>
                                Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <form id="assignment_form" method="POST" action="{{ route('fuel-station-fuel-type.store') }}">
                                @csrf
                                <div class="row">
                                    <!-- Left column -->
                                    <div class="col-md-6">
                                        <!-- Fuel Station Selection -->
                                        <div class="mb-10">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-home-3 fs-2 me-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Fuel Station
                                            </label>
                                            <select name="fuel_station_uuid" id="fuel_station_select" 
                                                class="form-select form-select-solid"
                                                data-kt-validate="true" 
                                                data-kt-validate-required="Fuel station selection is required">
                                                <option value="">Select Fuel Station</option>
                                                @foreach ($fuelStations as $station)
                                                    <option value="{{ $station->uuid }}"
                                                        {{ old('fuel_station_uuid') == $station->uuid ? 'selected' : '' }}>
                                                        {{ $station->name }} 
                                                        @if($station->location)
                                                            ({{ $station->location }})
                                                        @endif
                                                        - {{ $station->is_active ? 'Active' : 'Inactive' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Select the fuel station to assign fuel types</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fuel Types Selection -->
                                <div class="separator separator-dashed my-8"></div>
                                
                                <div class="mb-10">
                                    <label class="form-label required fs-4 mb-4">
                                        <i class="ki-duotone ki-gas fs-2 me-2 text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Available Fuel Types
                                    </label>
                                    <div class="form-text mb-4">Select fuel types available at this station</div>
                                    
                                    <div class="row g-6">
                                        @foreach ($fuelTypes as $fuelType)
                                            <div class="col-md-4 col-lg-3">
                                                <div class="form-check form-check-custom form-check-solid border rounded p-4 h-100">
                                                    <input class="form-check-input" type="checkbox" 
                                                        name="fuel_types[{{ $fuelType->uuid }}][is_active]" 
                                                        id="fuel_type_{{ $fuelType->uuid }}"
                                                        value="1"
                                                        {{ old("fuel_types.{$fuelType->uuid}.is_active") ? 'checked' : '' }}>
                                                    <label class="form-check-label w-100" for="fuel_type_{{ $fuelType->uuid }}">
                                                        <div class="fw-bold text-dark">{{ $fuelType->name }}</div>
                                                        <div class="text-muted fs-7">{{ $fuelType->code }}</div>
                                                        @if($fuelType->description)
                                                            <div class="text-muted fs-8 mt-1">{{ Str::limit($fuelType->description, 50) }}</div>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    @if($fuelTypes->isEmpty())
                                        <div class="alert alert-info">
                                            <i class="ki-duotone ki-information-2 fs-2 me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            No active fuel types found. Please create fuel types first.
                                        </div>
                                    @endif
                                </div>

                                <div class="separator separator-dashed my-8"></div>

                                <div class="text-end mt-8">
                                    <button type="reset" class="btn btn-warning me-3 btn-sm">
                                        <i class="ki-solid ki-arrows-circle fs-3 me-2"></i>
                                        Reset
                                    </button>
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">
                                        <i class="fa-regular fa-floppy-disk fs-3 me-2"></i>
                                        Assign Fuel Types
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
        // Validate select fields
        function validateSelect(select) {
            // Clear previous error
            select.classList.remove('is-invalid');
            let next = select.nextElementSibling;
            if (next && next.classList.contains('invalid-feedback')) {
                next.remove();
            }

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

        // Validate checkboxes (at least one selected)
        function validateCheckboxes() {
            const checkboxes = document.querySelectorAll('input[name^="fuel_types"]');
            const atLeastOneChecked = Array.from(checkboxes).some(cb => cb.checked);
            
            if (!atLeastOneChecked) {
                Swal.fire('Validation Error', 'Please select at least one fuel type.', 'warning');
                return false;
            }
            return true;
        }

        // Attach live validation handlers
        document.querySelectorAll('#assignment_form [data-kt-validate="true"]').forEach(input => {
            if (input.tagName === 'SELECT') {
                input.addEventListener('change', () => {
                    validateSelect(input);
                });
            }
        });

        // Submit handler with validation + Swal
        document.getElementById('assignment_form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const inputs = form.querySelectorAll('[data-kt-validate="true"]');
            let valid = true;
            let firstInvalid = null;

            // Clear all previous errors first
            inputs.forEach(input => {
                input.classList.remove('is-invalid');
                let next = input.nextElementSibling;
                if (next && next.classList.contains('invalid-feedback')) {
                    next.remove();
                }
            });

            // Validate all inputs and selects
            for (const input of inputs) {
                let isValid;
                if (input.tagName === 'SELECT') {
                    isValid = validateSelect(input);
                }

                if (!isValid) {
                    valid = false;
                    if (!firstInvalid) firstInvalid = input;
                    await Swal.fire('Validation Error', input.nextElementSibling.innerText, 'warning');
                    break;
                }
            }

            // Validate checkboxes
            if (valid && !validateCheckboxes()) {
                valid = false;
            }

            if (valid) {
                // Confirm before submit
                const result = await Swal.fire({
                    title: 'Confirm Assignment',
                    text: 'Are you sure you want to assign these fuel types to the selected station?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, assign fuel types',
                    cancelButtonText: 'Cancel'
                });

                if (result.isConfirmed) {
                    form.submit();
                }
            } else if (firstInvalid) {
                firstInvalid.focus();
            }
        });

        // Reset form handler
        document.querySelector('button[type="reset"]').addEventListener('click', function() {
            // Clear all validation errors
            document.querySelectorAll('#assignment_form .is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('#assignment_form .invalid-feedback').forEach(el => {
                el.remove();
            });
            
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
                    document.getElementById('assignment_form').reset();
                }
            });
        });

        // Station selection change - show current assignments
        document.getElementById('fuel_station_select').addEventListener('change', function() {
            const stationUuid = this.value;
            // You could implement AJAX here to show current assignments
            // for the selected station
        });
    </script>
@endpush