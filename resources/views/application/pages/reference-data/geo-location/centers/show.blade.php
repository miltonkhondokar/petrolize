@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="card shadow-sm">
                <div class="card-header bg-light-primary d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="ki-duotone ki-shop fs-2 text-primary me-2">
                            <i class="path1"></i><i class="path2"></i>
                        </i>
                        Center Details
                    </h3>
                    <a href="{{ route('centers.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-left fs-3 me-2"></i>
                        Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex flex-column">
                                <!-- Center Name -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Center Name:</span>
                                    <span class="fw-bold fs-5 text-dark">{{ $center->name }}</span>
                                </div>

                                <!-- Governorate -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Governorate:</span>
                                    @if($center->governorate)
                                        <span class="badge badge-light-info fs-5">{{ $center->governorate->name }}</span>
                                        <a href="{{ route('governorates.show', $center->governorate->uuid) }}" class="ms-2 text-info" title="View Governorate Details">
                                            <i class="ki-duotone ki-eye fs-4"></i>
                                        </a>
                                    @else
                                        <span class="text-muted fs-5">No governorate assigned</span>
                                    @endif
                                </div>

                                <!-- Region -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Region:</span>
                                    @if($center->governorate && $center->governorate->region)
                                        <span class="badge badge-light-primary fs-5">{{ $center->governorate->region->name }}</span>
                                        <a href="{{ route('regions.show', $center->governorate->region->uuid) }}" class="ms-2 text-primary" title="View Region Details">
                                            <i class="ki-duotone ki-eye fs-4"></i>
                                        </a>
                                    @else
                                        <span class="text-muted fs-5">N/A</span>
                                    @endif
                                </div>

                                <!-- Cities Count -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Total Cities:</span>
                                    <span class="badge badge-light-info fs-5">{{ $center->cities->count() }}</span>
                                </div>

                                <!-- Status -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Status:</span>
                                    <span class="badge badge-light-{{ $center->is_active ? 'success' : 'danger' }} fs-5">
                                        <i class="ki-duotone {{ $center->is_active ? 'ki-check-circle' : 'ki-cross' }} fs-4 me-1"></i>
                                        {{ $center->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <!-- UUID -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Center ID:</span>
                                    <span class="text-muted fs-5">{{ $center->uuid }}</span>
                                </div>

                                <!-- Created At -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Created:</span>
                                    <span class="text-muted fs-5">{{ $center->created_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $center->created_at->diffForHumans() }})</span>
                                </div>

                                <!-- Last Updated -->
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Last Updated:</span>
                                    <span class="text-muted fs-5">{{ $center->updated_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $center->updated_at->diffForHumans() }})</span>
                                </div>
                            </div>

                            <!-- Cities Section -->
                            @if($center->cities->count() > 0)
                                <div class="mt-10">
                                    <div class="separator separator-dashed my-5"></div>
                                    <h4 class="text-dark mb-5">
                                        <i class="ki-duotone ki-city fs-2 text-warning me-2">
                                            <i class="path1"></i><i class="path2"></i>
                                        </i>
                                        Cities in {{ $center->name }}
                                    </h4>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-rounded table-striped border gy-7 gs-7">
                                            <thead>
                                                <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                                    <th>Name</th>
                                                    <th>Status</th>
                                                    <th>Created At</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($center->cities as $city)
                                                    <tr>
                                                        <td>{{ $city->name }}</td>
                                                        <td>
                                                            <span class="badge badge-light-{{ $city->is_active ? 'success' : 'danger' }}">
                                                                {{ $city->is_active ? 'Active' : 'Inactive' }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $city->created_at->format('d M Y') }}</td>
                                                        <td class="text-end">
                                                            <a href="{{ route('cities.edit', $city->uuid) }}" class="btn btn-sm btn-light-warning">
                                                                <i class="ki-duotone ki-pencil fs-3"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card card-flush bg-light-info">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="ki-duotone ki-information fs-2 text-info me-2">
                                            <i class="path1"></i><i class="path2"></i>
                                        </i>
                                        Quick Actions
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-column gap-3">
                                        <a href="{{ route('centers.edit', $center->uuid) }}" class="btn btn-warning btn-sm">
                                            <i class="ki-duotone ki-pencil fs-3 me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Edit Center
                                        </a>
                                        
                                        @if($center->is_active)
                                            <a href="javascript:void(0)"
                                                onclick="changeStatus('{{ route('center-status-update', $center->uuid) }}', 'inactive')"
                                                class="btn btn-danger btn-sm">
                                                <i class="ki-duotone ki-cross-circle fs-3 me-2">
                                                    <i class="path1"></i><i class="path2"></i>
                                                </i>
                                                Deactivate Center
                                            </a>
                                        @else
                                            <a href="javascript:void(0)"
                                                onclick="changeStatus('{{ route('center-status-update', $center->uuid) }}', 'active')"
                                                class="btn btn-success btn-sm">
                                                <i class="ki-duotone ki-check-circle fs-3 me-2">
                                                    <i class="path1"></i><i class="path2"></i>
                                                </i>
                                                Activate Center
                                            </a>
                                        @endif

                                        <a href="javascript:void(0)" onclick="deleteCenter('{{ route('centers.destroy', $center->uuid) }}')" class="btn btn-danger btn-sm">
                                            <i class="ki-duotone ki-trash fs-3 me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                                <i class="path3"></i>
                                                <i class="path4"></i>
                                                <i class="path5"></i>
                                            </i>
                                            Delete Center
                                        </a>

                                        <a href="{{ route('cities.create') }}?center_uuid={{ $center->uuid }}" class="btn btn-info btn-sm">
                                            <i class="ki-duotone ki-plus fs-3 me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Add City
                                        </a>
                                    </div>
                                </div>
                            </div>
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
                title: `Are you sure you want to ${action} this center?`,
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

        function deleteCenter(url) {
            Swal.fire({
                title: 'Are you sure you want to delete this center?',
                text: "This will also delete all associated cities!",
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