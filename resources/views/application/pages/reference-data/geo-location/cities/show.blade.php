@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="card shadow-sm">
                <div class="card-header bg-light-danger d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="ki-duotone ki-city fs-2 text-primary me-2">
                            <i class="path1"></i><i class="path2"></i>
                        </i>
                        City Details
                    </h3>
                    <a href="{{ route('cities.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-left fs-3 me-2"></i>
                        Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex flex-column">
                                <!-- City Name -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">City Name:</span>
                                    <span class="fw-bold fs-5 text-dark">{{ $city->name }}</span>
                                </div>

                                <!-- Center -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Center:</span>
                                    @if($city->center)
                                        <span class="badge badge-light-info fs-5">{{ $city->center->name }}</span>
                                        <a href="#" class="ms-2 text-info" title="View Center Details">
                                            <i class="ki-duotone ki-eye fs-4"></i>
                                        </a>
                                    @else
                                        <span class="text-muted fs-5">No center assigned</span>
                                    @endif
                                </div>

                                <!-- Governorate -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Governorate:</span>
                                    @if($city->center && $city->center->governorate)
                                        <span class="badge badge-light-primary fs-5">{{ $city->center->governorate->name }}</span>
                                        <a href="#" class="ms-2 text-primary" title="View Governorate Details">
                                            <i class="ki-duotone ki-eye fs-4"></i>
                                        </a>
                                    @else
                                        <span class="text-muted fs-5">N/A</span>
                                    @endif
                                </div>

                                <!-- Region -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Region:</span>
                                    @if($city->center && $city->center->governorate && $city->center->governorate->region)
                                        <span class="badge badge-light-warning fs-5">{{ $city->center->governorate->region->name }}</span>
                                        <a href="#" class="ms-2 text-warning" title="View Region Details">
                                            <i class="ki-duotone ki-eye fs-4"></i>
                                        </a>
                                    @else
                                        <span class="text-muted fs-5">N/A</span>
                                    @endif
                                </div>

                                <!-- Status -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Status:</span>
                                    <span class="badge badge-light-{{ $city->is_active ? 'success' : 'danger' }} fs-5">
                                        <i class="ki-duotone {{ $city->is_active ? 'ki-check-circle' : 'ki-cross' }} fs-4 me-1"></i>
                                        {{ $city->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <!-- UUID -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">City ID:</span>
                                    <span class="text-muted fs-5">{{ $city->uuid }}</span>
                                </div>

                                <!-- Created At -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Created:</span>
                                    <span class="text-muted fs-5">{{ $city->created_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $city->created_at->diffForHumans() }})</span>
                                </div>

                                <!-- Last Updated -->
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Last Updated:</span>
                                    <span class="text-muted fs-5">{{ $city->updated_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $city->updated_at->diffForHumans() }})</span>
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
                                        Quick Actions
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-column gap-3">
                                        <a href="{{ route('cities.edit', $city->uuid) }}" class="btn btn-warning btn-sm">
                                            <i class="ki-duotone ki-pencil fs-3 me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Edit City
                                        </a>
                                        
                                        @if($city->is_active)
                                            <a href="javascript:void(0)"
                                                onclick="changeStatus('{{ route('city-status-update', $city->uuid) }}', 'inactive')"
                                                class="btn btn-danger btn-sm">
                                                <i class="ki-duotone ki-cross-circle fs-3 me-2">
                                                    <i class="path1"></i><i class="path2"></i>
                                                </i>
                                                Deactivate City
                                            </a>
                                        @else
                                            <a href="javascript:void(0)"
                                                onclick="changeStatus('{{ route('city-status-update', $city->uuid) }}', 'active')"
                                                class="btn btn-success btn-sm">
                                                <i class="ki-duotone ki-check-circle fs-3 me-2">
                                                    <i class="path1"></i><i class="path2"></i>
                                                </i>
                                                Activate City
                                            </a>
                                        @endif

                                        <a href="javascript:void(0)" onclick="deleteCity('{{ route('cities.destroy', $city->uuid) }}')" class="btn btn-danger btn-sm">
                                            <i class="ki-duotone ki-trash fs-3 me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                                <i class="path3"></i>
                                                <i class="path4"></i>
                                                <i class="path5"></i>
                                            </i>
                                            Delete City
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
                title: `Are you sure you want to ${action} this city?`,
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

        function deleteCity(url) {
            Swal.fire({
                title: 'Are you sure you want to delete this city?',
                text: "This action cannot be undone!",
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