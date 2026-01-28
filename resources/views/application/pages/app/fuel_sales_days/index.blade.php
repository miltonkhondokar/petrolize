@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            {{-- Filter --}}
            <div class="card card-custom gutter-b mb-5 shadow-sm">
                <div class="card-header bg-light-primary">
                    <div class="card-title">
                        <h3 class="card-label"><i class="fas fa-filter"></i> Filter <small>fuel sales days</small></h3>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('fuel_sales_days.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select name="fuel_station_uuid" class="form-select form-select-solid">
                                    <option value="">All Stations</option>
                                    @foreach ($stations as $st)
                                        <option value="{{ $st->uuid }}"
                                            {{ ($filters['fuel_station_uuid'] ?? '') == $st->uuid ? 'selected' : '' }}>
                                            {{ $st->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="sale_date" class="form-control form-control-solid"
                                    value="{{ $filters['sale_date'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select form-select-solid">
                                    <option value="">All Status</option>
                                    <option value="draft" {{ ($filters['status'] ?? '') == 'draft' ? 'selected' : '' }}>
                                        Draft
                                    </option>
                                    <option value="submitted"
                                        {{ ($filters['status'] ?? '') == 'submitted' ? 'selected' : '' }}>
                                        Submitted</option>
                                    <option value="approved"
                                        {{ ($filters['status'] ?? '') == 'approved' ? 'selected' : '' }}>
                                        Approved</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="from" class="form-control form-control-solid"
                                    value="{{ $filters['from'] ?? '' }}" placeholder="From">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="to" class="form-control form-control-solid"
                                    value="{{ $filters['to'] ?? '' }}" placeholder="To">
                            </div>
                            <div class="col-md-2 text-end">
                                <button class="btn btn-info w-100"><i
                                        class="ki-duotone ki-filter fs-3 me-2"></i>Filter</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('fuel_sales_days.index') }}" class="btn btn-warning w-100">
                                    <i class="ki-duotone ki-reload fs-3 me-2"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light-primary">
                    <h3 class="card-title fw-bold fs-3 mb-1">Fuel Sales Days</h3>
                    <a href="{{ route('fuel_sales_days.create') }}" class="btn btn-sm btn-primary">
                        <i class="ki-outline ki-plus-circle fs-3 me-1"></i> Add New Sales
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive overflow-visible">
                        <table class="table align-middle table-row-dashed table-bordered fs-6 gy-4">
                            <thead class="bg-light-dark">
                                <tr class="fw-bold text-gray-700">
                                    <th>#</th>
                                    <th>Station</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Cash</th>
                                    <th class="text-end">Bank</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($days as $idx => $d)
                                    <tr>
                                        <td>{{ $days->firstItem() + $idx }}</td>
                                        <td class="fw-semibold">{{ $d->station->name ?? '-' }}</td>
                                        <td>{{ $d->sale_date?->format('d M Y') }}</td>
                                        <td>
                                            <span
                                                class="badge badge-light-{{ $d->status == 'draft' ? 'warning' : ($d->status == 'submitted' ? 'info' : 'success') }}">
                                                {{ ucfirst($d->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format((float) $d->total_amount, 2) }}</td>
                                        <td class="text-end">{{ number_format((float) $d->cash_amount, 2) }}</td>
                                        <td class="text-end">{{ number_format((float) $d->bank_amount, 2) }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-light-info">{{ $d->items_count }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-light-info"
                                                href="{{ route('fuel_sales_days.show', $d->uuid) }}">
                                                View
                                            </a>
                                            @if ($d->status == 'draft')
                                                <a class="btn btn-sm btn-warning"
                                                    href="{{ route('fuel_sales_days.edit', $d->uuid) }}">
                                                    Edit
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-10">No records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $days->appends($filters)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
