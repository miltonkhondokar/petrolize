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
                                <i class="ki-duotone ki-map fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Create New Region
                            </h3>
                            <a href="{{ route('regions.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left fs-3 me-2"></i>
                                Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <form id="region_form" method="POST" action="{{ route('regions.store') }}">
                                @csrf
                                <div class="row">
                                    <!-- Left column -->
                                    <div class="col-md-6">
                                        <!-- Region Name -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-map fs-2 me-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Region Name
                                            </label>
                                            <input type="text" name="name" class="form-control form-control-solid"
                                                placeholder="Enter region name (e.g., North Region, Central Region)" 
                                                value="{{ old('name') }}"
                                                data-kt-validate="true" 
                                                data-kt-validate-required="Region name is required"
                                                data-kt-validate-pattern="^[a-zA-Z\s\-&]{3,100}$"
                                                data-kt-validate-pattern-msg="Only letters, spaces, hyphens and ampersands, 3 to 100 characters" />
                                            <div class="form-text">Unique name for the region</div>
                                        </div>
                                    </div>

                                    <!-- Right column -->
                                    <div class="col-md-6">
                                        <!-- Region Code -->
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-code-square fs-2 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Region Code
                                            </label>
                                            <input type="text" name="code" class="form-control form-control-solid"
                                                placeholder="Enter region code (e.g., NR, CR)" 
                                                value="{{ old('code') }}"
                                                data-kt-validate="true"
                                                data-kt-validate-pattern="^[A-Z0-9]{0,10}$"
                                                data-kt-validate-pattern-msg="Only uppercase letters and numbers, max 10 characters" />
                                            <div class="form-text">Unique code for the region (optional, will be auto-uppercased)</div>
                                        </div>
                                    </div>

                                    <!-- Full width for status -->
                                    <div class="col-md-12">
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
                                                <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                            <div class="form-text">Active regions will be available for selection</div>
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
                                        Create Region
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
        document.querySelectorAll('#region_form [data-kt-validate="true"]').forEach(input => {
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
        document.getElementById('region_form').addEventListener('submit', async function(e) {
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

            // Validate all inputs
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
                    title: 'Confirm Creation',
                    text: 'Are you sure you want to create this region?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, create region',
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
            document.querySelectorAll('#region_form .is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('#region_form .invalid-feedback').forEach(el => {
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
                    document.getElementById('region_form').reset();
                }
            });
        });
    </script>
@endpush