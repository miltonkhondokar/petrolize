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
                                <i class="ki-duotone ki-user-edit fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Edit Vendor: {{ $vendor->name }}
                            </h3>
                            <div>
                                <a href="{{ route('vendors.show', $vendor->uuid) }}" class="btn btn-sm btn-info me-2">
                                    <i class="bi bi-eye fs-3 me-2"></i>
                                    View Details
                                </a>
                                <a href="{{ route('vendors.index') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-arrow-left fs-3 me-2"></i>
                                    Back to List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="vendor_form" method="POST" action="{{ route('vendors.update', $vendor->id) }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <!-- Left column -->
                                    <div class="col-md-6">
                                        <!-- Vendor Name -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-user fs-2 me-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Vendor Name
                                            </label>
                                            <input type="text" name="name" class="form-control form-control-solid"
                                                placeholder="Enter vendor name (e.g., ABC Fuel Suppliers)" 
                                                value="{{ old('name', $vendor->name) }}"
                                                data-kt-validate="true" 
                                                data-kt-validate-required="Vendor name is required"
                                                data-kt-validate-pattern="^[a-zA-Z0-9\s\-&.,]{3,100}$"
                                                data-kt-validate-pattern-msg="Only letters, numbers, spaces, hyphens, ampersands, commas and dots, 3 to 100 characters" />
                                            <div class="form-text">Full name of the vendor/company</div>
                                        </div>

                                        <!-- Phone -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-call fs-2 me-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Phone Number
                                            </label>
                                            <input type="text" name="phone" class="form-control form-control-solid"
                                                placeholder="Enter phone number (e.g., +1234567890)" 
                                                value="{{ old('phone', $vendor->phone) }}"
                                                data-kt-validate="true" 
                                                data-kt-validate-required="Phone number is required"
                                                data-kt-validate-pattern="^[\d\s\+\-\(\)]{5,20}$"
                                                data-kt-validate-pattern-msg="Enter a valid phone number (5-20 characters)" />
                                            <div class="form-text">Primary contact number</div>
                                        </div>

                                        <!-- Address -->
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-location fs-2 me-2 text-warning">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Address
                                            </label>
                                            <textarea name="address" class="form-control form-control-solid" 
                                                rows="3" placeholder="Enter vendor address (optional)"
                                                data-kt-validate="true" 
                                                data-kt-validate-pattern="^[a-zA-Z0-9\s\-\.,#\n]{0,255}$"
                                                data-kt-validate-pattern-msg="Only letters, numbers, spaces, hyphens, commas, dots, # and newlines, max 255 characters">{{ old('address', $vendor->address) }}</textarea>
                                            <div class="form-text">Physical address of the vendor</div>
                                        </div>
                                    </div>

                                    <!-- Right column -->
                                    <div class="col-md-6">
                                        <!-- Email -->
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-sms fs-2 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Email Address
                                            </label>
                                            <input type="email" name="email" class="form-control form-control-solid"
                                                placeholder="Enter email address (e.g., contact@example.com)" 
                                                value="{{ old('email', $vendor->email) }}"
                                                data-kt-validate="true" 
                                                data-kt-validate-pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                                data-kt-validate-pattern-msg="Enter a valid email address" />
                                            <div class="form-text">Contact email address (optional)</div>
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
                                                <option value="1" {{ old('is_active', $vendor->is_active) == '1' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="0" {{ old('is_active', $vendor->is_active) == '0' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                            <div class="form-text">Active vendors will be available for selection</div>
                                        </div>

                                        <!-- Additional Info -->
                                        <div class="card card-flush bg-light-info mt-5">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <i class="ki-duotone ki-information fs-2x text-info me-3">
                                                        <i class="path1"></i><i class="path2"></i>
                                                    </i>
                                                    <div>
                                                        <h5 class="mb-1">Vendor Information</h5>
                                                        <p class="text-muted mb-0">
                                                            <small>Created: {{ $vendor->created_at->format('M d, Y h:i A') }}</small><br>
                                                            <small>Last Updated: {{ $vendor->updated_at->format('M d, Y h:i A') }}</small><br>
                                                            <small>Vendor ID: {{ $vendor->uuid }}</small>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mt-8">
                                    <a href="{{ route('vendors.index') }}" class="btn btn-light me-3 btn-sm">
                                        <i class="bi bi-x-circle fs-3 me-2"></i>
                                        Cancel
                                    </a>
                                    <button type="reset" class="btn btn-warning me-3 btn-sm">
                                        <i class="ki-solid ki-arrows-circle fs-3 me-2"></i>
                                        Reset Changes
                                    </button>
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">
                                        <i class="fa-regular fa-floppy-disk fs-3 me-2"></i>
                                        Update Vendor
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
        document.querySelectorAll('#vendor_form [data-kt-validate="true"]').forEach(input => {
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
        document.getElementById('vendor_form').addEventListener('submit', async function(e) {
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
                // Check if any changes were made
                const originalValues = {
                    name: '{{ $vendor->name }}',
                    phone: '{{ $vendor->phone }}',
                    address: '{{ $vendor->address ?? '' }}',
                    email: '{{ $vendor->email ?? '' }}',
                    is_active: '{{ $vendor->is_active }}'
                };

                const currentValues = {
                    name: document.querySelector('[name="name"]').value.trim(),
                    phone: document.querySelector('[name="phone"]').value.trim(),
                    address: document.querySelector('[name="address"]').value.trim(),
                    email: document.querySelector('[name="email"]').value.trim(),
                    is_active: document.querySelector('[name="is_active"]').value
                };

                const hasChanges = 
                    originalValues.name !== currentValues.name ||
                    originalValues.phone !== currentValues.phone ||
                    originalValues.address !== currentValues.address ||
                    originalValues.email !== currentValues.email ||
                    originalValues.is_active !== currentValues.is_active;

                if (!hasChanges) {
                    await Swal.fire({
                        title: 'No Changes',
                        text: 'No modifications were made to the vendor details.',
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Confirm before submit
                const result = await Swal.fire({
                    title: 'Confirm Update',
                    text: 'Are you sure you want to update this vendor?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update vendor',
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
            document.querySelectorAll('#vendor_form .is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('#vendor_form .invalid-feedback').forEach(el => {
                el.remove();
            });
            
            Swal.fire({
                title: 'Reset Changes?',
                text: 'All changes will be reverted to original values.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reset changes!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Reset form to original values
                    document.querySelector('[name="name"]').value = '{{ $vendor->name }}';
                    document.querySelector('[name="phone"]').value = '{{ $vendor->phone }}';
                    document.querySelector('[name="address"]').value = '{{ $vendor->address ?? '' }}';
                    document.querySelector('[name="email"]').value = '{{ $vendor->email ?? '' }}';
                    document.querySelector('[name="is_active"]').value = '{{ $vendor->is_active }}';
                    
                    Swal.fire('Reset!', 'Form has been reset to original values.', 'success');
                }
            });
        });
    </script>
@endpush