@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection


@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            <div class="card card-flush shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light-danger">
                    <h3 class="card-title">
                        <i class="ki-duotone ki-pencil fs-2 text-primary me-2">
                            <i class="path1"></i><i class="path2"></i>
                        </i>
                        Edit Permission
                    </h3>

                    <a href="{{ route('permissions.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-left fs-3 me-2"></i>
                        Back to List
                    </a>
                </div>

                <div class="card-body pt-0">
                    @if ($errors->any())
                        <div class="alert alert-warning">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="permission-form" action="{{ route('permissions.update', $permission->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-10">
                            <label class="form-label required">Permission Name</label>
                            <input type="text" name="name" class="form-control form-control-solid"
                                value="{{ old('name', $permission->name) }}" required>
                            @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-danger btn-sm" id="confirm-update-btn">
                                <i class="ki-duotone ki-check fs-2"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('confirm-update-btn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to update this permission!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('permission-form').submit();
                }
            });
        });
    </script>
@endpush
