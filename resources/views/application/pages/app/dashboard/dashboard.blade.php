@extends('application.layouts.app')
@section('title', 'Dashboard - Fuel Station Management')

@section('content')
<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <!--begin::Content container-->
    <div id="kt_app_content_container" class="app-container container-fluid">
        
        <!--begin::Row - Summary Stats Cards-->
        <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
            <!--begin::Col-->
            <div class="col-xl-3">
                <!--begin::Card widget 1-->
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #F7FAFC; background-image:url('{{ asset('assets/media/misc/bg-1.png') }}')">
                    <!--begin::Header-->
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Active Pumps</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Total Stations</span>
                        </h3>
                        <div class="card-toolbar">
                            <button class="btn btn-sm btn-icon btn-color-gray-500 btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-dots-horizontal fs-2x"></i>
                            </button>
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column w-100">
                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                <span class="fw-bolder fs-2x text-gray-900">{{ $totalPumps }}</span>
                                <div class="symbol symbol-circle symbol-40px">
                                    <span class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-geolocation fs-2x text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-gray-600 fs-7 fw-semibold">Active</span>
                                    <span class="text-gray-900 fw-bold fs-6">{{ $activePumps }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-gray-600 fs-7 fw-semibold">Inactive</span>
                                    <span class="text-gray-900 fw-bold fs-6">{{ $inactivePumps }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Card widget 1-->
            </div>
            <!--end::Col-->

            <!--begin::Col-->
            <div class="col-xl-3">
                <!--begin::Card widget 2-->
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #E8FFF3; background-image:url('{{ asset('assets/media/misc/bg-2.png') }}')">
                    <!--begin::Header-->
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Fuel Types</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Available Fuels</span>
                        </h3>
                        <div class="card-toolbar">
                            <button class="btn btn-sm btn-icon btn-color-gray-500 btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-dots-horizontal fs-2x"></i>
                            </button>
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column w-100">
                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                <span class="fw-bolder fs-2x text-gray-900">{{ $totalFuelTypes }}</span>
                                <div class="symbol symbol-circle symbol-40px">
                                    <span class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-droplet fs-2x text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-gray-600 fs-7 fw-semibold">Active</span>
                                    <span class="text-gray-900 fw-bold fs-6">{{ $activeFuelTypes }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-gray-600 fs-7 fw-semibold">Inactive</span>
                                    <span class="text-gray-900 fw-bold fs-6">{{ $inactiveFuelTypes }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Card widget 2-->
            </div>
            <!--end::Col-->

            <!--begin::Col-->
            <div class="col-xl-3">
                <!--begin::Card widget 3-->
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #FFF8DD; background-image:url('{{ asset('assets/media/misc/bg-3.png') }}')">
                    <!--begin::Header-->
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Today's Expenses</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ date('d M Y') }}</span>
                        </h3>
                        <div class="card-toolbar">
                            <button class="btn btn-sm btn-icon btn-color-gray-500 btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-dots-horizontal fs-2x"></i>
                            </button>
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column w-100">
                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                <span class="fw-bolder fs-2x text-gray-900">৳{{ number_format($todayExpenses, 0) }}</span>
                                <div class="symbol symbol-circle symbol-40px">
                                    <span class="symbol-label bg-light-warning">
                                        <i class="ki-duotone ki-wallet fs-2x text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-gray-600 fs-7 fw-semibold">Entries</span>
                                    <span class="text-gray-900 fw-bold fs-6">{{ $todayExpenseCount }}</span>
                                </div>
                                <div class="progress h-6px w-100 mt-2">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $todayExpenseCount > 0 ? min(100, ($todayExpenseCount/20)*100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Card widget 3-->
            </div>
            <!--end::Col-->

            <!--begin::Col-->
            <div class="col-xl-3">
                <!--begin::Card widget 4-->
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #FFF5F8; background-image:url('{{ asset('assets/media/misc/bg-4.png') }}')">
                    <!--begin::Header-->
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Complaints</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Issue Status</span>
                        </h3>
                        <div class="card-toolbar">
                            <button class="btn btn-sm btn-icon btn-color-gray-500 btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-dots-horizontal fs-2x"></i>
                            </button>
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column w-100">
                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                <span class="fw-bolder fs-2x text-gray-900">{{ $openComplaints }}</span>
                                <div class="symbol symbol-circle symbol-40px">
                                    <span class="symbol-label bg-light-danger">
                                        <i class="ki-duotone ki-notification-status fs-2x text-danger">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-gray-600 fs-7 fw-semibold">Open</span>
                                    <span class="text-gray-900 fw-bold fs-6">{{ $openComplaints }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-gray-600 fs-7 fw-semibold">Resolved</span>
                                    <span class="text-gray-900 fw-bold fs-6">{{ $resolvedComplaints }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Card widget 4-->
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->

        <!--begin::Row - Charts Section-->
        <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
            <!--begin::Col - Monthly Expenses Chart-->
            <div class="col-xl-8">
                <!--begin::Chart widget 1-->
                <div class="card card-flush h-xl-100">
                    <!--begin::Header-->
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Monthly Expenses Trend</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Last 6 months overview</span>
                        </h3>
                        <!--begin::Toolbar-->
                        <div class="card-toolbar">
                            <div class="btn-group" data-kt-buttons="true">
                                <label class="btn btn-sm btn-color-muted btn-active btn-active-primary px-4">
                                    <input type="radio" name="expense_period" value="month" checked="checked">
                                    Month
                                </label>
                                <label class="btn btn-sm btn-color-muted btn-active btn-active-primary px-4">
                                    <input type="radio" name="expense_period" value="quarter">
                                    Quarter
                                </label>
                                <label class="btn btn-sm btn-color-muted btn-active btn-active-primary px-4">
                                    <input type="radio" name="expense_period" value="year">
                                    Year
                                </label>
                            </div>
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body pt-5 pb-0">
                        <div id="kt_chart_expenses" style="height: 350px;"></div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Chart widget 1-->
            </div>
            <!--end::Col-->

            <!--begin::Col - Stock Distribution-->
            <div class="col-xl-4">
                <!--begin::Chart widget 2-->
                <div class="card card-flush h-xl-100">
                    <!--begin::Header-->
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Stock Distribution</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">By Fuel Type</span>
                        </h3>
                        <!--begin::Toolbar-->
                        <div class="card-toolbar">
                            <button class="btn btn-sm btn-icon btn-color-gray-500 btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-dots-square fs-2x"></i>
                            </button>
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body pt-5 pb-0">
                        <div id="kt_chart_stock_distribution" style="height: 350px;"></div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Chart widget 2-->
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->

        <!--begin::Row - Tables Section-->
        <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
            <!--begin::Col - Recent Activities-->
            <div class="col-xl-6">
                <!--begin::Table widget 1-->
                <div class="card card-flush h-xl-100">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Recent Activities</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Latest stocks & complaints</span>
                        </h3>
                        <!--begin::Toolbar-->
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_activity">
                                <i class="ki-duotone ki-plus fs-2"></i>Add New
                            </button>
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-5">
                        <!--begin::Timeline-->
                        <div class="timeline">
                            @php
                                $activities = collect();
                                foreach($recentStocks as $stock) {
                                    $activities->push([
                                        'type' => 'stock',
                                        'date' => $stock->stock_date,
                                        'time' => $stock->created_at->format('H:i'),
                                        'title' => 'Stock Added',
                                        'description' => $stock->quantity . 'L of ' . $stock->fuelType->name,
                                        'fuelStation' => $stock->fuelStation->name,
                                        'color' => 'primary',
                                        'icon' => 'ki-duotone ki-droplet fs-2x'
                                    ]);
                                }
                                
                                foreach($recentComplaints as $complaint) {
                                    $activities->push([
                                        'type' => 'complaint',
                                        'date' => $complaint->complaint_date,
                                        'time' => $complaint->created_at->format('H:i'),
                                        'title' => 'New Complaint',
                                        'description' => $complaint->title,
                                        'fuelStation' => $complaint->fuelStation->name,
                                        'color' => 'danger',
                                        'icon' => 'ki-duotone ki-notification fs-2x'
                                    ]);
                                }
                                
                                $sortedActivities = $activities->sortByDesc('date')->take(8);
                            @endphp
                            
                            @foreach($sortedActivities as $activity)
                            <!--begin::Timeline item-->
                            <div class="timeline-item">
                                <!--begin::Timeline line-->
                                <div class="timeline-line w-40px"></div>
                                <!--end::Timeline line-->
                                <!--begin::Timeline icon-->
                                <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                                    <div class="symbol-label bg-light-{{ $activity['color'] }}">
                                        <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }}">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <!--end::Timeline icon-->
                                <!--begin::Timeline content-->
                                <div class="timeline-content mb-10 mt-n1">
                                    <!--begin::Timeline heading-->
                                    <div class="pe-3 mb-5">
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-bold mb-2">{{ $activity['title'] }}</div>
                                        <!--end::Title-->
                                        <!--begin::Description-->
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <!--begin::Text-->
                                            <div class="text-gray-600 fw-semibold me-2">{{ $activity['description'] }}</div>
                                            <!--end::Text-->
                                            <!--begin::Separator-->
                                            <span class="bullet"></span>
                                            <!--end::Separator-->
                                            <!--begin::Text-->
                                            <div class="text-muted fw-semibold">{{ $activity['fuelStation'] }}</div>
                                            <!--end::Text-->
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Timeline heading-->
                                    <!--begin::Timeline time-->
                                    <span class="fs-7 text-muted fw-bold">{{ $activity['date']->format('d M') }} at {{ $activity['time'] }}</span>
                                    <!--end::Timeline time-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->
                            @endforeach
                        </div>
                        <!--end::Timeline-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Table widget 1-->
            </div>
            <!--end::Col-->

            <!--begin::Col - Fuel Station Performance-->
            <div class="col-xl-6">
                <!--begin::Table widget 2-->
                <div class="card card-flush h-xl-100">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Fuel Station Performance</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Stock & Complaint Overview</span>
                        </h3>
                        <!--begin::Toolbar-->
                        <div class="card-toolbar">
                            <button class="btn btn-sm btn-icon btn-color-gray-500 btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-filter fs-2x"></i>
                            </button>
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-5">
                        <!--begin::Table container-->
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-row-dashed align-middle gs-0 gy-4">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="ps-0 min-w-175px">Fuel Station</th>
                                        <th class="min-w-100px">Stock Value</th>
                                        <th class="min-w-100px">Complaints</th>
                                        <th class="min-w-125px text-end pe-0">Status</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody>
                                    @foreach($pumpPerformance as $fuelStation)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px me-5">
                                                    <span class="symbol-label bg-light-primary">
                                                        <i class="ki-duotone ki-geolocation fs-2x text-primary">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="#" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6">{{ $fuelStation->name }}</a>
                                                    <span class="text-gray-500 fw-semibold d-block fs-7">{{ $fuelStation->location }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-gray-900 fw-bold fs-6">৳{{ number_format($fuelStation->stock_value, 0) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column w-100 me-2">
                                                <span class="text-gray-900 fw-bold fs-6">{{ $fuelStation->complaint_count }}</span>
                                                <div class="progress h-6px w-100">
                                                    <div class="progress-bar bg-{{ $fuelStation->complaint_count > 5 ? 'danger' : ($fuelStation->complaint_count > 2 ? 'warning' : 'success') }}" role="progressbar" style="width: {{ min(100, ($fuelStation->complaint_count/10)*100) }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge badge-light-{{ $fuelStation->is_active ? 'success' : 'danger' }} py-3 px-4 fs-7">
                                                {{ $fuelStation->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Table container-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Table widget 2-->
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->

        <!--begin::Row - Expenses Breakdown-->
        <div class="row g-5 g-xl-8">
            <!--begin::Col - Expense Categories-->
            <div class="col-xl-12">
                <!--begin::Table widget 3-->
                <div class="card card-flush">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Expense Categories</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Monthly breakdown - {{ date('F Y') }}</span>
                        </h3>
                        <!--begin::Toolbar-->
                        <div class="card-toolbar">
                            <button class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_export_categories">
                                <i class="ki-duotone ki-exit-up fs-2"></i>Export Report
                            </button>
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-5">
                        <!--begin::Table container-->
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                        <th class="min-w-250px">Category</th>
                                        <th class="min-w-100px">Entries</th>
                                        <th class="min-w-150px">Total Amount</th>
                                        <th class="min-w-200px">Trend</th>
                                        <th class="min-w-100px text-end">Share</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody>
                                    @php $totalMonthExpense = $monthlyExpensesByCategory->sum('total'); @endphp
                                    @forelse($monthlyExpensesByCategory as $expense)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <span class="symbol-label bg-light-{{ $loop->index % 5 == 0 ? 'primary' : ($loop->index % 5 == 1 ? 'success' : ($loop->index % 5 == 2 ? 'warning' : ($loop->index % 5 == 3 ? 'danger' : 'info'))) }}">
                                                        <i class="ki-duotone ki-category fs-2x text-{{ $loop->index % 5 == 0 ? 'primary' : ($loop->index % 5 == 1 ? 'success' : ($loop->index % 5 == 2 ? 'warning' : ($loop->index % 5 == 3 ? 'danger' : 'info'))) }}">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="#" class="text-gray-900 fw-bold text-hover-primary mb-1">{{ $expense->category_name ?? 'Uncategorized' }}</a>
                                                    <span class="text-gray-500 fw-semibold d-block fs-7">{{ $expense->description ?? 'No description' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-gray-900 fw-bold fs-6">{{ $expense->count }}</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-900 fw-bold fs-5">৳{{ number_format($expense->total, 2) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div id="kt_table_widget_3_chart_{{ $loop->index }}" class="w-100px h-50px"></div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-gray-900 fw-bold fs-6">{{ $totalMonthExpense > 0 ? number_format(($expense->total / $totalMonthExpense) * 100, 1) : 0 }}%</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-gray-500 py-10">
                                            <i class="ki-duotone ki-wallet fs-3x text-gray-400 mb-5"></i>
                                            <div class="fw-semibold fs-5">No expenses recorded this month</div>
                                        </td>
                                    </tr>
                                    @endforelse
                                    @if($monthlyExpensesByCategory->isNotEmpty())
                                    <tr class="border-top border-gray-200 border-2">
                                        <td>
                                            <span class="text-gray-900 fw-bolder fs-5">TOTAL</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-900 fw-bolder fs-5">{{ $monthlyExpensesByCategory->sum('count') }}</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-900 fw-bolder fs-4">৳{{ number_format($totalMonthExpense, 2) }}</span>
                                        </td>
                                        <td></td>
                                        <td class="text-end">
                                            <span class="text-gray-900 fw-bolder fs-5">100%</span>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Table container-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Table widget 3-->
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->

    </div>
    <!--end::Content container-->
</div>
<!--end::Content-->
@endsection

@push('scripts')
<!-- ApexCharts -->
<script src="{{ asset('assets/plugins/apexcharts/apexcharts.bundle.js') }}"></script>

<script>
    // Initialize monthly expenses chart
    function initExpensesChart() {
        const element = document.getElementById("kt_chart_expenses");
        if (!element) {
            return;
        }

        const options = {
            series: [{
                name: 'Expenses',
                data: @json($monthlyExpensesChart['data'] ?? [])
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            colors: ['#7239EA'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: @json($monthlyExpensesChart['labels'] ?? []),
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: '#A1A5B7',
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return '৳' + val.toLocaleString();
                    },
                    style: {
                        colors: '#A1A5B7',
                        fontSize: '12px'
                    }
                }
            },
            grid: {
                borderColor: '#F1F1F1',
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return '৳' + val.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }
            }
        };

        const chart = new ApexCharts(element, options);
        chart.render();
    }

    // Initialize stock distribution chart
    function initStockDistributionChart() {
        const element = document.getElementById("kt_chart_stock_distribution");
        if (!element) {
            return;
        }

        const fuelTypes = @json($fuelTypes ?? []);
        const stockData = @json($fuelTypeStocks ?? []);

        const colors = ['#50CD89', '#009EF7', '#F1416C', '#FFC700', '#7239EA', '#BEBEC6'];
        const series = stockData.map(item => parseFloat(item.total_quantity));
        const labels = stockData.map(item => item.fuel_name);

        const options = {
            series: series,
            chart: {
                type: 'donut',
                height: 350
            },
            labels: labels,
            colors: colors.slice(0, series.length),
            stroke: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'bottom',
                fontSize: '13px',
                fontFamily: 'inherit',
                fontWeight: 400,
                labels: {
                    colors: '#5E6278'
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                showAlways: true,
                                label: 'Total Stock',
                                fontSize: '18px',
                                fontFamily: 'inherit',
                                fontWeight: 600,
                                color: '#5E6278',
                                formatter: function (w) {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return total.toLocaleString() + ' L';
                                }
                            }
                        }
                    }
                }
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: {
                        height: 300
                    }
                }
            }]
        };

        const chart = new ApexCharts(element, options);
        chart.render();
    }

    // Initialize mini trend charts for expense categories
    function initMiniTrendCharts() {
        const categories = @json($monthlyExpensesByCategory ?? []);
        const colors = ['primary', 'success', 'warning', 'danger', 'info'];
        
        categories.forEach((category, index) => {
            const element = document.getElementById(`kt_table_widget_3_chart_${index}`);
            if (!element) return;
            
            // Generate random trend data for demo
            const data = Array.from({length: 7}, () => Math.floor(Math.random() * 1000) + 500);
            
            const options = {
                series: [{
                    name: 'Trend',
                    data: data
                }],
                chart: {
                    type: 'area',
                    height: 50,
                    sparkline: {
                        enabled: true
                    }
                },
                colors: [`var(--bs-${colors[index % colors.length]})`],
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.1,
                        stops: [0, 90, 100]
                    }
                },
                tooltip: {
                    enabled: false
                }
            };
            
            const chart = new ApexCharts(element, options);
            chart.render();
        });
    }

    // Document ready
    document.addEventListener("DOMContentLoaded", function () {
        initExpensesChart();
        initStockDistributionChart();
        initMiniTrendCharts();
        
        // Period selector functionality
        document.querySelectorAll('input[name="expense_period"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Add loading animation
                const chartElement = document.querySelector('#kt_chart_expenses');
                chartElement.classList.add('chart-loading');
                
                // Simulate API call with timeout
                setTimeout(() => {
                    chartElement.classList.remove('chart-loading');
                    // Here you would normally fetch new data based on selected period
                }, 500);
            });
        });
    });
</script>

<style>
    .card {
        border: 0;
        box-shadow: 0px 0px 20px 0px rgba(76, 87, 125, 0.02);
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0px 0px 30px 0px rgba(76, 87, 125, 0.08);
        transform: translateY(-2px);
    }
    
    .timeline .timeline-item:last-child .timeline-line {
        display: none;
    }
    
    .timeline .timeline-line {
        border-left: 1px dashed #E4E6EF;
    }
    
    .chart-loading {
        opacity: 0.7;
        pointer-events: none;
    }
    
    .progress-bar {
        transition: width 1s ease-in-out;
    }
    
    .symbol-label {
        transition: all 0.3s ease;
    }
    
    .symbol-label:hover {
        transform: scale(1.1);
    }
    
    .badge {
        transition: all 0.3s ease;
    }
    
    .badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush