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
                    <form method="GET" action="{{ route('users.index') }}">
                        @php
                            $filters = request()->only([
                                'name',
                                'phone',
                                'email',
                                'user_type',
                                'user_status',
                                'gender',
                            ]);
                        @endphp

                        <div class="row g-3">
                            {{-- Name --}}
                            <div class="col-md-2">
                                <input type="text" name="name" class="form-control form-control-solid"
                                    placeholder="Name" value="{{ $filters['name'] ?? '' }}">
                            </div>

                            {{-- Phone --}}
                            <div class="col-md-2">
                                <input type="text" name="phone" class="form-control form-control-solid"
                                    placeholder="Phone" value="{{ $filters['phone'] ?? '' }}">
                            </div>

                            {{-- Email --}}
                            <div class="col-md-2">
                                <input type="email" name="email" class="form-control form-control-solid"
                                    placeholder="Email" value="{{ $filters['email'] ?? '' }}">
                            </div>

                            {{-- User Type --}}
                            <div class="col-md-2">
                                <select name="user_type" class="form-select form-select-solid">
                                    <option value="">User Type</option>
                                    <option value="1" {{ ($filters['user_type'] ?? '') == 1 ? 'selected' : '' }}>User
                                    </option>
                                    <option value="2" {{ ($filters['user_type'] ?? '') == 2 ? 'selected' : '' }}>Agent
                                    </option>
                                    <option value="3" {{ ($filters['user_type'] ?? '') == 3 ? 'selected' : '' }}>Admin
                                    </option>
                                    <option value="4" {{ ($filters['user_type'] ?? '') == 4 ? 'selected' : '' }}>Super
                                        Admin</option>
                                </select>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-2">
                                <select name="user_status" class="form-select form-select-solid">
                                    <option value="">Status</option>
                                    <option value="1" {{ ($filters['user_status'] ?? '') === '1' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="0" {{ ($filters['user_status'] ?? '') === '0' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>
                            </div>

                            {{-- Filter Button --}}
                            <div class="col-md-2 text-end">
                                <button class="btn btn-info w-100">
                                    <i class="ki-duotone ki-filter fs-3 me-2"></i>
                                    Filter
                                </button>
                            </div>

                            {{-- Reset Button --}}
                            <div class="col-md-2">
                                <a href="{{ route('users.index') }}" class="btn btn-warning w-100">
                                    <i class="ki-duotone ki-reload fs-3 me-2"></i>
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Users Table --}}
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title fw-bold fs-3 mb-1">Users List</h3>
                </div>
                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr class="fw-bold text-gray-700 bg-light">
                                    <th>
                                        <div class="d-flex align-items-center" style="padding-left: 5px;">
                                            <i class="ki-duotone ki-number fs-4 me-1 text-primary"></i>
                                        </div>
                                        #
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-user fs-4 me-1 text-primary">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Name
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-sms fs-4 me-1 text-info">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Email
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-tablet fs-4 me-1 text-info">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            Phone
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-briefcase fs-4 me-1 text-warning">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            User Type
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-shield-tick fs-4 me-1 text-success">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Status
                                        </div>
                                    </th>
                                    {{-- <th class="text-end">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <i class="ki-duotone ki-setting fs-4 me-1 text-danger">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Actions
                                    </div>
                                </th> --}}
                                </tr>

                            </thead>
                            <tbody>
                                @php
                                    $userTypeMap = [
                                        1 => 'User',
                                        2 => 'Agent',
                                        3 => 'Admin',
                                        4 => 'Super Admin',
                                    ];
                                    $typeColors = [
                                        1 => 'secondary',
                                        2 => 'info',
                                        3 => 'primary',
                                        4 => 'dark',
                                    ];
                                    $filters = request()->only([
                                        'name',
                                        'phone',
                                        'email',
                                        'user_type',
                                        'user_status',
                                        'gender',
                                    ]);
                                @endphp

                                @forelse ($users as $index => $user)
                                    <tr>
                                        <td>{{ $users->firstItem() + $index }}</td>
                                        <td>{{ $user->name ?? '-' }}</td>
                                        <td>{{ $user->email ?? '-' }}</td>
                                        <td>{{ $user->profile?->phone ?? ($user->phone ?? '-') }}</td>
                                        <td>
                                            <span class="badge badge-light-{{ $typeColors[$user->user_type] ?? 'light' }}">
                                                {{ $userTypeMap[$user->user_type] ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($user->user_status == 1)
                                                <span class="badge badge-light-success">Active</span>
                                            @else
                                                <span class="badge badge-light-danger">Inactive</span>
                                            @endif
                                        </td>
                                        {{-- <td class="text-end">
                                        <a href="{{ route('registered-users.edit', $user->uuid) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Edit">
                                            <i class="fas fa-edit text-danger"></i>
                                        </a>
                                        <a href="{{ route('registered-users.show', $user->uuid) }}" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" title="View">
                                            <i class="fas fa-eye text-info"></i>
                                        </a>
                                    </td> --}}
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-10">
                                            <i class="ki-duotone ki-information fs-2x text-gray-400 mb-2"></i><br>
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $users->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
