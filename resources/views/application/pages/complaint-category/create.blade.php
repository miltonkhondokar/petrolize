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
                                <i class="ki-duotone ki-exclamation-triangle fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Create New Fuel Station Complaint
                            </h3>
                            <a href="{{ route('complaint-category.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left fs-3 me-2"></i>
                                Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <form id="complaint_form" method="POST" action="{{ route('complaint-category.store') }}">
                                @csrf
                                <div class="row">
                                    <!-- Left column -->
                                    <div class="col-md-6">
                                        <!-- Fuel Station Selection -->
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
                                                data-kt-validate-required="Fuel Station selection is required">
                                                <option value="">Select Fuel Station</option>
                                                @foreach ($fuelStations as $fuelStation)
                                                    <option value="{{ $fuelStation->uuid }}"
                                                        {{ old('fuel_station_uuid') == $fuelStation->uuid ? 'selected' : '' }}>
                                                        {{ $fuelStation->name }}
                                                        @if($fuelStation->location)
                                                            - {{ $fuelStation->location }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Select the fuel station with the issue</div>
                                        </div>

                                        <!-- Category -->
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-category fs-2 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Category
                                            </label>
                                            <select name="category" class="form-select form-select-solid">
                                                <option value="">Select Category (Optional)</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category }}"
                                                        {{ old('category') == $category ? 'selected' : '' }}>
                                                        {{ ucwords(str_replace('_', ' ', $category)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Type of complaint</div>
                                        </div>

                                        <!-- Title -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-note fs-2 me-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Complaint Title
                                            </label>
                                            <input type="text" name="title" class="form-control form-control-solid"
                                                placeholder="Enter complaint title (e.g., Fuel Nozzle Not Working)" 
                                                value="{{ old('title') }}"
                                                data-kt-validate="true" 
                                                data-kt-validate-required="Complaint title is required"
                                                data-kt-validate-pattern="^[a-zA-Z0-9\s\-\.,!?]{5,255}$"
                                                data-kt-validate-pattern-msg="Only letters, numbers, spaces and basic punctuation, 5 to 255 characters" />
                                            <div class="form-text">Brief description of the complaint</div>
                                        </div>
                                    </div>

                                    <!-- Right column -->
                                    <div class="col-md-6">
                                        <!-- Status -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-status fs-2 me-2 text-warning">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Status
                                            </label>
                                            <select name="status" class="form-select form-select-solid"
                                                data-kt-validate="true"
                                                data-kt-validate-required="Status is required">
                                                <option value="open" {{ old('status') == 'open' ? 'selected' : '' }}>
                                                    Open
                                                </option>
                                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>
                                                    In Progress
                                                </option>
                                                <option value="resolved" {{ old('status') == 'resolved' ? 'selected' : '' }}>
                                                    Resolved
                                                </option>
                                            </select>
                                            <div class="form-text">Current status of the complaint</div>
                                        </div>

                                        <!-- Complaint Date -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-calendar-8 fs-2 me-2 text-danger">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Complaint Date
                                            </label>
                                            <input type="date" name="complaint_date" class="form-control form-control-solid"
                                                value="{{ old('complaint_date', date('Y-m-d')) }}"
                                                data-kt-validate="true" 
                                                data-kt-validate-required="Complaint date is required" />
                                            <div class="form-text">Date when the complaint was reported</div>
                                        </div>

                                        <!-- Resolved Date -->
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-calendar-check fs-2 me-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Resolved Date
                                            </label>
                                            <input type="date" name="resolved_date" class="form-control form-control-solid"
                                                value="{{ old('resolved_date') }}" />
                                            <div class="form-text">Date when the complaint was resolved (if applicable)</div>
                                        </div>

                                        <!-- Active Status -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-shield-tick fs-2 me-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Active Status
                                            </label>
                                            <select name="is_active" class="form-select form-select-solid"
                                                data-kt-validate="true"
                                                data-kt-validate-required="Active status is required">
                                                <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                            <div class="form-text">Active complaints will be shown in reports</div>
                                        </div>
                                    </div>

                                    <!-- Full width for description -->
                                    <div class="col-md-12">
                                        <!-- Description -->
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-note-2 fs-2 me-2 text-warning">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Description
                                            </label>
                                            <textarea name="description" class="form-control form-control-solid" 
                                                rows="5" placeholder="Enter detailed description of the complaint (optional)"
                                                data-kt-validate="true" 
                                                data-kt-validate-pattern="^[a-zA-Z0-9\s\-\.,!?\n]{0,1000}$"
                                                data-kt-validate-pattern-msg="Only letters, numbers, spaces and basic punctuation, max 1000 characters">{{ old('description') }}</textarea>
                                            <div class="form-text">Detailed information about the complaint</div>
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
                                        Create Complaint
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

        // Validate textarea fields
        function validateTextarea(textarea) {
            // Clear previous error
            textarea.classList.remove('is-invalid');
            let next = textarea.nextElementSibling;
            if (next && next.classList.contains('invalid-feedback')) {
                next.remove();
            }

            const value = textarea.value.trim();
            const pattern = textarea.getAttribute('data-kt-validate-pattern');
            const patternMsg = textarea.getAttribute('data-kt-validate-pattern-msg');

            let errorMsg = null;

            if (pattern && value && !new RegExp(pattern).test(value)) {
                errorMsg = patternMsg;
            }

            if (errorMsg) {
                textarea.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.classList.add('invalid-feedback');
                errorDiv.innerText = errorMsg;
                textarea.after(errorDiv);
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
        document.querySelectorAll('#complaint_form [data-kt-validate="true"]').forEach(input => {
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
        document.getElementById('complaint_form').addEventListener('submit', async function(e) {
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
                } else if (input.tagName === 'TEXTAREA') {
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

            // Validate all inputs
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
                    title: 'Confirm Creation',
                    text: 'Are you sure you want to create this fuel station complaint?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, create complaint',
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
            document.querySelectorAll('#complaint_form .is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('#complaint_form .invalid-feedback').forEach(el => {
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
                    document.getElementById('complaint_form').reset();
                }
            });
        });
    </script>
@endpush