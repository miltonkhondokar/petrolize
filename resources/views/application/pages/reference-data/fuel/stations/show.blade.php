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
                        <i class="ki-duotone ki-home-3 fs-2 text-primary me-2">
                            <i class="path1"></i><i class="path2"></i>
                        </i>
                        Fuel Station Details
                    </h3>
                    <a href="{{ route('fuel-station.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-left fs-3 me-2"></i>
                        Back to List
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex flex-column">
                                <!-- Station Name -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Station Name:</span>
                                    <span class="fw-bold fs-5 text-dark">{{ $fuelStation->name }}</span>
                                </div>

                                <!-- Geo -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Region / Governorate / Center / City:</span>
                                    <span class="fw-bold fs-5 text-dark">
                                        {{ $fuelStation->region->name ?? '-' }}
                                        @if($fuelStation->governorate) / {{ $fuelStation->governorate->name }} @endif
                                        @if($fuelStation->center) / {{ $fuelStation->center->name }} @endif
                                        @if($fuelStation->city) / {{ $fuelStation->city->name }} @endif
                                    </span>
                                </div>

                                <!-- Location -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Location:</span>
                                    <span class="fw-bold fs-5 text-dark">{{ $fuelStation->location ?? 'Not specified' }}</span>
                                </div>

                                <!-- Manager -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Manager:</span>
                                    @if($fuelStation->manager)
                                        <span class="fw-bold fs-5 text-dark">{{ $fuelStation->manager->name }}</span>
                                        <span class="text-muted ms-2">({{ $fuelStation->manager->email }})</span>
                                    @else
                                        <span class="badge badge-light-warning fs-5">Not Assigned</span>
                                    @endif
                                </div>

                                <!-- Status -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Status:</span>
                                    <span class="badge badge-light-{{ $fuelStation->is_active ? 'success' : 'danger' }} fs-5">
                                        <i class="ki-duotone {{ $fuelStation->is_active ? 'ki-check-circle' : 'ki-cross' }} fs-4 me-1"></i>
                                        {{ $fuelStation->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <!-- UUID -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Station ID:</span>
                                    <span class="text-muted fs-5">{{ $fuelStation->uuid }}</span>
                                </div>

                                <!-- Created At -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Created:</span>
                                    <span class="text-muted fs-5">{{ $fuelStation->created_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $fuelStation->created_at->diffForHumans() }})</span>
                                </div>

                                <!-- Last Updated -->
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Last Updated:</span>
                                    <span class="text-muted fs-5">{{ $fuelStation->updated_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $fuelStation->updated_at->diffForHumans() }})</span>
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
                                        <a href="{{ route('fuel-station.edit', $fuelStation->uuid) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="ki-duotone ki-pencil fs-3 me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Edit Station
                                        </a>

                                        @if($fuelStation->is_active)
                                            <a href="javascript:void(0)"
                                                onclick="changeStatus('{{ route('fuel-station-status-update', $fuelStation->uuid) }}', 'inactive')"
                                                class="btn btn-danger btn-sm">
                                                <i class="ki-duotone ki-cross-circle fs-3 me-2">
                                                    <i class="path1"></i><i class="path2"></i>
                                                </i>
                                                Deactivate Station
                                            </a>
                                        @else
                                            <a href="javascript:void(0)"
                                                onclick="changeStatus('{{ route('fuel-station-status-update', $fuelStation->uuid) }}', 'active')"
                                                class="btn btn-success btn-sm">
                                                <i class="ki-duotone ki-check-circle fs-3 me-2">
                                                    <i class="path1"></i><i class="path2"></i>
                                                </i>
                                                Activate Station
                                            </a>
                                        @endif
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
