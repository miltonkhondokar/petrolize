@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

{{-- Filter Section --}}
<div class="card card-custom gutter-b mb-5 mb-xl-8 shadow-sm">
    <div class="card-header bg-light-danger">
        <div class="card-title">
            <h3 class="card-label">
                <i class="fas fa-filter"></i> Filter
                <small>filter roles</small>
            </h3>
        </div>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('roles.index') }}">
            @php
                $filters = request()->only(['name']);
            @endphp

            <div class="row g-3 align-items-end">
                {{-- Role Name --}}
                <div class="col-md-4">
                    <input
                        type="text"
                        name="name"
                        class="form-control form-control-solid"
                        placeholder="Role Name"
                        value="{{ $filters['name'] ?? '' }}"
                    >
                </div>

                {{-- Filter Button --}}
                <div class="col-md-2">
                    <button class="btn btn-info w-100">
                        <i class="ki-duotone ki-filter fs-3 me-2"></i>
                        Filter
                    </button>
                </div>

                {{-- Reset Button --}}
                <div class="col-md-2">
                    <a href="{{ route('roles.index') }}" class="btn btn-warning w-100">
                        <i class="ki-duotone ki-reload fs-3 me-2"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>


            {{-- Roles Table --}}
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title fw-bold fs-3 mb-1">Roles List</h3>
                </div>
                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr class="fw-bold text-gray-700 bg-light">
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-hashtag text-primary me-2"></i>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-tag text-success me-2"></i>
                                            Role Name
                                        </div>
                                    </th>
                                    <th class="text-end">
                                        <div class="d-flex justify-content-end align-items-center">
                                            <i class="fas fa-cogs text-danger me-2"></i>
                                            Actions
                                        </div>
                                    </th>
                                </tr>

                            </thead>
                            <tbody>
                                @if ($roles->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center">No roles found</td>
                                    </tr>
                                @else
                                    @foreach ($roles as $index => $role)
                                        <tr>
                                            <td>{{ $roles->firstItem() + $index }}</td>
                                            <td>{{ $role->name }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('permissions-list', $role->id) }}"
                                                    class="btn btn-warning btn-sm mx-1">
                                                    Add / Edit Permissions
                                                </a>
                                                <a href="{{ route('roles.edit', $role->id) }}"
                                                    class="btn btn-primary btn-sm mx-1">
                                                    Edit
                                                </a>
                                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                    class="d-inline delete-role-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-danger btn-sm mx-1 delete-role-btn">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $roles->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-role-form');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // prevent the form from submitting immediately

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // submit the form if confirmed
                        }
                    });
                });
            });
        });
    </script>
@endpush
