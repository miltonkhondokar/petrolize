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
                        <i class="ki-duotone ki-exclamation-triangle fs-2 text-primary me-2">
                            <i class="path1"></i><i class="path2"></i>
                        </i>
                        Fuel Station Complaint Details
                    </h3>
                    <a href="{{ route('complaint-category.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-left fs-3 me-2"></i>
                        Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex flex-column">
                                <!-- Fuel Station Information -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Fuel Station:</span>
                                    <span class="fw-bold fs-5 text-dark">{{ $complaint->fuelStation->name ?? 'N/A' }}</span>
                                    @if($complaint->fuelStation->location)
                                        <span class="text-muted ms-2">({{ $complaint->fuelStation->location }})</span>
                                    @endif
                                </div>

                                <!-- Category -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Category:</span>
                                    @if($complaint->category)
                                        <span class="badge badge-light-info fs-5">
                                            {{ ucwords(str_replace('_', ' ', $complaint->category)) }}
                                        </span>
                                    @else
                                        <span class="text-muted fs-5">Not specified</span>
                                    @endif
                                </div>

                                <!-- Title -->
                                <div class="d-flex align-items-start mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Title:</span>
                                    <span class="fw-bold fs-5 text-dark">{{ $complaint->title }}</span>
                                </div>

                                <!-- Description -->
                                <div class="d-flex align-items-start mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Description:</span>
                                    <span class="text-muted fs-5">{{ $complaint->description ?? 'No description provided' }}</span>
                                </div>

                                <!-- Status -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Status:</span>
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
                                    <span class="badge badge-light-{{ $statusColors[$complaint->status] ?? 'secondary' }} fs-5">
                                        <i class="ki-duotone 
                                            {{ $complaint->status == 'open' ? 'ki-clock' : 
                                               ($complaint->status == 'in_progress' ? 'ki-spinner' : 'ki-check') 
                                            }} fs-4 me-1"></i>
                                        {{ $statusLabels[$complaint->status] ?? $complaint->status }}
                                    </span>
                                </div>

                                <!-- Complaint Date -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Complaint Date:</span>
                                    <span class="text-muted fs-5">{{ \Carbon\Carbon::parse($complaint->complaint_date)->format('d M Y') }}</span>
                                </div>

                                <!-- Resolved Date -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Resolved Date:</span>
                                    @if($complaint->resolved_date)
                                        <span class="text-success fs-5">{{ \Carbon\Carbon::parse($complaint->resolved_date)->format('d M Y') }}</span>
                                    @else
                                        <span class="text-muted fs-5">Not resolved yet</span>
                                    @endif
                                </div>

                                <!-- Active Status -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Active Status:</span>
                                    <span class="badge badge-light-{{ $complaint->is_active ? 'success' : 'danger' }} fs-5">
                                        <i class="ki-duotone {{ $complaint->is_active ? 'ki-check-circle' : 'ki-cross' }} fs-4 me-1"></i>
                                        {{ $complaint->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <!-- UUID -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Complaint ID:</span>
                                    <span class="text-muted fs-5">{{ $complaint->uuid }}</span>
                                </div>

                                <!-- Created At -->
                                <div class="d-flex align-items-center mb-7">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Created:</span>
                                    <span class="text-muted fs-5">{{ $complaint->created_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $complaint->created_at->diffForHumans() }})</span>
                                </div>

                                <!-- Last Updated -->
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-gray-600 fs-5 me-2">Last Updated:</span>
                                    <span class="text-muted fs-5">{{ $complaint->updated_at->format('d M Y, h:i A') }}</span>
                                    <span class="text-muted ms-2">({{ $complaint->updated_at->diffForHumans() }})</span>
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
                                        <a href="{{ route('complaint-category.edit', $complaint->uuid) }}" class="btn btn-warning btn-sm">
                                            <i class="ki-duotone ki-pencil fs-3 me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Edit Complaint
                                        </a>
                                        
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-light-danger btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ki-duotone ki-clock fs-3 me-2"></i>
                                                Change Status
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="javascript:void(0)"
                                                        onclick="updateStatus('{{ route('complaint-category.status-update', $complaint->uuid) }}', 'open')"
                                                        class="dropdown-item">
                                                        <i class="ki-duotone ki-clock fs-2 me-2 text-danger">
                                                            <i class="path1"></i><i class="path2"></i>
                                                        </i>
                                                        Mark as Open
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)"
                                                        onclick="updateStatus('{{ route('complaint-category.status-update', $complaint->uuid) }}', 'in_progress')"
                                                        class="dropdown-item">
                                                        <i class="ki-duotone ki-spinner fs-2 me-2 text-warning">
                                                            <i class="path1"></i><i class="path2"></i>
                                                        </i>
                                                        Mark as In Progress
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)"
                                                        onclick="updateStatus('{{ route('complaint-category.status-update', $complaint->uuid) }}', 'resolved')"
                                                        class="dropdown-item">
                                                        <i class="ki-duotone ki-check fs-2 me-2 text-success">
                                                            <i class="path1"></i><i class="path2"></i>
                                                        </i>
                                                        Mark as Resolved
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
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