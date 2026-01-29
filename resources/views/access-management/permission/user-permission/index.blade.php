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
                            <small>filter users</small>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('user-role.index') }}">
                        @php
                            $filters = request()->only(['name', 'email', 'role']);
                        @endphp
                        <div class="row g-3 align-items-end">

                            {{-- Name --}}
                            <div class="col-md-2">
                                <input type="text" name="name" class="form-control form-control-solid"
                                    placeholder="Name" value="{{ $filters['name'] ?? '' }}">
                            </div>

                            {{-- Email --}}
                            <div class="col-md-2">
                                <input type="email" name="email" class="form-control form-control-solid"
                                    placeholder="Email" value="{{ $filters['email'] ?? '' }}">
                            </div>

                            {{-- Role --}}
                            <div class="col-md-2">
                                <select name="role" class="form-select form-select-solid">
                                    <option value="">All Roles</option>
                                    @foreach (\Spatie\Permission\Models\Role::pluck('name') as $role)
                                        <option value="{{ $role }}"
                                            {{ isset($filters['role']) && $filters['role'] == $role ? 'selected' : '' }}>
                                            {{ $role }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Buttons --}}
                            <div class="col-md-2">
                                <button class="btn btn-info w-100">
                                    <i class="ki-duotone ki-filter fs-3 me-2"></i> Filter
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('user-role.index') }}" class="btn btn-warning w-100">
                                    <i class="ki-duotone ki-reload fs-3 me-2"></i> Reset
                                </a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
            <div class="card card-flush mt-6">
                <div class="card-header bg-light-danger">
                    <div class="card-title">
                        <h2>All Users</h2>
                    </div>
                </div>

                <div class="card-body pt-0">
                    @if ($users->isEmpty())
                        <div class="alert alert-warning">
                            <strong>No users found.</strong>
                        </div>
                    @else
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="users_table">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach ($user->getRoleNames() as $roleName)
                                                <span
                                                    class="badge badge-light-primary fw-bold me-1">{{ $roleName }}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-end">
                                            {{-- @can('user_update') --}}
                                            <a href="{{ route('user-role.edit', $user->id) }}"
                                                class="btn btn-sm btn-light-warning">
                                                <i class="ki-duotone ki-pencil fs-3"></i> Edit
                                            </a>
                                            {{-- @endcan --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $users->links('pagination::bootstrap-5') }}
                        </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    </div>
@endsection
