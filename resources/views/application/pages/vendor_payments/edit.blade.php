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
                                Edit Vendor Payment
                            </h3>
                            <a href="{{ route('vendor_payments.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left fs-3 me-2"></i>
                                Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <form id="vendor_payment_form" method="POST"
                                action="{{ route('vendor_payments.update', $payment->uuid) }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <!-- Left column -->
                                    <div class="col-md-6">
                                        <!-- Vendor Selection -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-profile-user fs-2 me-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Vendor
                                            </label>
                                            <select name="vendor_uuid" class="form-select form-select-solid"
                                                data-kt-validate="true" data-kt-validate-required="Vendor is required">
                                                <option value="">Select Vendor</option>
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor->uuid }}"
                                                        {{ old('vendor_uuid', $payment->vendor_uuid) == $vendor->uuid ? 'selected' : '' }}>
                                                        {{ $vendor->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Select the vendor for this payment</div>
                                        </div>

                                        <!-- Payment Date -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-calendar-8 fs-2 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Payment Date
                                            </label>
                                            <input type="date" name="payment_date"
                                                class="form-control form-control-solid"
                                                value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}"
                                                data-kt-validate="true"
                                                data-kt-validate-required="Payment date is required" />
                                            <div class="form-text">Date when payment was made</div>
                                        </div>

                                        <!-- Unpaid Purchases / Allocations -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-document fs-2 me-2 text-warning"></i>
                                                Allocate Payment to Purchases
                                            </label>
                                            <div id="allocation_container">
                                                @foreach ($payment->allocations as $idx => $alloc)
                                                    <div class="mb-3 allocation-row"
                                                        data-uuid="{{ $alloc->fuel_purchase_uuid }}">
                                                        <label class="form-label">
                                                            {{ $alloc->purchase->invoice_no ?? $alloc->fuel_purchase_uuid }}
                                                            (Balance:
                                                            ${{ number_format($alloc->purchase->total_amount - $alloc->allocated_amount, 2) }})
                                                        </label>
                                                        <input type="number"
                                                            name="allocations[{{ $idx }}][allocated_amount]"
                                                            class="form-control"
                                                            value="{{ old("allocations.$idx.allocated_amount", $alloc->allocated_amount) }}"
                                                            max="{{ $alloc->purchase->total_amount }}" step="0.01"
                                                            min="0" placeholder="Enter payment amount" />
                                                        <input type="hidden"
                                                            name="allocations[{{ $idx }}][fuel_purchase_uuid]"
                                                            value="{{ $alloc->fuel_purchase_uuid }}" />
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Right column -->
                                    <div class="col-md-6">
                                        <!-- Payment Method -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-credit-card fs-2 me-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Payment Method
                                            </label>
                                            <select name="method" class="form-select form-select-solid"
                                                data-kt-validate="true"
                                                data-kt-validate-required="Payment method is required">
                                                <option value="">Select Method</option>
                                                <option value="cash"
                                                    {{ old('method', $payment->method) == 'cash' ? 'selected' : '' }}>Cash
                                                </option>
                                                <option value="bank"
                                                    {{ old('method', $payment->method) == 'bank' ? 'selected' : '' }}>Bank
                                                    Transfer</option>
                                            </select>
                                            <div class="form-text">Payment method used</div>
                                        </div>

                                        <!-- Amount -->
                                        <div class="mb-5">
                                            <label class="form-label required">
                                                <i class="ki-duotone ki-dollar fs-2 me-2 text-danger">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Amount
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="amount" class="form-control form-control-solid"
                                                    placeholder="0.00" step="0.01" min="0.01"
                                                    value="{{ old('amount', $payment->amount) }}" data-kt-validate="true"
                                                    data-kt-validate-required="Amount is required"
                                                    data-kt-validate-pattern="^\d+(\.\d{1,2})?$"
                                                    data-kt-validate-pattern-msg="Please enter a valid amount (e.g., 100.50)" />
                                            </div>
                                            <div class="form-text">Payment amount in dollars</div>
                                            @if ($payment->allocations->sum('allocated_amount') > 0)
                                                <div class="alert alert-warning mt-2 py-2">
                                                    <i class="ki-duotone ki-information fs-3 me-2"></i>
                                                    Already allocated:
                                                    ${{ number_format($payment->allocations->sum('allocated_amount'), 2) }}
                                                </div>
                                            @endif
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
                                                placeholder="Enter any additional notes (optional)" data-kt-validate="true" data-kt-validate-pattern="^.{0,500}$"
                                                data-kt-validate-pattern-msg="Maximum 500 characters allowed">{{ old('note', $payment->note) }}</textarea>
                                            <div class="form-text">Additional notes about this payment</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mt-8">
                                    <a href="{{ route('vendor_payments.index') }}" class="btn btn-light me-3 btn-sm">
                                        <i class="bi bi-x-circle fs-3 me-2"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">
                                        <i class="fa-regular fa-floppy-disk fs-3 me-2"></i>
                                        Update Payment
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

        document.querySelector('select[name="vendor_uuid"]').addEventListener('change', async function() {
            const vendorUuid = this.value;
            const container = document.getElementById('allocation_container');
            container.innerHTML = '';

            if (!vendorUuid) return;

            const res = await fetch(`/vendor-payments/unpaid/${vendorUuid}`);
            const purchases = await res.json();

            if (!purchases.length) {
                container.innerHTML = '<div class="text-muted">No unpaid purchases for this vendor</div>';
                return;
            }

            purchases.forEach((p, idx) => {
                const row = document.createElement('div');
                row.classList.add('mb-3', 'allocation-row');
                row.dataset.uuid = p.uuid;

                row.innerHTML = `
            <label class="form-label">${p.invoice_no} (${p.station_name}) - Balance: $${p.balance.toFixed(2)}</label>
            <input type="number" name="allocations[${idx}][allocated_amount]" class="form-control" 
                max="${p.balance}" step="0.01" min="0" placeholder="Enter payment amount"/>
            <input type="hidden" name="allocations[${idx}][fuel_purchase_uuid]" value="${p.uuid}" />
        `;
                container.appendChild(row);
            });
        });


        // Attach live validation handlers
        document.querySelectorAll('#vendor_payment_form [data-kt-validate="true"]').forEach(input => {
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
        document.getElementById('vendor_payment_form').addEventListener('submit', async function(e) {
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
                    title: 'Confirm Payment Update',
                    text: 'Are you sure you want to update this vendor payment?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update payment',
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
