@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="card"> <!-- Add this card wrapper -->
                <div class="card card-flush shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light-danger">
                    <h3 class="card-title">
                        <i class="ki-duotone ki-pencil fs-2 text-primary me-2">
                            <i class="path1"></i><i class="path2"></i>
                        </i>
                        Edit Role
                    </h3>

                    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-left fs-3 me-2"></i>
                        Back to List
                    </a>
                </div>

                <div class="card-body pt-5"> <!-- pt-5 for some padding on top -->
                    @if ($errors->any())
                        <div class="alert alert-warning">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="role-form" method="POST" action="{{ route('roles.update', $role->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-10">
                            <label class="form-label required">Role Name</label>
                            <input type="text" name="name" class="form-control form-control-solid"
                                value="{{ old('name', $role->name) }}" required>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-danger btn-sm" id="confirm-update-btn">
                                <i class="ki-duotone ki-check fs-2"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div> <!-- end card -->
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        document.getElementById('confirm-update-btn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to update the role name!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('role-form').submit();
                }
            });
        });
    </script>
@endpush
