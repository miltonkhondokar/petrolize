@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="row">
                <div class="col-md-12">

                    {{-- Alerts --}}
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
                                <i class="ki-duotone ki-pencil fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Edit Fuel Station Capacity
                            </h3>

                            <a href="{{ route('fuel-capacity.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left fs-3 me-2"></i>
                                Back to List
                            </a>
                        </div>

                        <div class="card-body">
                            <form id="fuel_capacity_form" method="POST" action="{{ route('fuel-capacity.update', $row->uuid) }}">
                                @csrf
                                @method('PUT')

                                {{-- Hidden fields for station and fuel type --}}
                                <input type="hidden" name="fuel_station_uuid" value="{{ $row->fuel_station_uuid }}">
                                <input type="hidden" name="fuel_type_uuid" value="{{ $row->fuel_type_uuid }}">

                                {{-- Effective From Date --}}
                                <div class="row mb-6">
                                    <div class="col-md-6">
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-calendar-8 fs-2 me-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Effective From Date
                                            </label>
                                            <input
                                                type="date"
                                                name="effective_from"
                                                id="effective_from"
                                                class="form-control form-control-solid"
                                                value="{{ old('effective_from', optional($row->effective_from)->format('Y-m-d')) }}"
                                                required
                                                data-kt-validate="true"
                                                data-kt-validate-required="Effective date is required"
                                            />
                                            <div class="form-text">Date when this capacity becomes effective</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-information-5 fs-2 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Note (Optional)
                                            </label>
                                            <textarea
                                                name="note"
                                                id="note"
                                                class="form-control form-control-solid"
                                                rows="2"
                                                placeholder="Add any notes about this capacity..."
                                            >{{ old('note', $row->note) }}</textarea>
                                            <div class="form-text">Additional information about this capacity record</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Station Display --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-home-3 fs-2 me-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Fuel Station
                                            </label>
                                            
                                            <div class="form-control form-control-solid bg-light">
                                                <div class="fw-semibold">{{ $row->station->name ?? 'N/A' }}</div>
                                                @if (!empty($row->station?->location))
                                                    <div class="text-muted fs-7">{{ $row->station->location }}</div>
                                                @endif
                                            </div>
                                            <div class="form-text">Station cannot be changed for existing capacity record</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-gas-station fs-2 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Fuel Type
                                            </label>
                                            
                                            <div class="form-control form-control-solid bg-light">
                                                <div class="fw-semibold">{{ $row->fuelType->name ?? 'N/A' }}</div>
                                                @if (!empty($row->fuelType?->code))
                                                    <div class="text-muted fs-7">{{ $row->fuelType->code }}</div>
                                                @endif
                                            </div>
                                            <div class="form-text">Fuel type cannot be changed for existing capacity record</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Capacity and Status --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-water fs-2 me-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Capacity (Liters)
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">L</span>
                                                <input
                                                    type="number"
                                                    name="capacity_liters"
                                                    class="form-control form-control-solid"
                                                    placeholder="0.000"
                                                    value="{{ old('capacity_liters', $row->capacity_liters) }}"
                                                    step="0.001"
                                                    min="0"
                                                    required
                                                    data-kt-validate="true"
                                                    data-kt-validate-required="Capacity is required"
                                                    data-kt-validate-pattern="^\d+(\.\d{1,3})?$"
                                                    data-kt-validate-pattern-msg="Enter a valid capacity with up to 3 decimal places"
                                                />
                                            </div>
                                            <div class="form-text">Capacity in liters for this fuel type</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-5">
                                            <label class="form-label">
                                                <i class="ki-duotone ki-information-5 fs-2 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Status
                                            </label>
                                            <div class="p-4 rounded bg-light">
                                                <div class="form-check form-switch form-check-custom form-check-solid">
                                                    <input type="hidden" name="is_active" value="0" />
                                                    <input
                                                        class="form-check-input status-toggle"
                                                        type="checkbox"
                                                        name="is_active"
                                                        value="1"
                                                        {{ old('is_active', $row->is_active) ? 'checked' : '' }}
                                                    />
                                                    <label class="form-check-label fw-semibold">Active Status</label>
                                                </div>
                                                <div class="text-muted mt-2">
                                                    Active capacity records are used in calculations. Inactive records are ignored.
                                                </div>
                                            </div>
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
                                        Update Capacity
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
        // --- Validation helpers ---
        function clearInvalid(input) {
            input.classList.remove('is-invalid');
            let next = input.nextElementSibling;
            if (next && next.classList.contains('invalid-feedback')) next.remove();
        }

        function validateInput(input) {
            clearInvalid(input);

            const value = (input.value ?? '').trim();
            const requiredMsg = input.getAttribute('data-kt-validate-required');
            const pattern = input.getAttribute('data-kt-validate-pattern');
            const patternMsg = input.getAttribute('data-kt-validate-pattern-msg');

            let errorMsg = null;

            if (!value && requiredMsg) {
                errorMsg = requiredMsg;
            } else if (pattern && value && !(new RegExp(pattern).test(value))) {
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

        // --- Form submit with validation + Swal ---
        document.getElementById('fuel_capacity_form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const inputs = form.querySelectorAll('[data-kt-validate="true"]');

            let firstInvalid = null;

            // Clear previous errors
            inputs.forEach(input => clearInvalid(input));

            // Validate all
            for (const input of inputs) {
                const isValid = validateInput(input);
                if (!isValid) {
                    firstInvalid = firstInvalid || input;
                    await Swal.fire('Validation Error', input.nextElementSibling?.innerText || 'Please check the form.', 'warning');
                    break;
                }
            }

            if (firstInvalid) {
                firstInvalid.focus();
                return;
            }

            const result = await Swal.fire({
                title: 'Confirm Update',
                text: 'Are you sure you want to update this fuel capacity?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, update',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                form.submit();
            }
        });

        // --- Reset handler ---
        document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Reset Form?',
                text: 'All changes will be cleared.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reset it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('fuel_capacity_form').reset();

                    // clear validation errors
                    document.querySelectorAll('#fuel_capacity_form .is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('#fuel_capacity_form .invalid-feedback').forEach(el => el.remove());
                }
            });
        });
    </script>
@endpush