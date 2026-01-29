@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            {{-- Alerts --}}
            @if ($errors->any())
                <div class="alert alert-warning d-flex align-items-center p-5 mb-5">
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
                <div class="alert alert-success d-flex align-items-center p-5 mb-5">
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
                <div class="alert alert-danger d-flex align-items-center p-5 mb-5">
                    <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                        <i class="path1"></i><i class="path2"></i>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-danger">Error!</h4>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- Filter Section --}}
            <div class="card card-custom gutter-b mb-5 mb-xl-8 shadow-sm">
                <div class="card-header bg-light-danger">
                    <div class="card-title">
                        <h3 class="card-label">
                            <i class="fas fa-filter"></i> Filter
                            <small>filter fuel unit prices</small>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('fuel-unit-price.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select name="fuel_station_uuid" class="form-select form-select-solid">
                                    <option value="">Select Fuel Station</option>
                                    @foreach ($fuelStations as $fuelStation)
                                        <option
                                            value="{{ $fuelStation->uuid }}"
                                            {{ isset($filters['fuel_station_uuid']) && $filters['fuel_station_uuid'] == $fuelStation->uuid ? 'selected' : '' }}
                                        >
                                            {{ $fuelStation->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="fuel_type_uuid" class="form-select form-select-solid">
                                    <option value="">Select Fuel Type</option>
                                    @foreach ($fuelTypes as $fuelType)
                                        <option
                                            value="{{ $fuelType->uuid }}"
                                            {{ isset($filters['fuel_type_uuid']) && $filters['fuel_type_uuid'] == $fuelType->uuid ? 'selected' : '' }}
                                        >
                                            {{ $fuelType->name }} ({{ $fuelType->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="is_active" class="form-select form-select-solid">
                                    <option value="">Status</option>
                                    <option value="1" {{ isset($filters['is_active']) && $filters['is_active'] == '1' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="0" {{ isset($filters['is_active']) && $filters['is_active'] == '0' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-2 text-end">
                                <button class="btn btn-info w-100"><i
                                        class="ki-duotone ki-filter fs-3 me-2"></i>Filter</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('fuel-unit-price.index') }}" class="btn btn-warning w-100">
                                    <i class="ki-duotone ki-reload fs-3 me-2"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- List Table --}}
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light-danger">
                    <h3 class="card-title fw-bold fs-3 mb-1">Fuel Unit Prices List</h3>
                    <a href="{{ route('fuel-unit-price.create') }}" class="btn btn-sm btn-primary">
                        <i class="ki-outline ki-plus-circle fs-3 me-1"></i> Add New Price
                    </a>
                </div>

                <div class="card-body py-3">
                    <div class="table-responsive overflow-visible">
                        <table class="table align-middle table-row-dashed table-bordered fs-6 gy-4">
                            <thead class="bg-light-dark">
                                <tr class="fw-bold text-gray-700">
                                    <th style="width: 50px;">#</th>

                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-home-3 fs-2 me-2 text-primary">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Fuel Station
                                        </div>
                                    </th>

                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-gas-station fs-2 me-2 text-info">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Fuel Type
                                        </div>
                                    </th>

                                    <th style="width: 170px;">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-dollar fs-2 me-2 text-success">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Price Per Unit
                                        </div>
                                    </th>

                                    <th style="width: 130px;">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-shield-tick fs-2 me-2 text-success">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Status
                                        </div>
                                    </th>

                                    <th style="width: 160px;">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-calendar fs-2 me-2 text-primary">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Created At
                                        </div>
                                    </th>

                                    <th class="text-end" style="width: 160px;">
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
                                @forelse ($prices as $index => $price)
                                    <tr>
                                        <td>{{ $prices->firstItem() + $index }}</td>

                                        <td class="text-dark fw-semibold">
                                            <div>
                                                {{ $price->fuelStation->name ?? 'N/A' }}
                                                @if (!empty($price->fuelStation?->location))
                                                    <div class="text-muted fs-7">{{ $price->fuelStation->location }}</div>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $price->fuelType->name ?? 'N/A' }}</span>
                                                <span class="text-muted fs-7">{{ $price->fuelType->code ?? '' }}</span>
                                                <span class="text-muted fs-7">Rating: {{ $price->fuelType->rating_value ?? '' }}</span>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="badge badge-light-primary fs-6">
                                                <i class="ki-duotone ki-dollar fs-4 me-1"></i>
                                                {{ number_format($price->price_per_unit, 2) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge badge-light-{{ $price->is_active ? 'success' : 'danger' }}">
                                                <i class="ki-duotone {{ $price->is_active ? 'ki-check-circle' : 'ki-cross' }} fs-5 me-1"></i>
                                                {{ $price->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="text-muted" title="{{ optional($price->created_at)->format('d M Y, h:i A') }}">
                                                {{ optional($price->created_at)->diffForHumans() }}
                                            </span>
                                        </td>

                                        <td class="text-end">
                                            <div class="dropdown">
                                                <a href="#" class="btn btn-sm btn-light-info dropdown-toggle" data-bs-toggle="dropdown">
                                                    <i class="ki-outline ki-setting-4 fs-2 me-2"></i> Actions
                                                </a>

                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="{{ route('fuel-unit-price.station-show', $price->fuel_station_uuid) }}" class="dropdown-item">
                                                            <i class="ki-duotone ki-eye fs-2 me-2 text-info">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                            View
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a href="{{ route('fuel-unit-price.edit', $price->fuel_station_uuid) }}" class="dropdown-item">
                                                            <i class="ki-duotone ki-pencil fs-2 me-2 text-warning">
                                                                <i class="path1"></i><i class="path2"></i>
                                                            </i>
                                                            Edit
                                                        </a>
                                                    </li>

                                                    <li><hr class="dropdown-divider"></li>

                                                    <li>
                                                        @if ($price->is_active)
                                                            <a
                                                                href="javascript:void(0)"
                                                                onclick="changeStatus('{{ route('fuel-unit-price-status-update', $price->uuid) }}', 'inactive')"
                                                                class="dropdown-item"
                                                            >
                                                                <i class="ki-duotone ki-cross-circle fs-2 me-2 text-danger">
                                                                    <i class="path1"></i><i class="path2"></i>
                                                                </i>
                                                                Deactivate
                                                            </a>
                                                        @else
                                                            <a
                                                                href="javascript:void(0)"
                                                                onclick="changeStatus('{{ route('fuel-unit-price-status-update', $price->uuid) }}', 'active')"
                                                                class="dropdown-item"
                                                            >
                                                                <i class="ki-duotone ki-check-circle fs-2 me-2 text-success">
                                                                    <i class="path1"></i><i class="path2"></i>
                                                                </i>
                                                                Activate
                                                            </a>
                                                        @endif
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-10">
                                            <i class="ki-duotone ki-information-2 fs-2x text-gray-400 mb-2"></i><br>
                                            No fuel unit prices found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $prices->appends($filters)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script>
    function changeStatus(url, status) {
        const action = status === 'active' ? 'activate' : 'deactivate';

        Swal.fire({
            title: `Are you sure you want to ${action} this price?`,
            text: "You can change this later.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Yes, ${action} it!`
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;

                const csrfToken = "{{ csrf_token() }}";

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';

                const csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = '_token';
                csrfField.value = csrfToken;

                const statusField = document.createElement('input');
                statusField.type = 'hidden';
                statusField.name = 'status';
                statusField.value = status;

                form.appendChild(methodField);
                form.appendChild(csrfField);
                form.appendChild(statusField);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush
