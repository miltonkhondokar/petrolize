@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="row">
                <div class="col-md-12">

                    {{-- Alerts --}}
                    @if ($errors->any())
                        <div class="alert alert-warning d-flex align-items-center p-5 mb-5">
                            <i class="ki-duotone ki-warning fs-2hx text-warning me-4">
                                <i class="path1"></i><i class="path2"></i>
                            </i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-warning">Validation Errors</h4>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success d-flex align-items-center p-5 mb-5">
                            <i class="ki-duotone ki-check fs-2hx text-success me-4">
                                <i class="path1"></i><i class="path2"></i>
                            </i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-success">Success!</h4>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger d-flex align-items-center p-5 mb-5">
                            <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                                <i class="path1"></i><i class="path2"></i>
                            </i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-danger">Error!</h4>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="card card-flush shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light-primary">
                            <h3 class="card-title">
                                <i class="ki-duotone ki-eye fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Fuel Station Capacity Details
                            </h3>

                            <div class="d-flex gap-2">
                                <a href="{{ route('fuel-capacity.edit', $row->uuid) }}" class="btn btn-sm btn-warning">
                                    <i class="ki-duotone ki-pencil fs-3 me-2">
                                        <i class="path1"></i><i class="path2"></i>
                                    </i>
                                    Edit
                                </a>

                                <a href="{{ route('fuel-capacity.index') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-arrow-left fs-3 me-2"></i>
                                    Back to List
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                {{-- Basic Information --}}
                                <div class="col-md-6">
                                    <div class="mb-8">
                                        <h4 class="text-dark mb-4">
                                            <i class="ki-duotone ki-information-5 fs-2 me-2 text-info">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Basic Information
                                        </h4>
                                        
                                        <div class="d-flex flex-column gap-4">
                                            <div>
                                                <label class="form-label text-muted">Fuel Station</label>
                                                <div class="d-flex align-items-center">
                                                    <i class="ki-duotone ki-home-3 fs-2 me-3 text-primary">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <div>
                                                        <div class="fw-bold fs-5">{{ $row->station->name ?? 'N/A' }}</div>
                                                        @if (!empty($row->station?->location))
                                                            <div class="text-muted fs-7">{{ $row->station->location }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <label class="form-label text-muted">Fuel Type</label>
                                                <div class="d-flex align-items-center">
                                                    <i class="ki-duotone ki-gas-station fs-2 me-3 text-info">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <div>
                                                        <div class="fw-bold fs-5">{{ $row->fuelType->name ?? 'N/A' }}</div>
                                                        @if (!empty($row->fuelType?->code))
                                                            <div class="text-muted fs-7">Code: {{ $row->fuelType->code }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Status & Dates --}}
                                <div class="col-md-6">
                                    <div class="mb-8">
                                        <h4 class="text-dark mb-4">
                                            <i class="ki-duotone ki-calendar-8 fs-2 me-2 text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Status & Dates
                                        </h4>
                                        
                                        <div class="d-flex flex-column gap-4">
                                            <div>
                                                <label class="form-label text-muted">Status</label>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-lg badge-light-{{ $row->is_active ? 'success' : 'danger' }} d-inline-flex align-items-center px-3 py-2">
                                                        <i class="ki-duotone {{ $row->is_active ? 'ki-check-circle' : 'ki-cross' }} fs-2 me-2"></i>
                                                        {{ $row->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <label class="form-label text-muted">Effective From Date</label>
                                                <div class="d-flex align-items-center">
                                                    <i class="ki-duotone ki-calendar-8 fs-2 me-3 text-primary">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <div class="fw-bold fs-5">
                                                        {{ optional($row->effective_from)->format('d M Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <label class="form-label text-muted">Created At</label>
                                                <div class="d-flex align-items-center">
                                                    <i class="ki-duotone ki-clock fs-2 me-3 text-muted">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <div>
                                                        <div class="fw-bold fs-7">{{ optional($row->created_at)->format('d M Y, h:i A') }}</div>
                                                        <div class="text-muted fs-8">({{ optional($row->created_at)->diffForHumans() }})</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Capacity Details --}}
                                <div class="col-md-12">
                                    <div class="separator separator-dashed my-8"></div>
                                    
                                    <div class="mb-8">
                                        <h4 class="text-dark mb-4">
                                            <i class="ki-duotone ki-water fs-2 me-2 text-info">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Capacity Details
                                        </h4>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="bg-light-primary rounded p-6">
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <span class="text-muted">Capacity (Liters)</span>
                                                        <i class="ki-duotone ki-water fs-2x text-primary">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </div>
                                                    <div class="fw-bold fs-2 text-primary">
                                                        {{ number_format($row->capacity_liters, 3) }} <span class="fs-4 text-muted">L</span>
                                                    </div>
                                                    <div class="text-muted mt-2">Total storage capacity for this fuel type</div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="bg-light-info rounded p-6">
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <span class="text-muted">Note</span>
                                                        <i class="ki-duotone ki-note fs-2x text-info">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </div>
                                                    <div class="fw-bold fs-5">
                                                        {{ $row->note ?? 'No notes provided' }}
                                                    </div>
                                                    <div class="text-muted mt-2">Additional information about this capacity</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Audit Information --}}
                                <div class="col-md-12">
                                    <div class="separator separator-dashed my-8"></div>
                                    
                                    <div class="mb-4">
                                        <h4 class="text-dark mb-4">
                                            <i class="ki-duotone ki-shield-tick fs-2 me-2 text-success">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            System Information
                                        </h4>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-4">
                                                    <label class="form-label text-muted">Record ID</label>
                                                    <div class="fw-bold">{{ $row->uuid }}</div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="mb-4">
                                                    <label class="form-label text-muted">Last Updated</label>
                                                    <div class="fw-bold">{{ optional($row->updated_at)->format('d M Y, h:i A') }}</div>
                                                    <div class="text-muted fs-8">({{ optional($row->updated_at)->diffForHumans() }})</div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="mb-4">
                                                    <label class="form-label text-muted">Database ID</label>
                                                    <div class="fw-bold">#{{ $row->id }}</div>
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
        </div>
    </div>
@endsection