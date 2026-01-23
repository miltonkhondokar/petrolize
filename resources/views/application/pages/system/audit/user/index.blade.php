@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

    <div class="card mb-5 mb-xl-8">
        <div class="card-header border-0 pt-5 d-flex justify-content-between align-items-center">
            <h3 class="card-title fw-bold fs-3 mb-1">
                <i class="fa-solid fa-shield-halved me-2"></i> Audit Logs
            </h3>
        </div>

        <div class="card-body py-3">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted text-uppercase fs-7">
                            <th>#</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($auditLogs as $index => $log)
                            <tr>
                                <td>{{ $auditLogs->firstItem() + $index }}</td>
                                <td>
                                    <span class="fw-semibold text-primary">
                                        {{ $log->user->name ?? 'System' }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $log->user->email ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-light-info text-uppercase fw-bold px-3 py-2">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td><span class="text-muted">{{ $log->ip_address ?? '-' }}</span></td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 150px;" title="{{ $log->user_agent }}">
                                        {{ $log->user_agent }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted" title="{{ $log->created_at }}">
                                        {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-10 fs-6">No audit logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        {{ $auditLogs->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>
@endsection
