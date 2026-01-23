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
                            <small>filter fuel station fuel type assignments</small>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('fuel-station-fuel-type.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select name="fuel_station_uuid" class="form-select form-select-solid">
                                    <option value="">All Fuel Stations</option>
                                    @foreach($fuelStations as $station)
                                        <option value="{{ $station->uuid }}"
                                            {{ isset($filters['fuel_station_uuid']) && $filters['fuel_station_uuid'] == $station->uuid ? 'selected' : '' }}>
                                            {{ $station->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="fuel_type_uuid" class="form-select form-select-solid">
                                    <option value="">All Fuel Types</option>
                                    @foreach($fuelTypes as $fuelType)
                                        <option value="{{ $fuelType->uuid }}"
                                            {{ isset($filters['fuel_type_uuid']) && $filters['fuel_type_uuid'] == $fuelType->uuid ? 'selected' : '' }}>
                                            {{ $fuelType->name }}
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
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-info">
                                    <i class="ki-duotone ki-filter fs-3 me-2"></i>Filter
                                </button>
                                <a href="{{ route('fuel-station-fuel-type.index') }}" class="btn btn-warning ms-2">
                                    <i class="ki-duotone ki-reload fs-3 me-2"></i>Reset
                                </a>
                                <a href="{{ route('fuel-station-fuel-type.create') }}" class="btn btn-primary ms-2">
                                    <i class="ki-duotone ki-plus-circle fs-3 me-2"></i>Assign Fuel Types
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Fuel Station Fuel Types Table -->
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light-primary">
                    <h3 class="card-title fw-bold fs-3 mb-1">Fuel Station Fuel Type Assignments</h3>
                    <a href="{{ route('fuel-station-fuel-type.create') }}" class="btn btn-sm btn-primary">
                        <i class="ki-outline ki-plus-circle fs-3 me-1"></i> Assign Fuel Types
                    </a>
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
                                            <i class="ki-duotone ki-gas fs-2 me-2 text-info">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Fuel Type
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
                                @forelse ($assignments as $index => $assignment)
                                    <tr>
                                        <td>{{ $assignments->firstItem() + $index }}</td>
                                        <td class="text-dark fw-semibold">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <span class="fw-bold">{{ $assignment->fuelStation->name ?? 'N/A' }}</span>
                                                    @if($assignment->fuelStation->location)
                                                        <div class="text-muted fs-7">{{ $assignment->fuelStation->location }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-35px symbol-circle me-3">
                                                    <span class="symbol-label bg-light-primary text-primary fs-6">
                                                        {{ substr($assignment->fuelType->name ?? 'FT', 0, 2) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="fw-bold">{{ $assignment->fuelType->name ?? 'N/A' }}</span>
                                                    <div class="text-muted fs-7">{{ $assignment->fuelType->code ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-{{ $assignment->is_active ? 'success' : 'danger' }}">
                                                <i
                                                    class="ki-duotone {{ $assignment->is_active ? 'ki-check-circle' : 'ki-cross' }} fs-5 me-1"></i>
                                                {{ $assignment->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted"
                                                title="{{ $assignment->created_at->format('d M Y, h:i A') }}">
                                                {{ $assignment->created_at->diffForHumans() }}
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
                                                        <a href="{{ route('fuel-station-fuel-type.show', $assignment->uuid) }}" class="dropdown-item">
                                                            <i class="ki-duotone ki-eye fs-2 me-2 text-info">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                            View
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('fuel-station-fuel-type.edit', $assignment->fuel_station_uuid) }}" 
                                                           class="dropdown-item">
                                                            <i class="ki-duotone ki-pencil fs-2 me-2 text-warning">
                                                                <i class="path1"></i><i class="path2"></i>
                                                            </i>
                                                            Edit Station Fuel Types
                                                        </a>
                                                    </li>
                                                    <li>
                                                        @if ($assignment->is_active)
                                                            <a href="javascript:void(0)"
                                                                onclick="changeStatus('{{ route('fuel-station-fuel-type.status-update', $assignment->uuid) }}', 'inactive')"
                                                                class="dropdown-item">
                                                                <i
                                                                    class="ki-duotone ki-cross-circle fs-2 me-2 text-danger">
                                                                    <i class="path1"></i><i class="path2"></i>
                                                                </i>
                                                                Deactivate
                                                            </a>
                                                        @else
                                                            <a href="javascript:void(0)"
                                                                onclick="changeStatus('{{ route('fuel-station-fuel-type.status-update', $assignment->uuid) }}', 'active')"
                                                                class="dropdown-item">
                                                                <i
                                                                    class="ki-duotone ki-check-circle fs-2 me-2 text-success">
                                                                    <i class="path1"></i><i class="path2"></i>
                                                                </i>
                                                                Activate
                                                            </a>
                                                        @endif
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)"
                                                            onclick="confirmDelete('{{ route('fuel-station-fuel-type.destroy', $assignment->uuid) }}')"
                                                            class="dropdown-item text-danger">
                                                            <i
                                                                class="ki-duotone ki-trash fs-2 me-2 text-danger">
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
                                        <td colspan="6" class="text-center text-muted py-10">
                                            <i class="ki-duotone ki-information-2 fs-2x text-gray-400 mb-2"></i><br>
                                            No fuel station fuel type assignments found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $assignments->appends($filters)->links('pagination::bootstrap-5') }}
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
                title: `Are you sure you want to ${action} this assignment?`,
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

        function confirmDelete(url) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
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