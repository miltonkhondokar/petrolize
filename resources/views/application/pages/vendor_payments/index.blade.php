@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            {{-- Filter Section --}}
            <div class="card card-custom gutter-b mb-5 mb-xl-8 shadow-sm">
                <div class="card-header bg-light-primary">
                    <div class="card-title">
                        <h3 class="card-label">
                            <i class="fas fa-filter"></i> Filter
                            <small>filter vendor payments</small>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('vendor_payments.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select name="vendor_uuid" class="form-select form-select-solid">
                                    <option value="">Select Vendor</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->uuid }}"
                                            {{ isset($filters['vendor_uuid']) && $filters['vendor_uuid'] == $vendor->uuid ? 'selected' : '' }}>
                                            {{ $vendor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="method" class="form-select form-select-solid">
                                    <option value="">Payment Method</option>
                                    <option value="cash"
                                        {{ isset($filters['method']) && $filters['method'] == 'cash' ? 'selected' : '' }}>
                                        Cash
                                    </option>
                                    <option value="bank"
                                        {{ isset($filters['method']) && $filters['method'] == 'bank' ? 'selected' : '' }}>
                                        Bank
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="from" class="form-control form-control-solid"
                                    placeholder="From Date" value="{{ $filters['from'] ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="to" class="form-control form-control-solid"
                                    placeholder="To Date" value="{{ $filters['to'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-info">
                                    <i class="ki-duotone ki-filter fs-3 me-2"></i>Filter
                                </button>
                                <a href="{{ route('vendor_payments.index') }}" class="btn btn-warning ms-2">
                                    <i class="ki-duotone ki-reload fs-3 me-2"></i>Reset
                                </a>
                                <a href="{{ route('vendor_payments.create') }}" class="btn btn-primary ms-2">
                                    <i class="ki-outline ki-plus fs-3 me-2"></i>Add Payment
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Vendor Payments Table -->
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light-primary">
                    <h3 class="card-title fw-bold fs-3 mb-1">Vendor Payments</h3>
                    <div class="d-flex">
                        <span class="badge badge-light-success fs-6 me-3">
                            Total: {{ $payments->total() }} Payments
                        </span>
                    </div>
                </div>
                <div class="card-body py-3">
                    <div class="table-responsive overflow-visible">
                        <table class="table align-middle table-row-dashed table-bordered fs-6 gy-4">
                            <thead class="bg-light-dark">
                                <tr class="fw-bold text-gray-700">
                                    <th>#</th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-profile-user fs-2 me-2 text-primary">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Vendor
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-calendar-8 fs-2 me-2 text-info">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Payment Date
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-dollar fs-2 me-2 text-success">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Amount
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-credit-card fs-2 me-2 text-warning">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Method
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-document fs-2 me-2 text-danger">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Reference
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-calendar fs-2 me-2 text-primary">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Created At
                                        </div>
                                    </th>
                                    <th class="text-end">
                                        <div class="d-flex justify-content-end align-items-center">
                                            <i class="ki-duotone ki-setting-2 fs-2 me-2 text-danger">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Actions
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $index => $payment)
                                    <tr>
                                        <td>{{ $payments->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px symbol-circle me-3">
                                                    <div class="symbol-label bg-light-primary">
                                                        <i class="ki-duotone ki-user-circle fs-2 text-primary"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-gray-800">{{ $payment->vendor->name ?? 'N/A' }}</div>
                                                    <div class="text-muted fs-7">{{ $payment->vendor->email ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $payment->payment_date->format('d M Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-success fs-6 fw-bold">
                                                ${{ number_format($payment->amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-{{ $payment->method == 'bank' ? 'info' : 'warning' }}">
                                                <i class="ki-duotone ki-{{ $payment->method == 'bank' ? 'bank' : 'money' }} fs-5 me-1"></i>
                                                {{ ucfirst($payment->method) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $payment->reference_no ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted"
                                                title="{{ $payment->created_at->format('d M Y, h:i A') }}">
                                                {{ $payment->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <a href="#" class="btn btn-sm btn-light-info dropdown-toggle"
                                                    data-bs-toggle="dropdown">
                                                    <i class="ki-outline ki-setting-4 fs-2 me-2"></i> Actions
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="{{ route('vendor_payments.show', $payment->uuid) }}" class="dropdown-item">
                                                            <i class="ki-duotone ki-eye fs-2 me-2 text-info">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                            View Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('vendor_payments.edit', $payment->uuid) }}" class="dropdown-item">
                                                            <i class="ki-duotone ki-pencil fs-2 me-2 text-warning">
                                                                <i class="path1"></i><i class="path2"></i>
                                                            </i>
                                                            Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)" onclick="showAllocations('{{ $payment->uuid }}')" 
                                                           class="dropdown-item">
                                                            <i class="ki-duotone ki-copy fs-2 me-2 text-primary">
                                                                <i class="path1"></i><i class="path2"></i>
                                                            </i>
                                                            Allocations
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-10">
                                            <i class="ki-duotone ki-information-2 fs-2x text-gray-400 mb-2"></i><br>
                                            No vendor payments found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $payments->appends($filters)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showAllocations(uuid) {
            window.location.href = `/vendor-payments/${uuid}`;
        }
    </script>
@endpush