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
                                <i class="ki-duotone ki-dollar fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Edit Fuel Unit Price
                            </h3>
                            <a href="{{ route('fuel-unit-price.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left fs-3 me-2"></i>
                                Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <form id="fuel_unit_price_form" method="POST" action="{{ route('fuel-unit-price.update', $price->uuid) }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <!-- Left column -->
                                    <div class="col-md-6">
                                        <!-- Pump Selection -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-home-3 fs-2 me-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Fuel Station (Pump)
                                            </label>
                                            <select name="pump_uuid" class="form-select form-select-solid"
                                                data-kt-validate="true" 
                                                data-kt-validate-required="Pump selection is required">
                                                <option value="">Select Pump</option>
                                                @foreach ($pumps as $pump)
                                                    <option value="{{ $pump->uuid }}"
                                                        {{ old('pump_uuid', $price->pump_uuid) == $pump->uuid ? 'selected' : '' }}>
                                                        {{ $pump->name }}
                                                        @if($pump->location)
                                                            - {{ $pump->location }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Select the fuel station for this price</div>
                                        </div>

                                        <!-- Price Per Unit -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-dollar fs-2 me-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Price Per Unit
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="price_per_unit" class="form-control form-control-solid"
                                                    placeholder="Enter price per unit (e.g., 150.00)" 
                                                    value="{{ old('price_per_unit', $price->price_per_unit) }}"
                                                    step="0.01"
                                                    min="0"
                                                    data-kt-validate="true" 
                                                    data-kt-validate-required="Price per unit is required"
                                                    data-kt-validate-pattern="^\d+(\.\d{1,2})?$"
                                                    data-kt-validate-pattern-msg="Enter a valid price with up to 2 decimal places" />
                                            </div>
                                            <div class="form-text">Price per unit of fuel (in your currency)</div>
                                        </div>
                                    </div>

                                    <!-- Right column -->
                                    <div class="col-md-6">
                                        <!-- Fuel Type Selection -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-gas-station fs-2 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Fuel Type
                                            </label>
                                            <select name="fuel_type_uuid" class="form-select form-select-solid"
                                                data-kt-validate="true" 
                                                data-kt-validate-required="Fuel type selection is required">
                                                <option value="">Select Fuel Type</option>
                                                @foreach ($fuelTypes as $fuelType)
                                                    <option value="{{ $fuelType->uuid }}"
                                                        {{ old('fuel_type_uuid', $price->fuel_type_uuid) == $fuelType->uuid ? 'selected' : '' }}>
                                                        {{ $fuelType->name }} ({{ $fuelType->code }})
                                                        - Rating: {{ $fuelType->rating_value }}
                                                        - Unit: {{ $fuelType->defaultUnit->abbreviation ?? 'N/A' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Select the type of fuel</div>
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
                                                <option value="1" {{ old('is_active', $price->is_active) == '1' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="0" {{ old('is_active', $price->is_active) == '0' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                            <div class="form-text">Active prices will be used for calculations</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mt-8">
                                    <a href="{{ route('fuel-unit-price.index') }}" class="btn btn-light me-3 btn-sm">
                                        <i class="bi bi-x-circle fs-3 me-2"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">
                                        <i class="fa-regular fa-floppy-disk fs-3 me-2"></i>
                                        Update Price
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
        document.querySelectorAll('#fuel_unit_price_form [data-kt-validate="true"]').forEach(input => {
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
        document.getElementById('fuel_unit_price_form').addEventListener('submit', async function(e) {
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
                    text: 'Are you sure you want to update this fuel unit price?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update price',
                    cancelButtonText: 'Cancel'
                });

                if (result.isConfirmed) {
                    form.submit();
                }
            } else if (firstInvalid) {
                firstInvalid.focus();
            }
        });
    </script>
@endpush