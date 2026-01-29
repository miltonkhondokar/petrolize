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
                            <small>filter cost entries</small>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('cost-entries.index') }}">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <select name="fuel_station_uuid" class="form-select form-select-solid">
                                    <option value="">All Stations</option>
                                    @foreach($fuelStations as $station)
                                        <option value="{{ $station->uuid }}"
                                            {{ isset($filters['fuel_station_uuid']) && $filters['fuel_station_uuid'] == $station->uuid ? 'selected' : '' }}>
                                            {{ $station->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="cost_category_uuid" class="form-select form-select-solid">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->uuid }}"
                                            {{ isset($filters['cost_category_uuid']) && $filters['cost_category_uuid'] == $category->uuid ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="is_active" class="form-select form-select-solid">
                                    <option value="">Status</option>
                                    <option value="1"
                                        {{ isset($filters['is_active']) && $filters['is_active'] == '1' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="0"
                                        {{ isset($filters['is_active']) && $filters['is_active'] == '0' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="expense_date" class="form-control form-control-solid"
                                    value="{{ $filters['expense_date'] ?? '' }}">
                            </div>
                            <div class="col-md-2 text-end">
                                <button class="btn btn-info w-100"><i
                                        class="ki-duotone ki-filter fs-3 me-2"></i>Filter</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('cost-entries.index') }}" class="btn btn-warning w-100">
                                    <i class="ki-duotone ki-reload fs-3 me-2"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cost Entries Table -->
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light-primary">
                    <h3 class="card-title fw-bold fs-3 mb-1">Cost Entries</h3>
                    <div class="d-flex">
                        @php
                            $totalAmount = $costEntries->sum('amount');
                        @endphp
                        <span class="badge badge-light-success fs-6 me-3">
                            Total: ${{ number_format($totalAmount, 2) }}
                        </span>
                        <a href="{{ route('cost-entries.create') }}" class="btn btn-sm btn-primary">
                            <i class="ki-outline ki-plus-circle fs-3 me-1"></i> Add New Entry
                        </a>
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
                                            <i class="ki-duotone ki-home-3 fs-2 me-2 text-primary">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Fuel Station
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-category fs-2 me-2 text-info">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Category
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
                                            <i class="ki-duotone ki-calendar-8 fs-2 me-2 text-warning">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Expense Date
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
                                            <i class="ki-duotone ki-shield-tick fs-2 me-2 text-success">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Status
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
                                @forelse ($costEntries as $index => $entry)
                                    <tr>
                                        <td>{{ $costEntries->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px symbol-circle me-3">
                                                    <div class="symbol-label bg-light-primary">
                                                        <i class="ki-duotone ki-home-3 fs-2 text-primary"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-gray-800">{{ $entry->fuelStation->name ?? 'N/A' }}</div>
                                                    <div class="text-muted fs-7">{{ $entry->fuelStation->location ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-info">{{ $entry->category->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-success fs-6 fw-bold">
                                                ${{ number_format($entry->amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $entry->expense_date->format('d M Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $entry->reference_no ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-{{ $entry->is_active ? 'success' : 'danger' }}">
                                                <i class="ki-duotone {{ $entry->is_active ? 'ki-check-circle' : 'ki-cross' }} fs-5 me-1"></i>
                                                {{ $entry->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted"
                                                title="{{ $entry->created_at->format('d M Y, h:i A') }}">
                                                {{ $entry->created_at->diffForHumans() }}
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
                                                        <a href="{{ route('cost-entries.show', $entry->uuid) }}" class="dropdown-item">
                                                            <i class="ki-duotone ki-eye fs-2 me-2 text-info">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                            View Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('cost-entries.edit', $entry->uuid) }}" class="dropdown-item">
                                                            <i class="ki-duotone ki-pencil fs-2 me-2 text-warning">
                                                                <i class="path1"></i><i class="path2"></i>
                                                            </i>
                                                            Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)"
                                                            onclick="deleteEntry('{{ route('cost-entries.destroy', $entry->uuid) }}')"
                                                            class="dropdown-item text-danger">
                                                            <i class="ki-duotone ki-trash fs-2 me-2 text-danger">
                                                                <i class="path1"></i><i class="path2"></i>
                                                            </i>
                                                            Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-10">
                                            <i class="ki-duotone ki-information-2 fs-2x text-gray-400 mb-2"></i><br>
                                            No cost entries found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $costEntries->appends($filters)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function deleteEntry(url) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This cost entry will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;

                    const csrfToken = "{{ csrf_token() }}";
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    const csrfField = document.createElement('input');
                    csrfField.type = 'hidden';
                    csrfField.name = '_token';
                    csrfField.value = csrfToken;

                    form.appendChild(methodField);
                    form.appendChild(csrfField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush