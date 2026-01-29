@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-flush shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light-danger">
                            <h3 class="card-title">
                                <i class="ki-duotone ki-gas-station fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Fuel Type Details
                            </h3>
                            <div>
                                <a href="{{ route('fuel.edit', $fuel->uuid) }}" class="btn btn-sm btn-warning me-2">
                                    <i class="ki-duotone ki-pencil fs-3 me-2"></i>Edit
                                </a>
                                <a href="{{ route('fuel.index') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-arrow-left fs-3 me-2"></i>Back to List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <div class="mb-10">
                                        <h4 class="text-gray-800 mb-5">
                                            <i class="ki-duotone ki-information fs-2 text-primary me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Basic Information
                                        </h4>
                                        <div class="table-responsive">
                                            <table class="table table-row-dashed table-borderless">
                                                <tr>
                                                    <th class="ps-0" width="35%">Name:</th>
                                                    <td class="text-dark fw-bold">{{ $fuel->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0">Code:</th>
                                                    <td>
                                                        <span class="badge badge-light-info">{{ $fuel->code }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0">Rating Value:</th>
                                                    <td>
                                                        <span class="badge badge-light-primary">{{ $fuel->rating_value }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0">UUID:</th>
                                                    <td class="text-muted">{{ $fuel->uuid }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Information -->
                                <div class="col-md-6">
                                    <div class="mb-10">
                                        <h4 class="text-gray-800 mb-5">
                                            <i class="ki-duotone ki-setting-3 fs-2 text-primary me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Additional Information
                                        </h4>
                                        <div class="table-responsive">
                                            <table class="table table-row-dashed table-borderless">
                                                <tr>
                                                    <th class="ps-0" width="35%">Default Unit:</th>
                                                    <td>
                                                        @if($fuel->defaultUnit)
                                                            {{ $fuel->defaultUnit->name }} ({{ $fuel->defaultUnit->symbol }})
                                                        @else
                                                            <span class="text-muted">Not set</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0">Status:</th>
                                                    <td>
                                                        <span class="badge badge-light-{{ $fuel->is_active ? 'success' : 'danger' }}">
                                                            <i class="ki-duotone {{ $fuel->is_active ? 'ki-check-circle' : 'ki-cross' }} fs-5 me-1"></i>
                                                            {{ $fuel->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0">Created At:</th>
                                                    <td class="text-muted">
                                                        {{ $fuel->created_at->format('d M Y, h:i A') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0">Updated At:</th>
                                                    <td class="text-muted">
                                                        {{ $fuel->updated_at->format('d M Y, h:i A') }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Associated Data -->
                            <div class="row mt-10">
                                <div class="col-md-12">
                                    <h4 class="text-gray-800 mb-5">
                                        <i class="ki-duotone ki-chart-line fs-2 text-primary me-2">
                                            <i class="path1"></i><i class="path2"></i>
                                        </i>
                                        Associated Data
                                    </h4>
                                    <div class="row g-5">
                                        <div class="col-md-4">
                                            <div class="card card-flush bg-light-danger">
                                                <div class="card-body text-center">
                                                    <i class="fa-solid fa-gas-pump fs-4x text-info mb-3"></i>
                                                    <h4 class="text-gray-800 mb-1">{{ $fuel->pumps->count() }}</h4>
                                                    <p class="text-gray-600">Associated Pumps</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card card-flush bg-light-info">
                                                <div class="card-body text-center">
                                                    <i class="ki-duotone ki-dollar fs-4x text-info mb-3">
                                                        <i class="path1"></i><i class="path2"></i>
                                                    </i>
                                                    <h4 class="text-gray-800 mb-1">{{ $fuel->pumpFuelPrices->count() }}</h4>
                                                    <p class="text-gray-600">Price Records</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card card-flush bg-light-success">
                                                <div class="card-body text-center">
                                                    <i class="ki-duotone ki-chart-simple fs-4x text-success mb-3">
                                                        <i class="path1"></i><i class="path2"></i>
                                                    </i>
                                                    <h4 class="text-gray-800 mb-1">{{ $fuel->pumpFuelReadings->count() }}</h4>
                                                    <p class="text-gray-600">Reading Records</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-10">
                                <div class="col-md-12 text-end">
                                    @if($fuel->is_active)
                                        <a href="javascript:void(0)" 
                                           onclick="changeStatus('{{ route('fuel-status-update', $fuel->uuid) }}', 'inactive')"
                                           class="btn btn-sm btn-danger me-2">
                                            <i class="ki-duotone ki-cross-circle fs-3 me-2"></i>
                                            Deactivate
                                        </a>
                                    @else
                                        <a href="javascript:void(0)" 
                                           onclick="changeStatus('{{ route('fuel-status-update', $fuel->uuid) }}', 'active')"
                                           class="btn btn-sm btn-success me-2">
                                            <i class="ki-duotone ki-check-circle fs-3 me-2"></i>
                                            Activate
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
@endsection

@push('scripts')
    <script>

        function changeStatus(url, status) {
            const action = status === 'active' ? 'activate' : 'deactivate';
            Swal.fire({
                title: `Are you sure you want to ${action} this fuel type?`,
                text: "This will affect all associated pumps.",
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