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
                        <i class="ki-duotone ki-link fs-2 text-primary me-2">
                            <i class="path1"></i><i class="path2"></i>
                        </i>
                        Fuel Station Fuel Type Assignment Details
                    </h3>
                    <a href="{{ route('fuel-station-fuel-type.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-left fs-3 me-2"></i>
                        Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex flex-column">
                                <!-- Assignment UUID -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Assignment ID:</span>
                                    <span class="text-muted fs-5">{{ $assignment->uuid }}</span>
                                </div>

                                <!-- Fuel Station -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Fuel Station:</span>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold fs-5 text-dark">{{ $assignment->fuelStation->name ?? 'N/A' }}</span>
                                        @if($assignment->fuelStation->location)
                                            <span class="text-muted fs-6">{{ $assignment->fuelStation->location }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Fuel Type -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Fuel Type:</span>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold fs-5 text-dark">{{ $assignment->fuelType->name ?? 'N/A' }}</span>
                                        <span class="text-muted fs-6">Code: {{ $assignment->fuelType->code ?? 'N/A' }}</span>
                                        @if($assignment->fuelType->description)
                                            <span class="text-muted fs-7 mt-1">{{ $assignment->fuelType->description }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Status:</span>
                                    <span class="badge badge-light-{{ $assignment->is_active ? 'success' : 'danger' }} fs-5">
                                        <i class="ki-duotone {{ $assignment->is_active ? 'ki-check-circle' : 'ki-cross' }} fs-4 me-1"></i>
                                        {{ $assignment->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <!-- Created At -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Created:</span>
                                    <span class="text-muted fs-5">{{ $assignment->created_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $assignment->created_at->diffForHumans() }})</span>
                                </div>

                                <!-- Last Updated -->
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Last Updated:</span>
                                    <span class="text-muted fs-5">{{ $assignment->updated_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $assignment->updated_at->diffForHumans() }})</span>
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
                                        <a href="{{ route('fuel-station-fuel-type.edit', $assignment->fuel_station_uuid) }}" 
                                           class="btn btn-warning btn-sm">
                                            <i class="ki-duotone ki-pencil fs-3 me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Edit Station Fuel Types
                                        </a>
                                        
                                        @if($assignment->is_active)
                                            <a href="javascript:void(0)"
                                                onclick="changeStatus('{{ route('fuel-station-fuel-type.status-update', $assignment->uuid) }}', 'inactive')"
                                                class="btn btn-danger btn-sm">
                                                <i class="ki-duotone ki-cross-circle fs-3 me-2">
                                                    <i class="path1"></i><i class="path2"></i>
                                                </i>
                                                Deactivate Assignment
                                            </a>
                                        @else
                                            <a href="javascript:void(0)"
                                                onclick="changeStatus('{{ route('fuel-station-fuel-type.status-update', $assignment->uuid) }}', 'active')"
                                                class="btn btn-success btn-sm">
                                                <i class="ki-duotone ki-check-circle fs-3 me-2">
                                                    <i class="path1"></i><i class="path2"></i>
                                                </i>
                                                Activate Assignment
                                            </a>
                                        @endif
                                        
                                        <a href="javascript:void(0)"
                                            onclick="confirmDelete('{{ route('fuel-station-fuel-type.destroy', $assignment->uuid) }}')"
                                            class="btn btn-danger btn-sm">
                                            <i class="ki-duotone ki-trash fs-3 me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Delete Assignment
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Fuel Station Info -->
                            <div class="card card-flush bg-light-danger mt-4">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="ki-duotone ki-home-3 fs-2 text-primary me-2">
                                            <i class="path1"></i><i class="path2"></i>
                                        </i>
                                        Fuel Station Info
                                    </h3>
                                </div>
                                <div class="card-body">
                                    @if($assignment->fuelStation)
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $assignment->fuelStation->name }}</span>
                                            @if($assignment->fuelStation->location)
                                                <span class="text-muted fs-7">{{ $assignment->fuelStation->location }}</span>
                                            @endif
                                            <span class="text-muted fs-7 mt-1">
                                                Status: 
                                                <span class="badge badge-light-{{ $assignment->fuelStation->is_active ? 'success' : 'danger' }}">
                                                    {{ $assignment->fuelStation->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </span>
                                            <a href="{{ route('fuel-station.show', $assignment->fuelStation->uuid) }}" 
                                               class="btn btn-link btn-sm p-0 mt-2">
                                                View Station Details →
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted">Station information not available</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Fuel Type Info -->
                            <div class="card card-flush bg-light-success mt-4">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="ki-duotone ki-gas fs-2 text-success me-2">
                                            <i class="path1"></i><i class="path2"></i>
                                        </i>
                                        Fuel Type Info
                                    </h3>
                                </div>
                                <div class="card-body">
                                    @if($assignment->fuelType)
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $assignment->fuelType->name }}</span>
                                            <span class="text-muted fs-7">Code: {{ $assignment->fuelType->code }}</span>
                                            @if($assignment->fuelType->description)
                                                <span class="text-muted fs-8 mt-1">{{ $assignment->fuelType->description }}</span>
                                            @endif
                                            <span class="text-muted fs-7 mt-1">
                                                Status: 
                                                <span class="badge badge-light-{{ $assignment->fuelType->is_active ? 'success' : 'danger' }}">
                                                    {{ $assignment->fuelType->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-muted">Fuel type information not available</span>
                                    @endif
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