@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            {{-- Filter --}}
            <div class="card card-custom gutter-b mb-5 mb-xl-8 shadow-sm">
                <div class="card-header bg-light-primary">
                    <div class="card-title">
                        <h3 class="card-label">
                            <i class="fas fa-filter"></i> Filter
                            <small>filter fuel purchases</small>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('fuel_purchases.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select name="fuel_station_uuid" class="form-select form-select-solid">
                                    <option value="">Fuel Station</option>
                                    @foreach ($stations ?? [] as $st)
                                        <option value="{{ $st->uuid }}"
                                            {{ ($filters['fuel_station_uuid'] ?? '') === $st->uuid ? 'selected' : '' }}>
                                            {{ $st->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="vendor_uuid" class="form-select form-select-solid">
                                    <option value="">Vendor</option>
                                    @foreach ($vendors ?? [] as $v)
                                        <option value="{{ $v->uuid }}"
                                            {{ ($filters['vendor_uuid'] ?? '') === $v->uuid ? 'selected' : '' }}>
                                            {{ $v->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="status" class="form-select form-select-solid">
                                    <option value="">Status</option>
                                    @php $st = $filters['status'] ?? ''; @endphp
                                    <option value="draft" {{ $st === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="received_partial" {{ $st === 'received_partial' ? 'selected' : '' }}>
                                        Received Partial</option>
                                    <option value="received_full" {{ $st === 'received_full' ? 'selected' : '' }}>Received
                                        Full</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <input type="date" name="from" class="form-control form-control-solid"
                                    value="{{ $filters['from'] ?? '' }}" />
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="to" class="form-control form-control-solid"
                                    value="{{ $filters['to'] ?? '' }}" />
                            </div>
                            <div class="col-md-2 text-end">
                                <button class="btn btn-info w-100"><i
                                        class="ki-duotone ki-filter fs-3 me-2"></i>Filter</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('fuel_purchases.index') }}" class="btn btn-warning w-100">
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
                    <h3 class="card-title fw-bold fs-3 mb-1">Fuel Purchases</h3>
                    <a href="{{ route('fuel_purchases.create') }}" class="btn btn-sm btn-primary">
                        <i class="ki-outline ki-plus-circle fs-3 me-1"></i> Add New Purchase
                    </a>
                </div>

                <div class="card-body py-3">
                    <div class="table-responsive overflow-visible">
                        <table class="table align-middle table-row-dashed table-bordered fs-6 gy-4">
                            <thead class="bg-light-dark">
                                <tr class="fw-bold text-gray-700">
                                    <th>#</th>
                                    <th>Purchase Date</th>
                                    <th>Station</th>
                                    <th>Vendor</th>
                                    <th>Invoice</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($purchases as $index => $purchase)
                                    <tr>
                                        <td>{{ $purchases->firstItem() + $index }}</td>
                                        <td>
                                            <span class="text-muted"
                                                title="{{ optional($purchase->purchase_date)->format('d M Y') }}">
                                                {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold">{{ $purchase->station->name ?? '-' }}</td>
                                        <td>{{ $purchase->vendor->name ?? '-' }}</td>
                                        <td>{{ $purchase->invoice_no ?? '-' }}</td>

                                        <td>
                                            @php
                                                $badge = 'secondary';
                                                if ($purchase->status === 'received_full') {
                                                    $badge = 'success';
                                                } elseif ($purchase->status === 'received_partial') {
                                                    $badge = 'warning';
                                                } elseif ($purchase->status === 'draft') {
                                                    $badge = 'info';
                                                }
                                            @endphp
                                            <span class="badge badge-light-{{ $badge }}">
                                                {{ str_replace('_', ' ', ucfirst($purchase->status)) }}
                                            </span>
                                        </td>

                                        <td class="text-end fw-bold">
                                            {{ number_format((float) $purchase->total_amount, 2) }}
                                        </td>

                                        <td class="text-end">
                                            <div class="dropdown">
                                                <a href="#" class="btn btn-sm btn-light-info dropdown-toggle"
                                                    data-bs-toggle="dropdown">
                                                    <i class="ki-outline ki-setting-4 fs-2 me-2"></i> Actions
                                                </a>
                                                <ul class="dropdown-menu">
                                                    {{-- View always allowed --}}
                                                    <li>
                                                        <a href="{{ route('fuel_purchases.show', $purchase->uuid) }}"
                                                            class="dropdown-item">
                                                            <i class="ki-duotone ki-eye fs-2 me-2 text-info">
                                                                <span class="path1"></span><span
                                                                    class="path2"></span><span class="path3"></span>
                                                            </i>
                                                            View
                                                        </a>
                                                    </li>

                                                    {{-- Edit only when Draft --}}
                                                    @if ($purchase->status === 'draft')
                                                        <li>
                                                            <a href="{{ route('fuel_purchases.edit', $purchase->uuid) }}"
                                                                class="dropdown-item">
                                                                <i class="ki-duotone ki-pencil fs-2 me-2 text-warning">
                                                                    <i class="path1"></i><i class="path2"></i>
                                                                </i>
                                                                Edit
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <span class="dropdown-item text-muted disabled"
                                                                style="cursor:not-allowed;">
                                                                <i class="ki-duotone ki-lock fs-2 me-2 text-muted">
                                                                    <i class="path1"></i><i class="path2"></i>
                                                                </i>
                                                                Edit (Locked)
                                                            </span>
                                                        </li>
                                                    @endif
                                                </ul>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-10">
                                            <i class="ki-duotone ki-information-2 fs-2x text-gray-400 mb-2"></i><br>
                                            No purchases found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $purchases->appends($filters ?? [])->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
