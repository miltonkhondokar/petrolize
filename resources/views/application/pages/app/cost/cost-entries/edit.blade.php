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
                                <i class="ki-duotone ki-money fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Edit Cost Entry
                            </h3>
                            <a href="{{ route('cost-entries.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left fs-3 me-2"></i>
                                Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <form id="cost_entry_form" method="POST" action="{{ route('cost-entries.update', $costEntry->uuid) }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <!-- Left column -->
                                    <div class="col-md-6">
                                        <!-- Fuel Station -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-home-3 fs-2 me-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Fuel Station
                                            </label>
                                            <select name="fuel_station_uuid" class="form-select form-select-solid"
                                                data-kt-validate="true"
                                                data-kt-validate-required="Fuel station is required">
                                                <option value="">Select Fuel Station</option>
                                                @foreach ($fuelStations as $station)
                                                    <option value="{{ $station->uuid }}"
                                                        {{ old('fuel_station_uuid', $costEntry->fuel_station_uuid) == $station->uuid ? 'selected' : '' }}>
                                                        {{ $station->name }} - {{ $station->location ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Select the fuel station for this cost</div>
                                        </div>

                                        <!-- Cost Category -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-category fs-2 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Cost Category
                                            </label>
                                            <select name="cost_category_uuid" class="form-select form-select-solid"
                                                data-kt-validate="true"
                                                data-kt-validate-required="Cost category is required">
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->uuid }}"
                                                        {{ old('cost_category_uuid', $costEntry->cost_category_uuid) == $category->uuid ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Select the cost category</div>
                                        </div>
                                    </div>

                                    <!-- Right column -->
                                    <div class="col-md-6">
                                        <!-- Expense Date -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-calendar-8 fs-2 me-2 text-warning">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Expense Date
                                            </label>
                                            <input type="date" name="expense_date" class="form-control form-control-solid"
                                                value="{{ old('expense_date', $costEntry->expense_date->format('Y-m-d')) }}"
                                                data-kt-validate="true" 
                                                data-kt-validate-required="Expense date is required" />
                                            <div class="form-text">Date when the expense occurred</div>
                                        </div>

                                        <!-- Amount -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-dollar fs-2 me-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Amount
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="amount" class="form-control form-control-solid"
                                                    placeholder="0.00" step="0.01" min="0.01"
                                                    value="{{ old('amount', $costEntry->amount) }}"
                                                    data-kt-validate="true" 
                                                    data-kt-validate-required="Amount is required"
                                                    data-kt-validate-pattern="^\d+(\.\d{1,2})?$"
                                                    data-kt-validate-pattern-msg="Please enter a valid amount (e.g., 100.50)" />
                                            </div>
                                            <div class="form-text">Enter the cost amount</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reference Number -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-document fs-2 me-2 text-danger">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Reference Number
                                            </label>
                                            <input type="text" name="reference_no" class="form-control form-control-solid"
                                                placeholder="Enter reference number (optional)" 
                                                value="{{ old('reference_no', $costEntry->reference_no) }}"
                                                data-kt-validate="true" 
                                                data-kt-validate-pattern="^[a-zA-Z0-9\-\/]{0,100}$"
                                                data-kt-validate-pattern-msg="Only letters, numbers, hyphens and slashes, max 100 characters" />
                                            <div class="form-text">Invoice or reference number</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
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
                                                <option value="1" {{ old('is_active', $costEntry->is_active) == '1' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="0" {{ old('is_active', $costEntry->is_active) == '0' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                            <div class="form-text">Active entries will be included in reports</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Note -->
                                <div class="mb-5">
                                    <label class="form-label">
                                        <i class="ki-duotone ki-notes fs-2 me-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Note
                                    </label>
                                    <textarea name="note" class="form-control form-control-solid" rows="3"
                                        placeholder="Enter any additional notes (optional)"
                                        data-kt-validate="true" 
                                        data-kt-validate-pattern="^.{0,500}$"
                                        data-kt-validate-pattern-msg="Maximum 500 characters allowed">{{ old('note', $costEntry->note) }}</textarea>
                                    <div class="form-text">Additional details about this cost entry</div>
                                </div>

                                <div class="text-end mt-8">
                                    <a href="{{ route('cost-entries.index') }}" class="btn btn-light me-3 btn-sm">
                                        <i class="bi bi-x-circle fs-3 me-2"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">
                                        <i class="fa-regular fa-floppy-disk fs-3 me-2"></i>
                                        Update Cost Entry
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

        // Validate textarea
        function validateTextarea(textarea) {
            return validateInput(textarea);
        }

        // Attach live validation handlers
        document.querySelectorAll('#cost_entry_form [data-kt-validate="true"]').forEach(input => {
            if (input.tagName === 'SELECT') {
                input.addEventListener('change', () => {
                    validateSelect(input);
                });
            } else if (input.tagName === 'TEXTAREA') {
                input.addEventListener('input', () => {
                    validateTextarea(input);
                });
            } else {
                input.addEventListener('input', () => {
                    validateInput(input);
                });
            }
        });

        // Submit handler with validation + Swal
        document.getElementById('cost_entry_form').addEventListener('submit', async function(e) {
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

            // Validate all inputs, selects, and textareas
            for (const input of inputs) {
                let isValid;
                if (input.tagName === 'SELECT') {
                    isValid = validateSelect(input);
                } else if (input.tagName === 'TEXTAREA') {
                    isValid = validateTextarea(input);
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
                    title: 'Confirm Cost Entry Update',
                    text: 'Are you sure you want to update this cost entry?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update entry',
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