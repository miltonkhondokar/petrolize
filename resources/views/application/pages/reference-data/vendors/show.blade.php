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
                        <i class="ki-duotone ki-user fs-2 text-primary me-2">
                            <i class="path1"></i><i class="path2"></i>
                        </i>
                        Vendor Details
                    </h3>
                    <a href="{{ route('vendors.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-left fs-3 me-2"></i>
                        Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex flex-column">
                                <!-- Vendor Name -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Vendor Name:</span>
                                    <span class="fw-bold fs-5 text-dark">{{ $vendor->name }}</span>
                                </div>

                                <!-- Email -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Email:</span>
                                    @if($vendor->email)
                                        <a href="mailto:{{ $vendor->email }}" class="text-info fs-5">
                                            {{ $vendor->email }}
                                        </a>
                                    @else
                                        <span class="text-muted fs-5">Not provided</span>
                                    @endif
                                </div>

                                <!-- Phone -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Phone:</span>
                                    @if($vendor->phone)
                                        <a href="tel:{{ $vendor->phone }}" class="text-success fs-5">
                                            {{ $vendor->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted fs-5">Not provided</span>
                                    @endif
                                </div>

                                <!-- Address -->
                                <div class="d-flex align-items-start mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Address:</span>
                                    <span class="text-muted fs-5">{{ $vendor->address ?? 'No address provided' }}</span>
                                </div>

                                <!-- Status -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Status:</span>
                                    <span class="badge badge-light-{{ $vendor->is_active ? 'success' : 'danger' }} fs-5">
                                        <i class="ki-duotone {{ $vendor->is_active ? 'ki-check-circle' : 'ki-cross' }} fs-4 me-1"></i>
                                        {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <!-- Fuel Stocks Count -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Fuel Stocks Supplied:</span>
                                    <span class="badge badge-light-info fs-5">{{ $vendor->purchases_count }}</span>
                                </div>

                                <!-- UUID -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Vendor ID:</span>
                                    <span class="text-muted fs-5">{{ $vendor->uuid }}</span>
                                </div>

                                <!-- Created At -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Created:</span>
                                    <span class="text-muted fs-5">{{ $vendor->created_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $vendor->created_at->diffForHumans() }})</span>
                                </div>

                                <!-- Last Updated -->
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Last Updated:</span>
                                    <span class="text-muted fs-5">{{ $vendor->updated_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $vendor->updated_at->diffForHumans() }})</span>
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
                                        <a href="{{ route('vendors.edit', $vendor->uuid) }}" class="btn btn-warning btn-sm">
                                            <i class="ki-duotone ki-pencil fs-3 me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Edit Vendor
                                        </a>
                                        
                                        @if($vendor->is_active)
                                            <a href="javascript:void(0)"
                                                onclick="changeStatus('{{ route('vendor-status-update', $vendor->uuid) }}', 'inactive')"
                                                class="btn btn-danger btn-sm">
                                                <i class="ki-duotone ki-cross-circle fs-3 me-2">
                                                    <i class="path1"></i><i class="path2"></i>
                                                </i>
                                                Deactivate Vendor
                                            </a>
                                        @else
                                            <a href="javascript:void(0)"
                                                onclick="changeStatus('{{ route('vendor-status-update', $vendor->uuid) }}', 'active')"
                                                class="btn btn-success btn-sm">
                                                <i class="ki-duotone ki-check-circle fs-3 me-2">
                                                    <i class="path1"></i><i class="path2"></i>
                                                </i>
                                                Activate Vendor
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
                title: `Are you sure you want to ${action} this vendor?`,
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