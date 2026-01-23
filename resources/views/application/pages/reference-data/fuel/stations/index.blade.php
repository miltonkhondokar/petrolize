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
                            <small>filter fuel stations</small>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('fuel-station.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="name" class="form-control form-control-solid"
                                    placeholder="Station Name" value="{{ $filters['name'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="location" class="form-control form-control-solid"
                                    placeholder="Location" value="{{ $filters['location'] ?? '' }}">
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
                                <a href="{{ route('fuel-station.index') }}" class="btn btn-warning ms-2">
                                    <i class="ki-duotone ki-reload fs-3 me-2"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Fuel Stations Table -->
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light-primary">
                    <h3 class="card-title fw-bold fs-3 mb-1">Fuel Stations List</h3>
                    <a href="{{ route('fuel-station.create') }}" class="btn btn-sm btn-primary">
                        <i class="ki-outline ki-plus-circle fs-3 me-1"></i> Add New Fuel Station
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
                                            Station Name
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-location fs-2 me-2 text-info">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Location
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-profile-circle fs-2 me-2 text-success">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Manager
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
                                @forelse ($fuelStations as $index => $fuelStation)
                                    <tr>
                                        <td>{{ $fuelStations->firstItem() + $index }}</td>
                                        <td class="text-dark fw-semibold">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    {{ $fuelStation->name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $fuelStation->location ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if($fuelStation->manager)
                                                <span class="badge badge-light-info">{{ $fuelStation->manager->name }}</span>
                                            @else
                                                <span class="badge badge-light-warning">Not Assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light-{{ $fuelStation->is_active ? 'success' : 'danger' }}">
                                                <i
                                                    class="ki-duotone {{ $fuelStation->is_active ? 'ki-check-circle' : 'ki-cross' }} fs-5 me-1"></i>
                                                {{ $fuelStation->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted"
                                                title="{{ $fuelStation->created_at->format('d M Y, h:i A') }}">
                                                {{ $fuelStation->created_at->diffForHumans() }}
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
                                                        <a href="{{ route('fuel-station.show', $fuelStation->uuid) }}" class="dropdown-item">
                                                            <i class="ki-duotone ki-eye fs-2 me-2 text-info">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                            View
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('fuel-station.edit', $fuelStation->uuid) }}" class="dropdown-item">
                                                            <i class="ki-duotone ki-pencil fs-2 me-2 text-warning">
                                                                <i class="path1"></i><i class="path2"></i>
                                                            </i>
                                                            Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        @if ($fuelStation->is_active)
                                                            <a href="javascript:void(0)"
                                                                onclick="changeStatus('{{ route('fuel-station-status-update', $fuelStation->uuid) }}', 'inactive')"
                                                                class="dropdown-item">
                                                                <i
                                                                    class="ki-duotone ki-cross-circle fs-2 me-2 text-danger">
                                                                    <i class="path1"></i><i class="path2"></i>
                                                                </i>
                                                                Deactivate
                                                            </a>
                                                        @else
                                                            <a href="javascript:void(0)"
                                                                onclick="changeStatus('{{ route('fuel-station-status-update', $fuelStation->uuid) }}', 'active')"
                                                                class="dropdown-item">
                                                                <i
                                                                    class="ki-duotone ki-check-circle fs-2 me-2 text-success">
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
                                            No fuel stations found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $fuelStations->appends($filters)->links('pagination::bootstrap-5') }}
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
                title: `Are you sure you want to ${action} this fuel station?`,
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