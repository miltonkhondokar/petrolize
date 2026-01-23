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
                                <i class="ki-duotone ki-category fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Edit Complaint Category
                            </h3>
                            <a href="{{ route('complaint-category.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left fs-3 me-2"></i>
                                Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <form id="complaint_category_form" method="POST" action="{{ route('complaint-category.update', $category->uuid) }}">
                                @csrf
                                @method('PUT')
                                
                                <!-- Category Code (readonly for edit) -->
                                <div class="mb-5">
                                    <label class="form-label required">
                                        <i class="ki-duotone ki-code fs-2 me-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Category Code
                                    </label>
                                    <input type="text" class="form-control form-control-solid"
                                        value="{{ $category->code }}" readonly />
                                    <div class="form-text">Category code cannot be changed after creation</div>
                                </div>

                                <div class="row">
                                    <!-- Left column -->
                                    <div class="col-md-6">
                                        <!-- Category Name -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-category fs-2 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Category Name
                                            </label>
                                            <input type="text" name="name" class="form-control form-control-solid"
                                                placeholder="Enter category name (e.g., Fuel Shortage)" 
                                                value="{{ old('name', $category->name) }}"
                                                data-kt-validate="true" 
                                                data-kt-validate-required="Category name is required"
                                                data-kt-validate-pattern="^[a-zA-Z0-9\s\-&]{3,100}$"
                                                data-kt-validate-pattern-msg="Only letters, numbers, spaces, hyphens and ampersands, 3 to 100 characters" />
                                            <div class="form-text">Display name for the complaint category</div>
                                        </div>
                                    </div>

                                    <!-- Right column -->
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
                                                <option value="1" {{ old('is_active', $category->is_active) == '1' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="0" {{ old('is_active', $category->is_active) == '0' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                            <div class="form-text">Active categories will be available for selection</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mb-5">
                                    <label class="form-label">
                                        <i class="ki-duotone ki-document fs-2 me-2 text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Description
                                    </label>
                                    <textarea name="description" class="form-control form-control-solid" rows="3"
                                        placeholder="Enter description (optional)"
                                        data-kt-validate="true" 
                                        data-kt-validate-pattern="^.{0,500}$"
                                        data-kt-validate-pattern-msg="Maximum 500 characters allowed">{{ old('description', $category->description) }}</textarea>
                                    <div class="form-text">Brief description of the complaint category</div>
                                </div>

                                <div class="text-end mt-8">
                                    <a href="{{ route('complaint-category.index') }}" class="btn btn-light me-3 btn-sm">
                                        <i class="bi bi-x-circle fs-3 me-2"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">
                                        <i class="fa-regular fa-floppy-disk fs-3 me-2"></i>
                                        Update Category
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
        document.querySelectorAll('#complaint_category_form [data-kt-validate="true"]').forEach(input => {
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
        document.getElementById('complaint_category_form').addEventListener('submit', async function(e) {
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
                    title: 'Confirm Update',
                    text: 'Are you sure you want to update this complaint category?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update category',
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