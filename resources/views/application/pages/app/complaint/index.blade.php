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
                            <small>Filter Fuel Station Complaints</small>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('complaints.index') }}">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <select name="fuel_station_uuid" class="form-select form-select-solid">
                                    <option value="">Select Fuel Station</option>
                                    @foreach ($fuelStations as $fuelStation)
                                        <option value="{{ $fuelStation->uuid }}"
                                            {{ isset($filters['fuel_station_uuid']) && $filters['fuel_station_uuid'] == $fuelStation->uuid ? 'selected' : '' }}>
                                            {{ $fuelStation->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="category" class="form-select form-select-solid">
                                    <option value="">Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}"
                                            {{ isset($filters['category']) && $filters['category'] == $category ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $category)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select form-select-solid">
                                    <option value="">Status</option>
                                    <option value="open"
                                        {{ isset($filters['status']) && $filters['status'] == 'open' ? 'selected' : '' }}>
                                        Open
                                    </option>
                                    <option value="in_progress"
                                        {{ isset($filters['status']) && $filters['status'] == 'in_progress' ? 'selected' : '' }}>
                                        In Progress
                                    </option>
                                    <option value="resolved"
                                        {{ isset($filters['status']) && $filters['status'] == 'resolved' ? 'selected' : '' }}>
                                        Resolved
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="is_active" class="form-select form-select-solid">
                                    <option value="">Active Status</option>
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
                            <div class="col-md-2 text-end">
                                <button class="btn btn-info w-100"><i
                                        class="ki-duotone ki-filter fs-3 me-2"></i>Filter</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('complaints.index') }}" class="btn btn-warning w-100">
                                    <i class="ki-duotone ki-reload fs-3 me-2"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Fuel Station Complaints Table -->
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light-primary">
                    <h3 class="card-title fw-bold fs-3 mb-1">Fuel Station Complaints List</h3>
                    <a href="{{ route('complaints.create') }}" class="btn btn-sm btn-primary">
                        <i class="ki-outline ki-plus-circle fs-3 me-1"></i> Add New Complaint
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
                                            <i class="ki-duotone ki-category fs-2 me-2 text-info">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Category
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-note fs-2 me-2 text-success">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Title
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-status fs-2 me-2 text-warning">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Status
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-calendar-8 fs-2 me-2 text-danger">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Complaint Date
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-calendar-check fs-2 me-2 text-success">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Resolved Date
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-shield-tick fs-2 me-2 text-success">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Active
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
                                @forelse ($complaints as $index => $complaint)
                                    <tr>
                                        <td>{{ $complaints->firstItem() + $index }}</td>
                                        <td class="text-dark fw-semibold">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    {{ $complaint->fuelStation->name ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($complaint->category)
                                                <span class="badge badge-light-info">
                                                    {{ ucwords(str_replace('_', ' ', $complaint->category->name)) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-dark fw-semibold">
                                                {{ Str::limit($complaint->title, 30) }}
                                                @if($complaint->description)
                                                    <div class="text-muted fs-7">{{ Str::limit($complaint->description, 20) }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'open' => 'danger',
                                                    'in_progress' => 'warning',
                                                    'resolved' => 'success'
                                                ];
                                                $statusLabels = [
                                                    'open' => 'Open',
                                                    'in_progress' => 'In Progress',
                                                    'resolved' => 'Resolved'
                                                ];
                                            @endphp
                                            <span class="badge badge-light-{{ $statusColors[$complaint->status] ?? 'secondary' }}">
                                                {{ $statusLabels[$complaint->status] ?? $complaint->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                {{ \Carbon\Carbon::parse($complaint->complaint_date)->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($complaint->resolved_date)
                                                <span class="text-success">
                                                    {{ \Carbon\Carbon::parse($complaint->resolved_date)->format('d M Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light-{{ $complaint->is_active ? 'success' : 'danger' }}">
                                                <i
                                                    class="ki-duotone {{ $complaint->is_active ? 'ki-check-circle' : 'ki-cross' }} fs-5 me-1"></i>
                                                {{ $complaint->is_active ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted"
                                                title="{{ $complaint->created_at->format('d M Y, h:i A') }}">
                                                {{ $complaint->created_at->diffForHumans() }}
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
                                                        <a href="{{ route('complaints.show', $complaint->uuid) }}" class="dropdown-item">
                                                            <i class="ki-duotone ki-eye fs-2 me-2 text-info">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                            View
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('complaints.edit', $complaint->uuid) }}" class="dropdown-item">
                                                            <i class="ki-duotone ki-pencil fs-2 me-2 text-warning">
                                                                <i class="path1"></i><i class="path2"></i>
                                                            </i>
                                                            Edit
                                                        </a>
                                                    </li>
                                                    <li class="dropdown-divider"></li>
                                                    <li>
                                                        <a href="javascript:void(0)"
                                                            onclick="updateStatus('{{ route('complaints.status-update', $complaint->uuid) }}', 'open')"
                                                            class="dropdown-item">
                                                            <i class="ki-duotone ki-clock fs-2 me-2 text-danger">
                                                                <i class="path1"></i><i class="path2"></i>
                                                            </i>
                                                            Mark as Open
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)"
                                                            onclick="updateStatus('{{ route('complaints.status-update', $complaint->uuid) }}', 'in_progress')"
                                                            class="dropdown-item">
                                                            <i class="ki-duotone ki-spinner fs-2 me-2 text-warning">
                                                                <i class="path1"></i><i class="path2"></i>
                                                            </i>
                                                            Mark as In Progress
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)"
                                                            onclick="updateStatus('{{ route('complaints.status-update', $complaint->uuid) }}', 'resolved')"
                                                            class="dropdown-item">
                                                            <i class="ki-duotone ki-check fs-2 me-2 text-success">
                                                                <i class="path1"></i><i class="path2"></i>
                                                            </i>
                                                            Mark as Resolved
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-10">
                                            <i class="ki-duotone ki-information-2 fs-2x text-gray-400 mb-2"></i><br>
                                            No fuel station complaints found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $complaints->appends($filters)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateStatus(url, status) {
            const statusLabels = {
                'open': 'Open',
                'in_progress': 'In Progress',
                'resolved': 'Resolved'
            };
            
            Swal.fire({
                title: `Mark as ${statusLabels[status]}?`,
                text: "This will update the complaint status.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Yes, mark as ${statusLabels[status]}`
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