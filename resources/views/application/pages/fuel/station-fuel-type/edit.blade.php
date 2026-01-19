@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="row">
                <div class="col-md-12">
                    @if ($errors->any())
                        <div class="alert alert-warning d-flex align-items-center p-5">
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

                    <div class="card card-flush shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light-primary">
                            <h3 class="card-title">
                                <i class="ki-duotone ki-link fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Edit Fuel Types for: {{ $station->name }}
                            </h3>
                            <a href="{{ route('fuel-station-fuel-type.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left fs-3 me-2"></i>
                                Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <form id="assignment_form" method="POST" 
                                  action="{{ route('fuel-station-fuel-type.update', $station->uuid) }}">
                                @csrf
                                @method('PUT')
                                
                                <input type="hidden" name="fuel_station_uuid" value="{{ $station->uuid }}">
                                
                                <div class="row mb-8">
                                    <div class="col-md-12">
                                        <div class="alert alert-info d-flex align-items-center p-5">
                                            <i class="ki-duotone ki-information-2 fs-2hx text-info me-4">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            <div class="d-flex flex-column">
                                                <h4 class="mb-1 text-info">Editing Fuel Types for {{ $station->name }}</h4>
                                                <span>Station ID: {{ $station->uuid }} | Location: {{ $station->location ?? 'Not specified' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fuel Types Selection -->
                                <div class="mb-10">
                                    <label class="form-label required fs-4 mb-4">
                                        <i class="ki-duotone ki-gas fs-2 me-2 text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Available Fuel Types
                                    </label>
                                    <div class="form-text mb-4">Select fuel types available at this station</div>
                                    
                                    <div class="row g-6">
                                        @foreach ($fuelTypes as $fuelType)
                                            @php
                                                $isAssigned = isset($assignedFuelTypes[$fuelType->uuid]);
                                                $isActive = $isAssigned ? $assignedFuelTypes[$fuelType->uuid]->is_active : false;
                                            @endphp
                                            <div class="col-md-4 col-lg-3">
                                                <div class="form-check form-check-custom form-check-solid border rounded p-4 h-100 
                                                    {{ $isAssigned ? 'border-primary border-2' : '' }}">
                                                    <input class="form-check-input" type="checkbox" 
                                                        name="fuel_types[{{ $fuelType->uuid }}][is_active]" 
                                                        id="fuel_type_{{ $fuelType->uuid }}"
                                                        value="1"
                                                        {{ $isActive ? 'checked' : '' }}>
                                                    <label class="form-check-label w-100" for="fuel_type_{{ $fuelType->uuid }}">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="fw-bold text-dark">{{ $fuelType->name }}</span>
                                                            @if($isAssigned)
                                                                <span class="badge badge-light-primary">Assigned</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-muted fs-7">{{ $fuelType->code }}</div>
                                                        @if($fuelType->description)
                                                            <div class="text-muted fs-8 mt-1">{{ Str::limit($fuelType->description, 50) }}</div>
                                                        @endif
                                                        
                                                        @if($isAssigned)
                                                            <div class="mt-2">
                                                                <small class="text-success">
                                                                    <i class="ki-duotone ki-check-circle fs-4 me-1"></i>
                                                                    Currently {{ $isActive ? 'Active' : 'Inactive' }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    @if($fuelTypes->isEmpty())
                                        <div class="alert alert-info">
                                            <i class="ki-duotone ki-information-2 fs-2 me-2">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            No active fuel types found. Please create fuel types first.
                                        </div>
                                    @endif
                                </div>

                                <div class="separator separator-dashed my-8"></div>

                                <div class="text-end mt-8">
                                    <a href="{{ route('fuel-station-fuel-type.index') }}" class="btn btn-light me-3 btn-sm">
                                        <i class="bi bi-x-circle fs-3 me-2"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">
                                        <i class="fa-regular fa-floppy-disk fs-3 me-2"></i>
                                        Update Fuel Types
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Validate checkboxes (at least one selected)
        function validateCheckboxes() {
            const checkboxes = document.querySelectorAll('input[name^="fuel_types"]');
            const atLeastOneChecked = Array.from(checkboxes).some(cb => cb.checked);
            
            if (!atLeastOneChecked) {
                Swal.fire('Validation Error', 'Please select at least one fuel type.', 'warning');
                return false;
            }
            return true;
        }

        // Submit handler with validation + Swal
        document.getElementById('assignment_form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;

            // Validate checkboxes
            if (!validateCheckboxes()) {
                return;
            }

            // Confirm before submit
            const result = await Swal.fire({
                title: 'Confirm Update',
                text: 'Are you sure you want to update fuel types for this station?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, update fuel types',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                form.submit();
            }
        });
    </script>
@endpush