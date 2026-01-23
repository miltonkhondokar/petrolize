@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <!-- Cost Entry Details Card -->
            <div class="card shadow-sm mb-5">
                <div class="card-header bg-light-primary d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="ki-duotone ki-money fs-2 text-primary me-2">
                            <i class="path1"></i><i class="path2"></i>
                        </i>
                        Cost Entry Details
                    </h3>
                    <div class="d-flex">
                        <a href="{{ route('cost-entries.edit', $costEntry->uuid) }}" class="btn btn-sm btn-warning me-2">
                            <i class="ki-duotone ki-pencil fs-3 me-2"></i>
                            Edit
                        </a>
                        <a href="{{ route('cost-entries.index') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-arrow-left fs-3 me-2"></i>
                            Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex flex-column">
                                <!-- Fuel Station -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Fuel Station:</span>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px symbol-circle me-3">
                                            <div class="symbol-label bg-light-primary">
                                                <i class="ki-duotone ki-home-3 fs-2 text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="fw-bold fs-5 text-dark">{{ $costEntry->fuelStation->name ?? 'N/A' }}</span>
                                            @if($costEntry->fuelStation->location)
                                                <div class="text-muted fs-6">{{ $costEntry->fuelStation->location }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Cost Category -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Cost Category:</span>
                                    <span class="badge badge-light-info fs-5">{{ $costEntry->category->name ?? 'N/A' }}</span>
                                </div>

                                <!-- Amount -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Amount:</span>
                                    <span class="badge badge-light-success fs-3 fw-bold px-4 py-2">
                                        ${{ number_format($costEntry->amount, 2) }}
                                    </span>
                                </div>

                                <!-- Expense Date -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Expense Date:</span>
                                    <span class="fw-bold fs-5 text-dark">{{ $costEntry->expense_date->format('d M Y') }}</span>
                                </div>

                                <!-- Reference Number -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Reference No:</span>
                                    <span class="text-muted fs-5">{{ $costEntry->reference_no ?? 'N/A' }}</span>
                                </div>

                                <!-- Status -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Status:</span>
                                    <span class="badge badge-light-{{ $costEntry->is_active ? 'success' : 'danger' }} fs-5">
                                        <i class="ki-duotone {{ $costEntry->is_active ? 'ki-check-circle' : 'ki-cross' }} fs-4 me-1"></i>
                                        {{ $costEntry->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <!-- Note -->
                                @if($costEntry->note)
                                    <div class="d-flex align-items-start mb-7">
                                        <span class="fw-bold text-gray-600 fs-5 me-2">Note:</span>
                                        <span class="text-muted fs-5">{{ $costEntry->note }}</span>
                                    </div>
                                @endif

                                <!-- UUID -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Entry ID:</span>
                                    <span class="text-muted fs-5">{{ $costEntry->uuid }}</span>
                                </div>

                                <!-- Created At -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Created:</span>
                                    <span class="text-muted fs-5">{{ $costEntry->created_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $costEntry->created_at->diffForHumans() }})</span>
                                </div>

                                <!-- Last Updated -->
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Last Updated:</span>
                                    <span class="text-muted fs-5">{{ $costEntry->updated_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $costEntry->updated_at->diffForHumans() }})</span>
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
                                        <a href="{{ route('cost-entries.edit', $costEntry->uuid) }}" class="btn btn-warning btn-sm">
                                            <i class="ki-duotone ki-pencil fs-3 me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Edit Entry
                                        </a>

                                        <a href="javascript:void(0)"
                                            onclick="deleteEntry('{{ route('cost-entries.destroy', $costEntry->uuid) }}')"
                                            class="btn btn-danger btn-sm">
                                            <i class="ki-duotone ki-trash fs-3 me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Delete Entry
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
        function deleteEntry(url) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This cost entry will be permanently deleted!",
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