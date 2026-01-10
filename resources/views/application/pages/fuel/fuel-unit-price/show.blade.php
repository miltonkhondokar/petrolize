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
                        <i class="ki-duotone ki-dollar fs-2 text-primary me-2">
                            <i class="path1"></i><i class="path2"></i>
                        </i>
                        Fuel Unit Price Details
                    </h3>
                    <a href="{{ route('fuel-unit-price.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-left fs-3 me-2"></i>
                        Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex flex-column">
                                <!-- Pump Information -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Fuel Station:</span>
                                    <span class="fw-bold fs-5 text-dark">{{ $price->pump->name ?? 'N/A' }}</span>
                                    @if($price->pump->location)
                                        <span class="text-muted ms-2">({{ $price->pump->location }})</span>
                                    @endif
                                </div>

                                <!-- Fuel Type Information -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Fuel Type:</span>
                                    <span class="fw-bold fs-5 text-dark">{{ $price->fuelType->name ?? 'N/A' }}</span>
                                    <span class="badge badge-light-info ms-2">{{ $price->fuelType->code ?? '' }}</span>
                                    <span class="text-muted ms-2">Rating: {{ $price->fuelType->rating_value ?? '' }}</span>
                                </div>

                                <!-- Price Per Unit -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Price Per Unit:</span>
                                    <span class="badge badge-light-primary fs-5">
                                        <i class="ki-duotone ki-dollar fs-4 me-1"></i>
                                        {{ number_format($price->price_per_unit, 2) }}
                                    </span>
                                    @if($price->fuelType->defaultUnit)
                                        <span class="text-muted ms-2">per {{ $price->fuelType->defaultUnit->abbreviation }}</span>
                                    @endif
                                </div>

                                <!-- Status -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Status:</span>
                                    <span class="badge badge-light-{{ $price->is_active ? 'success' : 'danger' }} fs-5">
                                        <i class="ki-duotone {{ $price->is_active ? 'ki-check-circle' : 'ki-cross' }} fs-4 me-1"></i>
                                        {{ $price->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <!-- UUID -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Price ID:</span>
                                    <span class="text-muted fs-5">{{ $price->uuid }}</span>
                                </div>

                                <!-- Created At -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Created:</span>
                                    <span class="text-muted fs-5">{{ $price->created_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $price->created_at->diffForHumans() }})</span>
                                </div>

                                <!-- Last Updated -->
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Last Updated:</span>
                                    <span class="text-muted fs-5">{{ $price->updated_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $price->updated_at->diffForHumans() }})</span>
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
                                        <a href="{{ route('fuel-unit-price.edit', $price->uuid) }}" class="btn btn-warning btn-sm">
                                            <i class="ki-duotone ki-pencil fs-3 me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Edit Price
                                        </a>
                                        
                                        @if($price->is_active)
                                            <a href="javascript:void(0)"
                                                onclick="changeStatus('{{ route('fuel-unit-price-status-update', $price->uuid) }}', 'inactive')"
                                                class="btn btn-danger btn-sm">
                                                <i class="ki-duotone ki-cross-circle fs-3 me-2">
                                                    <i class="path1"></i><i class="path2"></i>
                                                </i>
                                                Deactivate Price
                                            </a>
                                        @else
                                            <a href="javascript:void(0)"
                                                onclick="changeStatus('{{ route('fuel-unit-price-status-update', $price->uuid) }}', 'active')"
                                                class="btn btn-success btn-sm">
                                                <i class="ki-duotone ki-check-circle fs-3 me-2">
                                                    <i class="path1"></i><i class="path2"></i>
                                                </i>
                                                Activate Price
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