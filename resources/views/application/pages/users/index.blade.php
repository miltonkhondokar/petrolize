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
                    <form method="GET" action="{{ route('user-master-data') }}">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <input type="text" name="name" class="form-control form-control-solid"
                                    placeholder="Name" value="{{ $filters['name'] ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <input type="email" name="email" class="form-control form-control-solid"
                                    placeholder="Email" value="{{ $filters['email'] ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="phone" class="form-control form-control-solid"
                                    placeholder="Phone" value="{{ $filters['phone'] ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <select name="gender" class="form-select form-select-solid">
                                    <option value="">Gender</option>
                                    <option value="1"
                                        {{ isset($filters['gender']) && $filters['gender'] == 1 ? 'selected' : '' }}>Male
                                    </option>
                                    <option value="2"
                                        {{ isset($filters['gender']) && $filters['gender'] == 2 ? 'selected' : '' }}>
                                        Female</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="user_status" class="form-select form-select-solid">
                                    <option value="">Status</option>
                                    <option value="1"
                                        {{ isset($filters['user_status']) && $filters['user_status'] == 1 ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0"
                                        {{ isset($filters['user_status']) && $filters['user_status'] == '0' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info">Filter</button>
                                <a href="{{ route('user-master-data') }}" class="btn btn-warning ms-2">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light-danger">
                    <h3 class="card-title fw-bold fs-3 mb-1">Users List</h3>
                    <a href="{{ route('user-master-data-create') }}" class="btn btn-sm btn-primary">
                        <i class="ki-outline ki-plus-circle fs-3 me-1"></i> Add New User
                    </a>
                </div>
                <div class="card-body py-3">
                    <div class="table-responsive overflow-visible">
                        <table class="table align-middle table-row-dashed table-bordered fs-6 gy-4">
                            <thead class="bg-light-dark">
                                <tr class="fw-bold text-gray-700">
                                    <th>
                                        <i class="ki-duotone ki-text-number fs-2 me-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                            <span class="path6"></span>
                                        </i>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-user fs-2 me-2 text-primary">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Name
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-sms fs-2 me-2 text-info">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Email
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-tablet fs-2 me-2 text-info">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            Phone
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-user-square fs-2 me-2 text-danger">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Gender
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-shield-tick fs-2 me-2 text-success">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Status
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-shield-search fs-2 me-2 text-warning">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Email Verified
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-calendar fs-2 me-2 text-primary">
                                                <i class="path1"></i><i class="path2"></i>
                                            </i>
                                            Created At
                                        </div>
                                    </th>
                                    <th class="text-end">
                                        <div class="d-flex justify-content-end align-items-center">
                                            <i class="ki-duotone ki-setting-2 fs-2 me-2 text-danger">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Actions
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $index => $user)
                                    <tr>
                                        <td>{{ $users->firstItem() + $index }}</td>
                                        <td class="text-dark fw-semibold">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    {{ $user->name ?? '-' }}
                                                    @if ($user->roles->count() > 0)
                                                        <div class="text-muted fs-7">
                                                            @foreach ($user->roles as $role)
                                                                <span
                                                                    class="badge badge-light-info">{{ $role->name }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email ?? '-' }}</td>
                                        <td>{{ $user->phone ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-light-{{ $user->gender_color }}">
                                                <i class="ki-duotone {{ $user->gender_icon }} fs-5 me-1"></i>
                                                {{ $user->gender_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-light-{{ $user->user_status == 1 ? 'success' : 'danger' }}">
                                                <i
                                                    class="ki-duotone {{ $user->user_status == 1 ? 'ki-check-circle' : 'ki-cross' }} fs-5 me-1"></i>
                                                {{ $user->user_status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-light-{{ $user->email_verification_status == 1 ? 'success' : 'warning' }}">
                                                <i
                                                    class="ki-duotone {{ $user->email_verification_status == 1 ? 'ki-check' : 'ki-shield-cross' }} fs-5 me-1"></i>
                                                {{ $user->email_verification_status == 1 ? 'Verified' : 'Unverified' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted"
                                                title="{{ $user->created_at->format('d M Y, h:i A') }}">
                                                {{ $user->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <a href="#" class="btn btn-sm btn-light-info dropdown-toggle"
                                                    data-bs-toggle="dropdown">
                                                    <i class="ki-outline ki-setting-4 fs-2 me-2"></i> Action
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        {{-- view link --}}
                                                        <a href="#" class="dropdown-item">
                                                            <i class="ki-duotone ki-eye fs-2 me-2 text-info">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                            View
                                                        </a>
                                                    </li>
                                                    <li>

                                                        {{-- edit link --}}
                                                        <a class="dropdown-item" href="#">
                                                            <i class="ki-duotone ki-pencil fs-2 me-2 text-warning">
                                                                <i class="path1"></i><i class="path2"></i>
                                                            </i>
                                                            Edit
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-10">
                                            <i class="ki-duotone ki-information-2 fs-2x text-gray-400 mb-2"></i><br>
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $users->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(url) {
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
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    const csrfField = document.createElement('input');
                    csrfField.type = 'hidden';
                    csrfField.name = '_token';
                    csrfField.value = csrfToken;

                    form.appendChild(methodField);
                    form.appendChild(csrfField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function changeStatus(url, status) {
            const action = status === 'active' ? 'activate' : 'deactivate';
            Swal.fire({
                title: `Are you sure you want to ${action} this user?`,
                text: "You can change this later.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Yes, ${action} it!`
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PATCH';

                    const csrfField = document.createElement('input');
                    csrfField.type = 'hidden';
                    csrfField.name = '_token';
                    csrfField.value = csrfToken;

                    const statusField = document.createElement('input');
                    statusField.type = 'hidden';
                    statusField.name = 'status';
                    statusField.value = status;

                    form.appendChild(methodField);
                    form.appendChild(csrfField);
                    form.appendChild(statusField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush
