@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <!-- Payment Details Card -->
            <div class="card shadow-sm mb-5">
                <div class="card-header bg-light-primary d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="ki-duotone ki-money fs-2 text-primary me-2">
                            <i class="path1"></i><i class="path2"></i>
                        </i>
                        Vendor Payment Details
                    </h3>
                    <div class="d-flex">
                        <a href="{{ route('vendor_payments.edit', $payment->uuid) }}" class="btn btn-sm btn-warning me-2">
                            <i class="ki-duotone ki-pencil fs-3 me-2"></i>
                            Edit
                        </a>
                        <a href="{{ route('vendor_payments.index') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-arrow-left fs-3 me-2"></i>
                            Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex flex-column">
                                <!-- Vendor -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Vendor:</span>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px symbol-circle me-3">
                                            <div class="symbol-label bg-light-primary">
                                                <i class="ki-duotone ki-user-circle fs-2 text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="fw-bold fs-5 text-dark">{{ $payment->vendor->name ?? 'N/A' }}</span>
                                            @if($payment->vendor->email)
                                                <div class="text-muted fs-6">{{ $payment->vendor->email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Details Row -->
                                <div class="row mb-7">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="fw-bold text-gray-600 fs-5 me-2">Payment Date:</span>
                                            <span class="fw-bold fs-5 text-dark">{{ $payment->payment_date->format('d M Y') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="fw-bold text-gray-600 fs-5 me-2">Payment Method:</span>
                                            <span class="badge badge-light-{{ $payment->method == 'bank' ? 'info' : 'warning' }} fs-6">
                                                <i class="ki-duotone ki-{{ $payment->method == 'bank' ? 'bank' : 'money' }} fs-5 me-1"></i>
                                                {{ ucfirst($payment->method) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="fw-bold text-gray-600 fs-5 me-2">Reference:</span>
                                            <span class="text-muted fs-5">{{ $payment->reference_no ?? 'N/A' }}</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold text-gray-600 fs-5 me-2">Created By:</span>
                                            <span class="text-muted fs-5">{{ $payment->createdBy->name ?? 'System' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Amount -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Amount:</span>
                                    <span class="badge badge-light-success fs-3 fw-bold px-4 py-2">
                                        ${{ number_format($payment->amount, 2) }}
                                    </span>
                                </div>

                                <!-- Allocations Summary -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Allocated Amount:</span>
                                    @php
                                        $allocatedAmount = $payment->allocations->sum('allocated_amount');
                                        $remainingAmount = $payment->amount - $allocatedAmount;
                                    @endphp
                                    <span class="badge badge-light-{{ $allocatedAmount > 0 ? 'primary' : 'warning' }} fs-6">
                                        ${{ number_format($allocatedAmount, 2) }}
                                    </span>
                                    <span class="text-muted ms-3 fs-5">Remaining: ${{ number_format($remainingAmount, 2) }}</span>
                                </div>

                                <!-- Note -->
                                @if($payment->note)
                                    <div class="d-flex align-items-start mb-7">
                                        <span class="fw-bold text-gray-600 fs-5 me-2">Note:</span>
                                        <span class="text-muted fs-5">{{ $payment->note }}</span>
                                    </div>
                                @endif

                                <!-- UUID and Timestamps -->
                                <div class="d-flex align-items-center mb-3">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Payment ID:</span>
                                    <span class="text-muted fs-5">{{ $payment->uuid }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Created:</span>
                                    <span class="text-muted fs-5">{{ $payment->created_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $payment->created_at->diffForHumans() }})</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card card-flush bg-light-info">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="ki-duotone ki-information fs-2 text-info me-2">
                                            <i class="path1"></i><i class="path2"></i>
                                        </i>
                                        Payment Summary
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-column gap-4">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-gray-600">Total Amount:</span>
                                            <span class="fw-bold text-dark">${{ number_format($payment->amount, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-gray-600">Allocated:</span>
                                            <span class="fw-bold text-primary">${{ number_format($allocatedAmount, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-gray-600">Remaining:</span>
                                            <span class="fw-bold {{ $remainingAmount > 0 ? 'text-warning' : 'text-success' }}">
                                                ${{ number_format($remainingAmount, 2) }}
                                            </span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-gray-600">Allocations Count:</span>
                                            <span class="badge badge-light-primary">{{ $payment->allocations->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Allocations Section -->
            <div class="card shadow-sm">
                <div class="card-header bg-light-success">
                    <h3 class="card-title">
                        <i class="ki-duotone ki-copy fs-2 text-success me-2">
                            <i class="path1"></i><i class="path2"></i>
                        </i>
                        Payment Allocations
                    </h3>
                </div>
                <div class="card-body">
                    
                    <!-- Existing Allocations -->
                    @if($payment->allocations->count() > 0)
                        <div class="table-responsive mb-5">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light-dark">
                                    <tr>
                                        <th>Purchase Date</th>
                                        <th>Purchase Reference</th>
                                        <th>Station</th>
                                        <th>Fuel Quantity</th>
                                        <th>Total Amount</th>
                                        <th>Allocated Amount</th>
                                        <th>Remaining Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payment->allocations as $allocation)
                                        @php
                                            $purchase = $allocation->purchase;
                                            $remaining = $purchase->total_amount - $purchase->allocations->sum('allocated_amount');
                                        @endphp
                                        <tr>
                                            <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                                            <td>{{ $purchase->reference_no ?? 'N/A' }}</td>
                                            <td>{{ $purchase->station->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($purchase->quantity, 2) }} L</td>
                                            <td>${{ number_format($purchase->total_amount, 2) }}</td>
                                            <td>
                                                <span class="badge badge-light-success">
                                                    ${{ number_format($allocation->allocated_amount, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-{{ $remaining > 0 ? 'warning' : 'success' }}">
                                                    ${{ number_format($remaining, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="ki-duotone ki-information fs-3 me-2"></i>
                            No allocations made for this payment yet.
                        </div>
                    @endif

                    <!-- Allocation Form -->
                    @if($remainingAmount > 0 && $openPurchases->count() > 0)
                        <div class="card card-flush bg-light-primary mt-5">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="ki-duotone ki-add-files fs-2 text-primary me-2"></i>
                                    Allocate to Purchases
                                </h4>
                            </div>
                            <div class="card-body">
                                <form id="allocation_form" method="POST" action="{{ route('vendor_payments.allocate', $payment->uuid) }}">
                                    @csrf
                                    <div class="alert alert-info mb-4">
                                        <i class="ki-duotone ki-information fs-3 me-2"></i>
                                        Remaining payment amount to allocate: <strong>${{ number_format($remainingAmount, 2) }}</strong>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Select</th>
                                                    <th>Purchase Date</th>
                                                    <th>Reference</th>
                                                    <th>Station</th>
                                                    <th>Total Amount</th>
                                                    <th>Already Paid</th>
                                                    <th>Remaining Due</th>
                                                    <th>Allocate Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($openPurchases as $purchase)
                                                    @php
                                                        $allocated = $purchase->allocations->sum('allocated_amount');
                                                        $remainingDue = $purchase->total_amount - $allocated;
                                                    @endphp
                                                    @if($remainingDue > 0)
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" class="form-check-input purchase-checkbox"
                                                                    data-remaining="{{ $remainingDue }}"
                                                                    onclick="toggleAllocation(this, '{{ $purchase->uuid }}')">
                                                            </td>
                                                            <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                                                            <td>{{ $purchase->reference_no ?? 'N/A' }}</td>
                                                            <td>{{ $purchase->station->name ?? 'N/A' }}</td>
                                                            <td>${{ number_format($purchase->total_amount, 2) }}</td>
                                                            <td>${{ number_format($allocated, 2) }}</td>
                                                            <td>
                                                                <span class="badge badge-light-warning">
                                                                    ${{ number_format($remainingDue, 2) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text">$</span>
                                                                    <input type="number" 
                                                                           name="allocations[{{ $purchase->uuid }}][allocated_amount]" 
                                                                           class="form-control allocation-amount"
                                                                           data-purchase-uuid="{{ $purchase->uuid }}"
                                                                           placeholder="0.00" 
                                                                           step="0.01" 
                                                                           min="0.01"
                                                                           max="{{ min($remainingDue, $remainingAmount) }}"
                                                                           disabled
                                                                           oninput="validateAllocation(this)">
                                                                    <input type="hidden" 
                                                                           name="allocations[{{ $purchase->uuid }}][fuel_purchase_uuid]" 
                                                                           value="{{ $purchase->uuid }}">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span>Total Allocated:</span>
                                            <span id="total_allocated" class="fw-bold">$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span>Remaining Payment Balance:</span>
                                            <span id="remaining_balance" class="fw-bold text-success">
                                                ${{ number_format($remainingAmount, 2) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-end mt-4">
                                        <button type="submit" class="btn btn-success" id="submit_allocation">
                                            <i class="ki-duotone ki-check fs-3 me-2"></i>
                                            Save Allocations
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @elseif($remainingAmount <= 0)
                        <div class="alert alert-success">
                            <i class="ki-duotone ki-check-circle fs-3 me-2"></i>
                            Payment has been fully allocated.
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="ki-duotone ki-information fs-3 me-2"></i>
                            No open purchases found for this vendor to allocate.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let totalAllocated = 0;
        const maxAllocation = parseFloat({{ $remainingAmount }});

        function toggleAllocation(checkbox, purchaseUuid) {
            const amountInput = document.querySelector(`input[data-purchase-uuid="${purchaseUuid}"]`);
            
            if (checkbox.checked) {
                amountInput.disabled = false;
                amountInput.value = '';
                amountInput.focus();
            } else {
                amountInput.disabled = true;
                amountInput.value = '';
                updateTotals();
            }
        }

        function validateAllocation(input) {
            const value = parseFloat(input.value) || 0;
            const max = parseFloat(input.max);
            
            if (value > max) {
                input.value = max.toFixed(2);
                Swal.fire('Warning', `Cannot allocate more than $${max.toFixed(2)} for this purchase`, 'warning');
            }
            
            updateTotals();
        }

        function updateTotals() {
            totalAllocated = 0;
            document.querySelectorAll('.allocation-amount:not(:disabled)').forEach(input => {
                const value = parseFloat(input.value) || 0;
                totalAllocated += value;
            });
            
            document.getElementById('total_allocated').textContent = '$' + totalAllocated.toFixed(2);
            const remaining = maxAllocation - totalAllocated;
            document.getElementById('remaining_balance').textContent = '$' + remaining.toFixed(2);
            
            // Update remaining balance color
            const balanceElement = document.getElementById('remaining_balance');
            if (remaining < 0) {
                balanceElement.classList.remove('text-success');
                balanceElement.classList.add('text-danger');
            } else if (remaining === 0) {
                balanceElement.classList.remove('text-success', 'text-danger');
                balanceElement.classList.add('text-warning');
            } else {
                balanceElement.classList.remove('text-danger', 'text-warning');
                balanceElement.classList.add('text-success');
            }
        }

        // Form submission
        document.getElementById('allocation_form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (totalAllocated <= 0) {
                await Swal.fire('Error', 'Please allocate some amount before saving', 'error');
                return;
            }
            
            if (totalAllocated > maxAllocation) {
                await Swal.fire('Error', 'Total allocated amount exceeds payment balance', 'error');
                return;
            }
            
            const result = await Swal.fire({
                title: 'Confirm Allocations',
                text: `Are you sure you want to allocate $${totalAllocated.toFixed(2)}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, save allocations',
                cancelButtonText: 'Cancel'
            });
            
            if (result.isConfirmed) {
                this.submit();
            }
        });

        // Auto-calculate when checkbox is checked
        document.querySelectorAll('.purchase-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    const remaining = parseFloat(this.getAttribute('data-remaining'));
                    const purchaseUuid = this.getAttribute('onclick').match(/'([^']+)'/)[1];
                    const input = document.querySelector(`input[data-purchase-uuid="${purchaseUuid}"]`);
                    
                    // Auto-fill with remaining due or available balance, whichever is smaller
                    const autoFill = Math.min(remaining, maxAllocation - totalAllocated);
                    if (autoFill > 0) {
                        input.value = autoFill.toFixed(2);
                        updateTotals();
                    }
                }
            });
        });
    </script>
@endpush