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
                <small>filter permissions</small>
            </h3>
        </div>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('permissions.index') }}">
            @php
                $filters = request()->only('name');
            @endphp

            <div class="row g-3 align-items-end">
                {{-- Permission Name --}}
                <div class="col-md-4">
                    <input
                        type="text"
                        name="name"
                        class="form-control form-control-solid"
                        placeholder="Permission Name"
                        value="{{ $filters['name'] ?? '' }}"
                        autocomplete="off"
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
                    <a href="{{ route('permissions.index') }}" class="btn btn-warning w-100">
                        <i class="ki-duotone ki-reload fs-3 me-2"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>


            {{-- Permissions Table --}}
            <div class="card mb-5 mb-xl-8">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold fs-3 mb-1">Permissions List</h3>
                    <a href="{{ route('permissions.create') }}" class="btn btn-sm btn-info">
                        <i class="fas fa-plus"></i> Add New Permission
                    </a>
                </div>

                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr class="fw-bold text-gray-700 bg-light">
                                    <th>
                                        <div class="d-flex align-items-center" style="padding-left: 5px;">
                                            <i class="ki-duotone ki-number fs-4 me-1 text-primary"></i> #
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-shield-search fs-4 me-1 text-info">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Permission Name
                                        </div>
                                    </th>
                                    <th class="text-end">
                                        <div class="d-flex justify-content-end align-items-center">
                                            <i class="ki-duotone ki-setting fs-4 me-1 text-danger">
                                                <span class="path1"></span><span class="path2"></span>
                                            </i>
                                            Actions
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($permissions as $index => $permission)
                                    <tr>
                                        <td>{{ $permissions->firstItem() + $index }}</td>
                                        <td>
                                            <span class="badge badge-light-primary fs-6">
                                                <i class="ki-duotone ki-lock fs-5 me-1 text-primary">
                                                    <i class="path1"></i><i class="path2"></i>
                                                </i>
                                                {{ $permission->name }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('permissions.edit', $permission->id) }}"
                                                class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                                data-bs-toggle="tooltip" title="Edit Permission">
                                                <i class="ki-duotone ki-pencil fs-2"><i class="path1"></i><i
                                                        class="path2"></i></i>
                                            </a>

                                            <button
                                                class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete-btn"
                                                data-id="{{ $permission->id }}" data-name="{{ $permission->name }}"
                                                data-bs-toggle="tooltip" title="Delete Permission">
                                                <i class="ki-duotone ki-trash fs-2"><i class="path1"></i><i
                                                        class="path2"></i></i>
                                            </button>

                                            {{-- Hidden Delete Form --}}
                                            <form id="delete-form-{{ $permission->id }}"
                                                action="{{ route('permissions.destroy', $permission->id) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-10">
                                            <i class="ki-duotone ki-information fs-2x text-gray-400 mb-2"></i><br>
                                            No permissions found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $permissions->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Bootstrap tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(el => new bootstrap.Tooltip(el))

            // SweetAlert Delete
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    Swal.fire({
                        title: `Delete "${name}"?`,
                        text: "This action cannot be undone.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('delete-form-' + id).submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
