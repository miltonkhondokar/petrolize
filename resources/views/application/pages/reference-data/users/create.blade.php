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
                        <div class="card-header d-flex justify-content-between align-items-center bg-light-danger">
                            <h3 class="card-title">
                                <i class="ki-duotone ki-user fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Register New User
                            </h3>
                            <a href="{{ route('user-master-data') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left fs-3 me-2"></i>
                                Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <form id="user_form" method="POST" action="{{ route('user-master-data-store') }}">
                                @csrf
                                <div class="row">
                                    <!-- Left column -->
                                    <div class="col-md-6">
                                        <!-- Full Name -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-user fs-2 me-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Full Name
                                            </label>
                                            <input type="text" name="name" class="form-control form-control-solid"
                                                placeholder="Enter full name" value="{{ old('name') }}"
                                                data-kt-validate="true" data-kt-validate-required="Full name is required"
                                                data-kt-validate-pattern="^[a-zA-Z\s]{3,50}$"
                                                data-kt-validate-pattern-msg="Only letters and spaces, 3 to 50 characters" />
                                        </div>

                                        <!-- Email Address -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-sms fs-2 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Email Address
                                            </label>
                                            <input type="email" name="email" class="form-control form-control-solid"
                                                placeholder="example@domain.com" value="{{ old('email') }}"
                                                data-kt-validate="true" data-kt-validate-required="Email is required"
                                                data-kt-validate-email="Invalid email format" />
                                        </div>

                                        <!-- Phone Number -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-tablet fs-2 me-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                Phone Number
                                            </label>
                                            <input type="text" name="phone" class="form-control form-control-solid"
                                                placeholder="11 digit phone" value="{{ old('phone') }}"
                                                data-kt-validate="true" data-kt-validate-required="Phone number is required"
                                                data-kt-validate-pattern="^[0-9]{11}$"
                                                data-kt-validate-pattern-msg="Phone must be 11 digits" />
                                        </div>

                                        <!-- Gender -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-user-square fs-2 me-2 text-danger">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Gender
                                            </label>
                                            <select name="gender" class="form-select form-select-solid"
                                                data-kt-validate="true" data-kt-validate-required="Gender is required">
                                                <option value="">Select Gender</option>
                                                <option value="1" {{ old('gender') == '1' ? 'selected' : '' }}>Male
                                                </option>
                                                <option value="2" {{ old('gender') == '2' ? 'selected' : '' }}>Female
                                                </option>
                                            </select>
                                        </div>

                                        <!-- User Type -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-briefcase fs-2 me-2 text-warning">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                User Type
                                            </label>
                                            <select name="user_type" class="form-select form-select-solid"
                                                data-kt-validate="true" data-kt-validate-required="User type is required">
                                                <option value="">Select User Type</option>
                                                @foreach ($userTypes as $type)
                                                    <option value="{{ $type }}"
                                                        {{ old('user_type') == $type ? 'selected' : '' }}>
                                                        {{ $type }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>

                                    <!-- Right column -->
                                    <div class="col-md-6">
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-lock-2 fs-2 me-2 text-danger"></i>
                                                Password
                                            </label>
                                            <div class="position-relative">
                                                <input type="password" name="password" id="password-field"
                                                    class="form-control form-control-solid" placeholder="Enter password"
                                                    value="{{ old('password') }}" data-kt-validate="true"
                                                    data-kt-validate-required="Password is required"
                                                    data-kt-validate-pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$"
                                                    data-kt-validate-pattern-msg="Min 8 chars, 1 uppercase, 1 lowercase, 1 number" />
                                                <i class="fa-solid fa-eye toggle-password position-absolute top-50 translate-middle-y cursor-pointer text-muted"
                                                    style="font-size: 1.2rem; right: 10px;"
                                                    title="Show/Hide Password"></i>
                                            </div>
                                            <div class="form-text mt-2">
                                                Must contain at least 8 characters with 1 uppercase, 1 lowercase, and 1
                                                number
                                            </div>
                                        </div>

                                        <!-- Confirm Password Field -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-lock-2 fs-2 me-2 text-primary"></i>
                                                Confirm Password
                                            </label>
                                            <div class="position-relative">
                                                <input type="password" name="password_confirmation"
                                                    id="password-confirmation-field"
                                                    class="form-control form-control-solid" placeholder="Confirm password"
                                                    value="{{ old('password_confirmation') }}" data-kt-validate="true"
                                                    data-kt-validate-required="Password confirmation is required" />
                                                <i class="fa-solid fa-eye toggle-password position-absolute top-50 translate-middle-y cursor-pointer text-muted"
                                                    style="font-size: 1.2rem; right: 10px;"
                                                    title="Show/Hide Password"></i>
                                            </div>
                                        </div>

                                        <!-- User Status -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-shield-tick fs-2 me-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                User Status
                                            </label>
                                            <select name="user_status" class="form-select form-select-solid"
                                                data-kt-validate="true"
                                                data-kt-validate-required="User status is required">
                                                <option value="1" {{ old('user_status') == '1' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="0" {{ old('user_status') == '0' ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                        </div>

                                        <!-- Roles -->
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-profile-circle fs-2 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Roles (Optional)
                                            </label>
                                            <select name="roles[]" class="form-select form-select-solid" multiple
                                                data-kt-select2="true" data-placeholder="Select roles...">
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}"
                                                        {{ in_array($role->id, old('roles', [])) ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">
                                        <i class="fa-regular fa-floppy-disk"></i>
                                        Register User
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
document.querySelectorAll('.toggle-password').forEach(function(toggle) {
    toggle.addEventListener('click', function() {
        // Find the input field within the same parent container
        const input = this.parentElement.querySelector('input');
        
        if (input) {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);

            // Toggle icon classes correctly
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        }
    });
});
    </script>
    <script>
        // Initialize Select2
        $(document).ready(function() {
            $('[data-kt-select2="true"]').select2({
                placeholder: $(this).data('placeholder'),
                allowClear: true
            });
        });

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
            const emailMsg = input.getAttribute('data-kt-validate-email');

            let errorMsg = null;

            if (!value && requiredMsg) {
                errorMsg = requiredMsg;
            } else if (pattern && value && !new RegExp(pattern).test(value)) {
                errorMsg = patternMsg;
            } else if (input.type === 'email' && value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                errorMsg = emailMsg;
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

        // Validate that password and confirmation match
        function validatePasswordMatch() {
            const password = document.querySelector('#user_form input[name="password"]');
            const confirm = document.querySelector('#user_form input[name="password_confirmation"]');

            // Clear previous errors
            [password, confirm].forEach(input => {
                input.classList.remove('is-invalid');
                let next = input.nextElementSibling;
                if (next && next.classList.contains('invalid-feedback')) {
                    next.remove();
                }
            });

            if (password.value !== confirm.value) {
                const errorMsg = 'Passwords do not match';

                // Add errors to both fields
                [password, confirm].forEach(input => {
                    input.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.classList.add('invalid-feedback');
                    errorDiv.innerText = errorMsg;
                    input.after(errorDiv);
                });
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
        document.querySelectorAll('#user_form [data-kt-validate="true"]').forEach(input => {
            if (input.tagName === 'SELECT') {
                input.addEventListener('change', () => {
                    validateSelect(input);
                });
            } else {
                input.addEventListener('input', () => {
                    validateInput(input);
                    if (input.name === 'password_confirmation' || input.name === 'password') {
                        validatePasswordMatch();
                    }
                });
            }
        });

        // Submit handler with validation + Swal
        document.getElementById('user_form').addEventListener('submit', async function(e) {
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

            // If all fields valid, check password match
            if (valid) {
                const pwdMatch = validatePasswordMatch();
                if (!pwdMatch) {
                    valid = false;
                    firstInvalid = document.querySelector('#user_form input[name="password"]');
                    await Swal.fire('Validation Error', 'Passwords do not match', 'warning');
                }
            }

            if (valid) {
                // Confirm before submit
                const result = await Swal.fire({
                    title: 'Confirm Submission',
                    text: 'Are you sure you want to create this user?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, create user',
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
