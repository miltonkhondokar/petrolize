@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Card for Permissions Assignment -->
                    <div class="card card-flush shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light-primary">
                            <h3 class="card-title">
                                <i class="ki-duotone ki-pencil fs-2 text-primary me-2">
                                    <i class="path1"></i><i class="path2"></i>
                                </i>
                                Permissions For Role&nbsp;<strong>{{ $role->name }}</strong>
                            </h3>

                            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left fs-3 me-2"></i>
                                Back to List
                            </a>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            <!-- Permissions Form -->
                            <form id="role-permissions-form" action="{{ route('role-permissions', $role->id) }}"
                                method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="permissions" class="form-label">Permissions</label>

                                    <div class="row">
                                        @foreach ($permissions as $permission)
                                            <div class="col-md-4 mb-3">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="permissions[]"
                                                        id="permission-{{ $permission->id }}"
                                                        value="{{ $permission->name }}"
                                                        {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>

                                                    <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @error('permissions')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Update Button -->
                                <div class="mb-3">
                                    <button type="submit" id="update-permissions-btn" class="btn btn-primary">
                                        <i class="fas fa-sync"></i> Update Permissions
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </section>
    @endsection

    @push('scripts')
        <script>
            document.getElementById('update-permissions-btn').addEventListener('click', function(e) {
                e.preventDefault();

                // Show SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to update permissions for this role.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the form if confirmed
                        document.getElementById('role-permissions-form').submit();
                    }
                });
            });
        </script>
    @endpush
