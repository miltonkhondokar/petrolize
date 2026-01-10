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
                                <i class="ki-duotone ki-gas-station fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Edit Fuel Type
                            </h3>
                            <a href="{{ route('fuel.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left fs-3 me-2"></i>
                                Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <form id="fuel_form" method="POST" action="{{ route('fuel.update', $fuel) }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <!-- Left column -->
                                    <div class="col-md-6">
                                        <!-- Fuel Name -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-gas-station fs-2 me-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Fuel Name
                                            </label>
                                            <input type="text" name="name" class="form-control form-control-solid"
                                                placeholder="Enter fuel name (e.g., Petrol 95, Diesel 50)" 
                                                value="{{ old('name', $fuel->name) }}"
                                                data-kt-validate="true" 
                                                data-kt-validate-required="Fuel name is required"
                                                data-kt-validate-pattern="^[a-zA-Z0-9\s\-]{3,50}$"
                                                data-kt-validate-pattern-msg="Only letters, numbers, spaces and hyphens, 3 to 50 characters" />
                                            <div class="form-text">Unique fuel type name</div>
                                        </div>

                                        <!-- Fuel Code -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-tag fs-2 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Fuel Code
                                            </label>
                                            <input type="text" name="code" class="form-control form-control-solid"
                                                placeholder="Enter unique code (e.g., PET95, DSL50)" 
                                                value="{{ old('code', $fuel->code) }}"
                                                data-kt-validate="true" 
                                                data-kt-validate-required="Fuel code is required"
                                                data-kt-validate-pattern="^[A-Z0-9]{2,10}$"
                                                data-kt-validate-pattern-msg="Uppercase letters and numbers only, 2 to 10 characters" />
                                            <div class="form-text">Unique code for identification</div>
                                        </div>

                                        <!-- Rating Value -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-chart-line fs-2 me-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Rating Value
                                            </label>
                                            <input type="number" name="rating_value" class="form-control form-control-solid"
                                                placeholder="Enter rating value (e.g., 95, 50)" 
                                                value="{{ old('rating_value', $fuel->rating_value) }}"
                                                data-kt-validate="true" 
                                                data-kt-validate-required="Rating value is required"
                                                data-kt-validate-pattern="^[0-9]{1,4}$"
                                                data-kt-validate-pattern-msg="Numbers only, 1-4 digits" />
                                            <div class="form-text">RON for Petrol, Cetane for Diesel</div>
                                        </div>
                                    </div>

                                    <!-- Right column -->
                                    <div class="col-md-6">
                                        <!-- Fuel Unit -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-ruler fs-2 me-2 text-warning">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Default Unit
                                            </label>
                                            <select name="fuel_unit_uuid" class="form-select form-select-solid"
                                                data-kt-validate="true" 
                                                data-kt-validate-required="Fuel unit is required">
                                                <option value="">Select Fuel Unit</option>
                                                @foreach ($fuelUnits as $unit)
                                                    <option value="{{ $unit->uuid }}"
                                                        {{ old('fuel_unit_uuid', $fuel->fuel_unit_uuid) == $unit->uuid ? 'selected' : '' }}>
                                                        {{ $unit->name }} ({{ $unit->symbol }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Measurement unit for this fuel type</div>
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
                                                data-kt-validate="true"
                                                data-kt-validate-required="Status is required">
                                                <option value="1" {{ old('is_active', $fuel->is_active) == '1' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="0" {{ old('is_active', $fuel->is_active) == '0' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                            <div class="form-text">Active fuel types will be available in pumps</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mt-8">
                                    <button type="reset" class="btn btn-warning me-3 btn-sm">
                                        <i class="ki-solid ki-arrows-circle fs-3 me-2"></i>
                                        Reset Changes
                                    </button>
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">
                                        <i class="fa-regular fa-floppy-disk fs-3 me-2"></i>
                                        Update Fuel Type
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
        // Validate a single input field live or on submit
        function validateInput(input) {
            // Clear previous error
            input.classList.remove('is-invalid');
            let next = input.nextElementSibling;
            if (next && next.classList.contains('invalid-feedback')) {
                next.remove();
            }

            const value = input.value.trim();
            const requiredMsg = input.getAttribute('data-kt-validate-required');
            const pattern = input.getAttribute('data-kt-validate-pattern');
            const patternMsg = input.getAttribute('data-kt-validate-pattern-msg');

            let errorMsg = null;

            if (!value && requiredMsg) {
                errorMsg = requiredMsg;
            } else if (pattern && value && !new RegExp(pattern).test(value)) {
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

        // Attach live validation handlers
        document.querySelectorAll('#fuel_form [data-kt-validate="true"]').forEach(input => {
            if (input.tagName === 'SELECT') {
                input.addEventListener('change', () => {
                    validateSelect(input);
                });
            } else {
                input.addEventListener('input', () => {
                    validateInput(input);
                });
            }
        });

        // Submit handler with validation + Swal
        document.getElementById('fuel_form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const inputs = form.querySelectorAll('[data-kt-validate="true"]');
            let valid = true;
            let firstInvalid = null;

            // Clear all previous errors first
            inputs.forEach(input => {
                if (input.tagName === 'SELECT') {
                    input.classList.remove('is-invalid');
                    let next = input.nextElementSibling;
                    if (next && next.classList.contains('invalid-feedback')) {
                        next.remove();
                    }
                } else {
                    input.classList.remove('is-invalid');
                    let next = input.nextElementSibling;
                    if (next && next.classList.contains('invalid-feedback')) {
                        next.remove();
                    }
                }
            });

            // Validate all inputs and selects
            for (const input of inputs) {
                let isValid;
                if (input.tagName === 'SELECT') {
                    isValid = validateSelect(input);
                } else {
                    isValid = validateInput(input);
                }

                if (!isValid) {
                    valid = false;
                    if (!firstInvalid) firstInvalid = input;
                    await Swal.fire('Validation Error', input.nextElementSibling.innerText, 'warning');
                    break;
                }
            }

            if (valid) {
                // Confirm before submit
                const result = await Swal.fire({
                    title: 'Confirm Update',
                    text: 'Are you sure you want to update this fuel type?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update fuel type',
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
        document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Reset Changes?',
                text: 'All unsaved changes will be lost.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reset!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Reset to original values
                    document.querySelector('input[name="name"]').value = "{{ $fuel->name }}";
                    document.querySelector('input[name="code"]').value = "{{ $fuel->code }}";
                    document.querySelector('input[name="rating_value"]').value = "{{ $fuel->rating_value }}";
                    document.querySelector('select[name="fuel_unit_uuid"]').value = "{{ $fuel->fuel_unit_uuid }}";
                    document.querySelector('select[name="is_active"]').value = "{{ $fuel->is_active }}";
                    
                    // Clear validation errors
                    document.querySelectorAll('#fuel_form .is-invalid').forEach(el => {
                        el.classList.remove('is-invalid');
                    });
                    document.querySelectorAll('#fuel_form .invalid-feedback').forEach(el => {
                        el.remove();
                    });
                    
                    Swal.fire('Reset!', 'Form has been reset to original values.', 'success');
                }
            });
        });
    </script>
@endpush