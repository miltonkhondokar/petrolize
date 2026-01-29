@extends('application.layouts.app')

@section('page-title')
    <h1 class="page-heading">Edit User Role</h1>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <div class="row">
            <div class="col-md-12">

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card card-flush shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light-danger">
                        <h3 class="card-title">
                            <i class="ki-duotone ki-pencil fs-2 text-primary me-2">
                                <i class="path1"></i><i class="path2"></i>
                            </i>
                            Edit User Role
                        </h3>

                        <a href="{{ route('user-role.index') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-arrow-left fs-3 me-2"></i>
                            Back to List
                        </a>
                    </div>

                    <div class="card-body">
                        <form id="user-permission-form"
                              action="{{ route('user-role.update', $user->id) }}"
                              method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Name --}}
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text"
                                       class="form-control"
                                       value="{{ $user->name }}"
                                       disabled>
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email"
                                       class="form-control"
                                       value="{{ $user->email }}"
                                       disabled>
                            </div>

                            {{-- Roles --}}
                            <div class="mb-4">
                                <label class="form-label">Assign Roles</label>
                                <select name="roles[]"
                                        class="form-select"
                                        multiple
                                        required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}"
                                            {{ in_array($role, $userRoles) ? 'selected' : '' }}>
                                            {{ $role }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('roles')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Submit --}}
                            <div class="text-end">
                                <button type="button"
                                        class="btn btn-primary"
                                        onclick="confirmUpdate()">
                                    <i class="ki-duotone ki-check fs-3 me-1"></i>
                                    Update Roles
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
    function confirmUpdate() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You want to update roles for this user!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('user-permission-form').submit();
            }
        });
    }
</script>
@endpush
