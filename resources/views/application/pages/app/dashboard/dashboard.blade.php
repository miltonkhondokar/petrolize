@extends('application.layouts.app')
@section('title', 'Dashboard - Fuel Station Management')

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            {{-- =========================
            HERO HEADER (nice + informative)
        ========================= --}}
            <div class="card card-flush mb-5 mb-xl-8">
                <div class="card-body py-6">
                    <div
                        class="d-flex flex-column flex-xl-row align-items-start align-items-xl-center justify-content-between gap-4">
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="symbol symbol-45px">
                                    <span class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-chart-line fs-2x text-primary">
                                            <span class="path1"></span><span class="path2"></span>
                                        </i>
                                    </span>
                                </span>
                                <div>
                                    <div class="fs-2 fw-bold text-gray-900">Fuel Station Dashboard</div>
                                    <div class="text-gray-600 fw-semibold">
                                        Live overview • {{ now()->format('d M Y, h:i A') }}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-3">
                                <span class="badge badge-light-primary py-2 px-3">
                                    Total Stock Value: <b
                                        class="ms-1">৳{{ number_format((float) $totalStockValue, 0) }}</b>
                                </span>
                                <span class="badge badge-light-success py-2 px-3">
                                    MTD Sales: <b class="ms-1">৳{{ number_format((float) $mtdSalesAmount, 0) }}</b>
                                </span>
                                <span class="badge badge-light-info py-2 px-3">
                                    MTD Liters: <b class="ms-1">{{ number_format((float) $mtdSoldLiters, 0) }} L</b>
                                </span>

                                @php $profit = (float)($profitSnapshot['profit'] ?? 0); @endphp
                                <span class="badge badge-light-{{ $profit >= 0 ? 'success' : 'danger' }} py-2 px-3">
                                    MTD Profit: <b class="ms-1">৳{{ number_format($profit, 0) }}</b>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('fuel_sales_days.index') }}" class="btn btn-sm btn-light-primary">
                                <i class="ki-duotone ki-book fs-5 me-1"></i> Sales Days
                            </a>
                            <a href="{{ route('fuel_sales_days.create') }}" class="btn btn-sm btn-primary">
                                <i class="ki-duotone ki-plus fs-5 me-1"></i> New Sales Day
                            </a>
                            <a href="{{ route('fuel-station.index') ?? '#' }}" class="btn btn-sm btn-light">
                                <i class="ki-duotone ki-geolocation fs-5 me-1"></i> Stations
                            </a>
                        </div>
                    </div>
                </div>
            </div>


            {{-- =========================
            ROW 1: KPI CARDS (now more business-focused)
        ========================= --}}
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">

                {{-- Pumps --}}
                <div class="col-xl-3">
                    <div class="card card-flush h-xl-100 hover-elevate-up">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-gray-500 fw-semibold mb-1">Fuel Stations</div>
                                    <div class="fs-2hx fw-bold text-gray-900">{{ $totalPumps }}</div>
                                </div>
                                <span class="symbol symbol-50px">
                                    <span class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-geolocation fs-2x text-primary">
                                            <span class="path1"></span><span class="path2"></span>
                                        </i>
                                    </span>
                                </span>
                            </div>

                            <div class="separator my-4"></div>

                            <div class="d-flex justify-content-between">
                                <span class="text-gray-600 fw-semibold">Active</span>
                                <span class="fw-bold text-gray-900">{{ $activePumps }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-gray-600 fw-semibold">Inactive</span>
                                <span class="fw-bold text-gray-900">{{ $inactivePumps }}</span>
                            </div>

                            <div class="progress h-6px mt-3">
                                @php
                                    $pct = $totalPumps > 0 ? ($activePumps / $totalPumps) * 100 : 0;
                                @endphp
                                <div class="progress-bar bg-primary" style="width: {{ min(100, $pct) }}%"></div>
                            </div>
                            <div class="text-muted fs-8 mt-2">Active ratio: {{ number_format($pct, 1) }}%</div>
                        </div>
                    </div>
                </div>

                {{-- Fuel Types --}}
                <div class="col-xl-3">
                    <div class="card card-flush h-xl-100 hover-elevate-up">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-gray-500 fw-semibold mb-1">Fuel Types</div>
                                    <div class="fs-2hx fw-bold text-gray-900">{{ $totalFuelTypes }}</div>
                                </div>
                                <span class="symbol symbol-50px">
                                    <span class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-droplet fs-2x text-success">
                                            <span class="path1"></span><span class="path2"></span>
                                        </i>
                                    </span>
                                </span>
                            </div>

                            <div class="separator my-4"></div>

                            <div class="d-flex justify-content-between">
                                <span class="text-gray-600 fw-semibold">Active</span>
                                <span class="fw-bold text-gray-900">{{ $activeFuelTypes }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-gray-600 fw-semibold">Inactive</span>
                                <span class="fw-bold text-gray-900">{{ $inactiveFuelTypes }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Today Sales --}}
                <div class="col-xl-3">
                    <div class="card card-flush h-xl-100 hover-elevate-up">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-gray-500 fw-semibold mb-1">Today Sales</div>
                                    <div class="fs-2hx fw-bold text-gray-900">
                                        ৳{{ number_format((float) $todaySalesAmount, 0) }}
                                    </div>
                                </div>
                                <span class="symbol symbol-50px">
                                    <span class="symbol-label bg-light-info">
                                        <i class="ki-duotone ki-dollar fs-2x text-info">
                                            <span class="path1"></span><span class="path2"></span>
                                        </i>
                                    </span>
                                </span>
                            </div>

                            <div class="separator my-4"></div>

                            <div class="d-flex justify-content-between">
                                <span class="text-gray-600 fw-semibold">Sold</span>
                                <span class="fw-bold text-gray-900">{{ number_format((float) $todaySoldLiters, 0) }}
                                    L</span>
                            </div>
                            <div class="text-muted fs-8 mt-2">
                                Based on submitted/approved sales days
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Vendor Due --}}
                <div class="col-xl-3">
                    <div class="card card-flush h-xl-100 hover-elevate-up">
                        <div class="card-body">
                            @php
                                $due = (float) ($vendorPayables['due'] ?? 0);
                                $dueBadge = $due > 0 ? 'danger' : 'success';
                            @endphp
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-gray-500 fw-semibold mb-1">Vendor Due</div>
                                    <div class="fs-2hx fw-bold text-gray-900">
                                        ৳{{ number_format($due, 0) }}
                                    </div>
                                </div>
                                <span class="symbol symbol-50px">
                                    <span class="symbol-label bg-light-{{ $dueBadge }}">
                                        <i class="ki-duotone ki-wallet fs-2x text-{{ $dueBadge }}">
                                            <span class="path1"></span><span class="path2"></span>
                                        </i>
                                    </span>
                                </span>
                            </div>

                            <div class="separator my-4"></div>

                            <div class="d-flex justify-content-between">
                                <span class="text-gray-600 fw-semibold">Total Purchases</span>
                                <span
                                    class="fw-bold text-gray-900">৳{{ number_format((float) ($vendorPayables['total_purchases'] ?? 0), 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-gray-600 fw-semibold">Paid</span>
                                <span
                                    class="fw-bold text-gray-900">৳{{ number_format((float) ($vendorPayables['paid'] ?? 0), 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            {{-- =========================
            ROW 2: BIG CHARTS (Sales + Stock movement)
        ========================= --}}
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                <div class="col-xl-8">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Sales Trend</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Last 14 days (Amount + Liters)</span>
                            </div>
                            <div class="card-toolbar">
                                <span class="badge badge-light-success">MTD
                                    ৳{{ number_format((float) $mtdSalesAmount, 0) }}</span>
                                <span class="badge badge-light-info ms-2">MTD
                                    {{ number_format((float) $mtdSoldLiters, 0) }} L</span>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <div id="kt_chart_sales_trend" style="height: 360px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Stock Movement</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Last 14 days (IN vs OUT)</span>
                            </div>
                            <div class="card-toolbar">
                                <span class="badge badge-light-primary">Ledger Based</span>
                            </div>
                        </div>
                        <div class="card-body pt-5 pb-0">
                            <div id="kt_chart_stock_move" style="height: 360px;"></div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- =========================
            ROW 3: Existing charts (Expenses + Stock distribution)
        ========================= --}}
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                <div class="col-xl-8">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Monthly Expenses Trend</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Last 6 months overview</span>
                            </div>
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
                        </div>
                        <div class="card-body pt-5 pb-0">
                            <div id="kt_chart_expenses" style="height: 350px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Stock Distribution</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">By Fuel Type</span>
                            </div>
                        </div>
                        <div class="card-body pt-5 pb-0">
                            <div id="kt_chart_stock_distribution" style="height: 350px;"></div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- =========================
            ROW 4: Top lists + Low stock alerts
        ========================= --}}
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">

                {{-- Top fuels --}}
                <div class="col-xl-4">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Top Fuels</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">This month (liters + revenue)</span>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            @forelse($topFuelsMtd as $row)
                                @php
                                    $liters = (float) $row->liters;
                                    $rev = (float) $row->revenue;
                                @endphp
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="symbol symbol-40px">
                                            <span class="symbol-label bg-light-success">
                                                <i class="ki-duotone ki-droplet fs-2 text-success">
                                                    <span class="path1"></span><span class="path2"></span>
                                                </i>
                                            </span>
                                        </span>
                                        <div>
                                            <div class="fw-bold text-gray-900">{{ $row->fuel_name }}</div>
                                            <div class="text-muted fs-8">{{ $row->fuel_code ?? '' }}</div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-gray-900">{{ number_format($liters, 0) }} L</div>
                                        <div class="text-muted fs-8">৳{{ number_format($rev, 0) }}</div>
                                    </div>
                                </div>
                                <div class="separator separator-dashed mb-4"></div>
                            @empty
                                <div class="text-center text-muted py-10">
                                    <i class="ki-duotone ki-information fs-3x text-gray-400 mb-3"></i>
                                    <div class="fw-semibold">No sales found for this month</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Top stations --}}
                <div class="col-xl-4">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Top Stations</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">This month (revenue)</span>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            @forelse($topStationsMtd as $row)
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="symbol symbol-40px">
                                            <span class="symbol-label bg-light-primary">
                                                <i class="ki-duotone ki-geolocation fs-2 text-primary">
                                                    <span class="path1"></span><span class="path2"></span>
                                                </i>
                                            </span>
                                        </span>
                                        <div class="fw-bold text-gray-900">{{ $row->station_name }}</div>
                                    </div>
                                    <div class="fw-bold text-gray-900">৳{{ number_format((float) $row->revenue, 0) }}
                                    </div>
                                </div>
                                <div class="separator separator-dashed mb-4"></div>
                            @empty
                                <div class="text-center text-muted py-10">
                                    <i class="ki-duotone ki-information fs-3x text-gray-400 mb-3"></i>
                                    <div class="fw-semibold">No sales found for this month</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Low stock alerts --}}
                <div class="col-xl-4">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Low Stock Alerts</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Latest ledger balance</span>
                            </div>
                            <div class="card-toolbar">
                                <span class="badge badge-light-danger">Attention</span>
                            </div>
                        </div>

                        <div class="card-body pt-5">
                            @forelse($lowStockAlerts as $a)
                                @php
                                    $bal = (float) $a->balance_after;
                                    $badge = $bal <= 1 ? 'danger' : ($bal <= 3 ? 'warning' : 'info');
                                @endphp
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div>
                                        <div class="fw-bold text-gray-900">{{ $a->station_name }}</div>
                                        <div class="text-muted fs-8">{{ $a->fuel_name }} ({{ $a->fuel_code ?? '' }})
                                        </div>
                                    </div>
                                    <span class="badge badge-light-{{ $badge }} fw-bold">
                                        {{ number_format($bal, 2) }} L
                                    </span>
                                </div>
                                <div class="separator separator-dashed mb-4"></div>
                            @empty
                                <div class="text-center text-muted py-10">
                                    <i class="ki-duotone ki-shield-tick fs-3x text-success mb-3"></i>
                                    <div class="fw-semibold">No low stock alerts</div>
                                    <div class="text-muted fs-8">Everything looks healthy ✅</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>


            {{-- =========================
            ROW 5: Recent Activities + Performance (keep yours, polished)
        ========================= --}}
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                <div class="col-xl-6">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Recent Activities</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Latest stocks & complaints</span>
                            </div>
                        </div>

                        <div class="card-body pt-5">
                            <div class="timeline">
                                @php
                                    $activities = collect();

                                    // recentStocks = FuelPurchase (from controller)
                                    foreach ($recentStocks as $purchase) {
                                        $activities->push([
                                            'type' => 'purchase',
                                            'date' => $purchase->purchase_date ?? $purchase->created_at,
                                            'time' => optional($purchase->created_at)->format('H:i'),
                                            'title' => 'Stock Received',
                                            'description' => 'Vendor: ' . ($purchase->vendor->name ?? '-'),
                                            'fuelStation' => $purchase->fuelStation->name ?? '-',
                                            'color' => 'primary',
                                            'icon' => 'ki-duotone ki-delivery-3 fs-2x',
                                        ]);
                                    }

                                    foreach ($recentComplaints as $complaint) {
                                        $activities->push([
                                            'type' => 'complaint',
                                            'date' => $complaint->complaint_date ?? $complaint->created_at,
                                            'time' => optional($complaint->created_at)->format('H:i'),
                                            'title' => 'New Complaint',
                                            'description' => $complaint->title,
                                            'fuelStation' => $complaint->fuelStation->name ?? '-',
                                            'color' => 'danger',
                                            'icon' => 'ki-duotone ki-notification fs-2x',
                                        ]);
                                    }

                                    $sortedActivities = $activities
                                        ->sortByDesc(fn($x) => \Carbon\Carbon::parse($x['date']))
                                        ->take(8);
                                @endphp


                                @foreach ($sortedActivities as $activity)
                                    <div class="timeline-item">
                                        <div class="timeline-line w-40px"></div>
                                        <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                                            <div class="symbol-label bg-light-{{ $activity['color'] }}">
                                                <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }}">
                                                    <span class="path1"></span><span class="path2"></span>
                                                </i>
                                            </div>
                                        </div>
                                        <div class="timeline-content mb-10 mt-n1">
                                            <div class="pe-3 mb-3">
                                                <div class="fs-6 fw-bold mb-1">{{ $activity['title'] }}</div>
                                                <div class="d-flex align-items-center mt-1 fs-7">
                                                    <div class="text-gray-600 fw-semibold me-2">
                                                        {{ $activity['description'] }}</div>
                                                    <span class="bullet"></span>
                                                    <div class="text-muted fw-semibold ms-2">
                                                        {{ $activity['fuelStation'] }}</div>
                                                </div>
                                            </div>
                                            <span class="fs-7 text-muted fw-bold">
                                                {{ $activity['date']->format('d M') }} at {{ $activity['time'] }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($sortedActivities->isEmpty())
                                <div class="text-center text-muted py-10">
                                    <div class="fw-semibold">No recent activities</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Fuel Station Performance</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Top 5 by stock value</span>
                            </div>
                        </div>

                        <div class="card-body pt-5">
                            <div class="table-responsive">
                                <table class="table table-row-dashed align-middle gs-0 gy-4">
                                    <thead>
                                        <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                            <th class="ps-0 min-w-220px">Station</th>
                                            <th class="min-w-140px">Stock Value</th>
                                            <th class="min-w-120px">Complaints</th>
                                            <th class="min-w-120px text-end pe-0">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pumpPerformance as $fuelStation)
                                            <tr>
                                                <td class="ps-0">
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-45px me-4">
                                                            <span class="symbol-label bg-light-primary">
                                                                <i class="ki-duotone ki-geolocation fs-2 text-primary">
                                                                    <span class="path1"></span><span
                                                                        class="path2"></span>
                                                                </i>
                                                            </span>
                                                        </div>
                                                        <div class="d-flex justify-content-start flex-column">
                                                            <span
                                                                class="text-gray-900 fw-bold mb-1 fs-6">{{ $fuelStation->name }}</span>
                                                            <span
                                                                class="text-gray-500 fw-semibold d-block fs-7">{{ $fuelStation->location }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="text-gray-900 fw-bold fs-6">৳{{ number_format((float) $fuelStation->stock_value, 0) }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column w-100 me-2">
                                                        <span
                                                            class="text-gray-900 fw-bold fs-6">{{ (int) $fuelStation->complaint_count }}</span>
                                                        <div class="progress h-6px w-100">
                                                            <div class="progress-bar bg-{{ $fuelStation->complaint_count > 5 ? 'danger' : ($fuelStation->complaint_count > 2 ? 'warning' : 'success') }}"
                                                                style="width: {{ min(100, ((int) $fuelStation->complaint_count / 10) * 100) }}%">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end pe-0">
                                                    <span
                                                        class="badge badge-light-{{ $fuelStation->is_active ? 'success' : 'danger' }} py-3 px-4 fs-7">
                                                        {{ $fuelStation->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @if ($pumpPerformance->isEmpty())
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-10">
                                                    No station performance data found.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="separator my-5"></div>

                            {{-- Profit snapshot small panel --}}
                            <div class="d-flex flex-wrap gap-3 justify-content-between">
                                <div class="d-flex flex-column">
                                    <span class="text-muted fw-semibold fs-8">MTD Sales</span>
                                    <span
                                        class="fw-bold text-gray-900">৳{{ number_format((float) ($profitSnapshot['sales'] ?? 0), 0) }}</span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-muted fw-semibold fs-8">MTD Purchases</span>
                                    <span
                                        class="fw-bold text-gray-900">৳{{ number_format((float) ($profitSnapshot['purchases'] ?? 0), 0) }}</span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-muted fw-semibold fs-8">MTD Expenses</span>
                                    <span
                                        class="fw-bold text-gray-900">৳{{ number_format((float) ($profitSnapshot['expenses'] ?? 0), 0) }}</span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-muted fw-semibold fs-8">MTD Profit</span>
                                    @php $p = (float)($profitSnapshot['profit'] ?? 0); @endphp
                                    <span
                                        class="fw-bold text-{{ $p >= 0 ? 'success' : 'danger' }}">৳{{ number_format($p, 0) }}</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================
    ROW X: Capacity & Storage Intelligence (NEW)
========================= --}}
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">

                {{-- Capacity Utilization --}}
                <div class="col-xl-4">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Tank Capacity Utilization</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Top stations by usage %</span>
                            </div>
                            <div class="card-toolbar">
                                <span class="badge badge-light-info">Live</span>
                            </div>
                        </div>

                        <div class="card-body pt-5">
                            @forelse($capacityUtilizationTop as $row)
                                @php
                                    $pct = (float) $row->utilization_percent;
                                    $color = $pct >= 90 ? 'danger' : ($pct >= 70 ? 'warning' : 'success');
                                @endphp

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-1">
                                        <div>
                                            <div class="fw-bold text-gray-900">{{ $row->station_name }}</div>
                                            <div class="text-muted fs-8">
                                                {{ $row->fuel_name }} ({{ $row->fuel_code }})
                                            </div>
                                        </div>
                                        <span class="fw-bold text-{{ $color }}">
                                            {{ number_format($pct, 1) }}%
                                        </span>
                                    </div>

                                    <div class="progress h-6px">
                                        <div class="progress-bar bg-{{ $color }}"
                                            style="width: {{ min(100, $pct) }}%"></div>
                                    </div>

                                    <div class="text-muted fs-8 mt-1">
                                        {{ number_format($row->current_stock, 0) }} L /
                                        {{ number_format($row->capacity_liters, 0) }} L
                                    </div>
                                </div>

                                <div class="separator separator-dashed"></div>
                            @empty
                                <div class="text-center text-muted py-10">
                                    No capacity utilization data available
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Missing Capacity Configurations --}}
                <div class="col-xl-4">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Missing Capacity Config</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">
                                    Stations without tank setup
                                </span>
                            </div>
                            <div class="card-toolbar">
                                <span class="badge badge-light-warning">Action Needed</span>
                            </div>
                        </div>

                        <div class="card-body pt-5">
                            @forelse($missingCapacityConfigs as $row)
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <div class="fw-bold text-gray-900">{{ $row->station_name }}</div>
                                        <div class="text-muted fs-8">
                                            {{ $row->fuel_name }} ({{ $row->fuel_code }})
                                        </div>
                                    </div>

                                    <span class="badge badge-light-warning">
                                        {{ number_format((float) $row->current_stock, 0) }} L
                                    </span>
                                </div>
                                <div class="separator separator-dashed"></div>
                            @empty
                                <div class="text-center text-muted py-10">
                                    All stations have capacity configured ✅
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Recent Capacity Changes --}}
                <div class="col-xl-4">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Recent Capacity Changes</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">
                                    Last {{ count($recentCapacityChanges) }} updates
                                </span>
                            </div>
                        </div>

                        <div class="card-body pt-5">
                            @forelse($recentCapacityChanges as $row)
                                <div class="mb-4">
                                    <div class="fw-bold text-gray-900">
                                        {{ $row->station_name }}
                                    </div>
                                    <div class="text-muted fs-8">
                                        {{ $row->fuel_name }} ({{ $row->fuel_code }})
                                    </div>

                                    <div class="d-flex justify-content-between mt-1">
                                        <span class="text-muted fs-8">
                                            {{ \Carbon\Carbon::parse($row->effective_from)->format('d M Y') }}
                                        </span>
                                        <span class="fw-bold">
                                            {{ number_format((float) $row->capacity_liters, 0) }} L
                                        </span>
                                    </div>

                                    @if ($row->note)
                                        <div class="text-muted fs-8 mt-1">
                                            {{ $row->note }}
                                        </div>
                                    @endif
                                </div>

                                <div class="separator separator-dashed"></div>
                            @empty
                                <div class="text-center text-muted py-10">
                                    No recent capacity updates
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>



            {{-- =========================
            ROW 6: Expense Categories (keep yours)
        ========================= --}}
            <div class="row g-5 g-xl-8">
                <div class="col-xl-12">
                    <div class="card card-flush">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Expense Categories</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Monthly breakdown -
                                    {{ date('F Y') }}</span>
                            </div>
                        </div>

                        <div class="card-body pt-5">
                            <div class="table-responsive">
                                <table class="table table-row-bordered table-row-dashed gy-4 align-middle">
                                    <thead>
                                        <tr class="fw-bold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                            <th class="min-w-250px">Category</th>
                                            <th class="min-w-100px">Entries</th>
                                            <th class="min-w-150px">Total Amount</th>
                                            <th class="min-w-200px">Trend</th>
                                            <th class="min-w-100px text-end">Share</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalMonthExpense = (float)$monthlyExpensesByCategory->sum('total'); @endphp

                                        @forelse($monthlyExpensesByCategory as $expense)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-40px me-3">
                                                            @php
                                                                $c =
                                                                    $loop->index % 5 == 0
                                                                        ? 'primary'
                                                                        : ($loop->index % 5 == 1
                                                                            ? 'success'
                                                                            : ($loop->index % 5 == 2
                                                                                ? 'warning'
                                                                                : ($loop->index % 5 == 3
                                                                                    ? 'danger'
                                                                                    : 'info')));
                                                            @endphp
                                                            <span class="symbol-label bg-light-{{ $c }}">
                                                                <i
                                                                    class="ki-duotone ki-category fs-2x text-{{ $c }}">
                                                                    <span class="path1"></span><span
                                                                        class="path2"></span>
                                                                </i>
                                                            </span>
                                                        </div>
                                                        <div class="d-flex justify-content-start flex-column">
                                                            <span
                                                                class="text-gray-900 fw-bold mb-1">{{ $expense->category_name ?? 'Uncategorized' }}</span>
                                                            <span
                                                                class="text-gray-500 fw-semibold d-block fs-7">{{ $expense->description ?? 'No description' }}</span>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <span
                                                        class="text-gray-900 fw-bold fs-6">{{ (int) $expense->count }}</span>
                                                </td>

                                                <td>
                                                    <span
                                                        class="text-gray-900 fw-bold fs-5">৳{{ number_format((float) $expense->total, 2) }}</span>
                                                </td>

                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div id="kt_table_widget_3_chart_{{ $loop->index }}"
                                                            class="w-100px h-50px"></div>
                                                    </div>
                                                </td>

                                                <td class="text-end">
                                                    <span class="text-gray-900 fw-bold fs-6">
                                                        {{ $totalMonthExpense > 0 ? number_format(((float) $expense->total / $totalMonthExpense) * 100, 1) : 0 }}%
                                                    </span>
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

                                        @if ($monthlyExpensesByCategory->isNotEmpty())
                                            <tr class="border-top border-gray-200 border-2">
                                                <td><span class="text-gray-900 fw-bolder fs-5">TOTAL</span></td>
                                                <td><span
                                                        class="text-gray-900 fw-bolder fs-5">{{ (int) $monthlyExpensesByCategory->sum('count') }}</span>
                                                </td>
                                                <td><span
                                                        class="text-gray-900 fw-bolder fs-4">৳{{ number_format($totalMonthExpense, 2) }}</span>
                                                </td>
                                                <td></td>
                                                <td class="text-end"><span
                                                        class="text-gray-900 fw-bolder fs-5">100%</span></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- =========================
    ROW Y: Business Intelligence (NEW)
========================= --}}
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">

                {{-- Fuel Efficiency / Loss --}}
                <div class="col-xl-6">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Fuel Efficiency / Loss (MTD)</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Top variance (loss/gain)</span>
                            </div>
                            <div class="card-toolbar">
                                <span class="badge badge-light-primary">Ledger + Sales + Purchases</span>
                            </div>
                        </div>

                        <div class="card-body pt-5">
                            @forelse($fuelEfficiency as $row)
                                @php
                                    $variance = (float) ($row['variance'] ?? 0);
                                    $pct = (float) ($row['variance_percent'] ?? 0);
                                    $status = $row['status'] ?? 'normal';
                                    $badge = $status === 'loss' ? 'danger' : ($status === 'gain' ? 'success' : 'info');
                                @endphp

                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div>
                                        <div class="fw-bold text-gray-900">{{ $row['station_name'] ?? '-' }}</div>
                                        <div class="text-muted fs-8">
                                            {{ $row['fuel_name'] ?? '-' }} ({{ $row['fuel_code'] ?? '' }})
                                            • Expected: {{ number_format((float) ($row['expected_closing'] ?? 0), 2) }} L
                                            • Actual: {{ number_format((float) ($row['actual_closing'] ?? 0), 2) }} L
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <span class="badge badge-light-{{ $badge }} fw-bold">
                                            {{ $status === 'loss' ? 'LOSS' : ($status === 'gain' ? 'GAIN' : 'OK') }}
                                        </span>
                                        <div class="fw-bold text-gray-900 mt-1">
                                            {{ number_format($variance, 2) }} L
                                        </div>
                                        <div class="text-muted fs-8">
                                            {{ number_format($pct, 2) }}%
                                        </div>
                                    </div>
                                </div>

                                <div class="separator separator-dashed mb-4"></div>
                            @empty
                                <div class="text-center text-muted py-10">
                                    No significant variance found ✅
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Sales vs Target --}}
                <div class="col-xl-6">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Sales vs Target</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Last 7 days performance</span>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <div id="kt_chart_sales_vs_target" style="height: 320px;"></div>

                            <div class="separator my-5"></div>

                            @php
                                $ach = $salesVsTarget['achievement'] ?? [];
                                $avgAch = count($ach) > 0 ? array_sum($ach) / count($ach) : 0;
                            @endphp
                            <div class="d-flex justify-content-between">
                                <span class="text-muted fw-semibold">Avg Achievement</span>
                                <span class="fw-bold text-gray-900">{{ number_format((float) $avgAch, 1) }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">

                {{-- Vendor Performance --}}
                <div class="col-xl-6">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Vendor Performance</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Last 3 months summary</span>
                            </div>
                        </div>

                        <div class="card-body pt-5">
                            <div class="table-responsive">
                                <table class="table table-row-dashed align-middle gs-0 gy-3">
                                    <thead>
                                        <tr class="fs-7 fw-bold text-gray-500">
                                            <th class="min-w-180px">Vendor</th>
                                            <th class="min-w-110px">Orders</th>
                                            <th class="min-w-140px">Value</th>
                                            <th class="min-w-120px">Due</th>
                                            <th class="min-w-120px text-end">Complete%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($vendorPerformance as $v)
                                            @php
                                                $due = (float) ($v->outstanding ?? 0);
                                                $rate = (float) ($v->completion_rate ?? 0);
                                                $rateBadge =
                                                    $rate >= 90 ? 'success' : ($rate >= 70 ? 'warning' : 'danger');
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-gray-900">{{ $v->vendor_name }}</div>
                                                    <div class="text-muted fs-8">{{ $v->company_name ?? '' }}</div>
                                                </td>
                                                <td class="fw-bold text-gray-900">{{ (int) ($v->total_orders ?? 0) }}</td>
                                                <td class="fw-bold text-gray-900">
                                                    ৳{{ number_format((float) ($v->total_purchase_value ?? 0), 0) }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-light-{{ $due > 0 ? 'danger' : 'success' }}">
                                                        ৳{{ number_format($due, 0) }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge badge-light-{{ $rateBadge }}">
                                                        {{ number_format($rate, 1) }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-10">
                                                    No vendor performance data
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Station Profitability --}}
                <div class="col-xl-6">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Station Profitability (MTD)</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Top 10 by net profit</span>
                            </div>
                        </div>

                        <div class="card-body pt-5">
                            <div class="table-responsive">
                                <table class="table table-row-dashed align-middle gs-0 gy-3">
                                    <thead>
                                        <tr class="fs-7 fw-bold text-gray-500">
                                            <th class="min-w-180px">Station</th>
                                            <th class="min-w-130px">Sales</th>
                                            <th class="min-w-130px">Net Profit</th>
                                            <th class="min-w-110px text-end">Margin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stationProfitability as $s)
                                            @php
                                                $net = (float) ($s['net_profit'] ?? 0);
                                                $margin = (float) ($s['profit_margin'] ?? 0);
                                                $badge = $net >= 0 ? 'success' : 'danger';
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-gray-900">{{ $s['station_name'] ?? '-' }}
                                                    </div>
                                                    <div class="text-muted fs-8">{{ $s['location'] ?? '' }}</div>
                                                </td>
                                                <td class="fw-bold text-gray-900">
                                                    ৳{{ number_format((float) ($s['sales'] ?? 0), 0) }}</td>
                                                <td>
                                                    <span class="badge badge-light-{{ $badge }}">
                                                        ৳{{ number_format($net, 0) }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-bold text-gray-900">
                                                    {{ number_format($margin, 2) }}%</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-10">
                                                    No profitability data
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            {{-- Fuel Price Trends --}}
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                <div class="col-xl-12">
                    <div class="card card-flush">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bold text-gray-900">Fuel Price Trends</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Last 30 days average selling price</span>
                            </div>
                        </div>

                        <div class="card-body pt-5">
                            <div id="kt_chart_fuel_price_trend" style="height: 380px;"></div>

                            <div class="separator my-6"></div>

                            <div class="row g-5">
                                @forelse($fuelPriceTrends as $f)
                                    @php
                                        $chg = (float) ($f['price_change'] ?? 0);
                                        $chgPct = (float) ($f['price_change_percent'] ?? 0);
                                        $badge = $chg >= 0 ? 'success' : 'danger';
                                    @endphp
                                    <div class="col-md-4">
                                        <div class="p-4 border rounded">
                                            <div class="fw-bold text-gray-900">
                                                {{ $f['fuel_name'] ?? '-' }} ({{ $f['fuel_code'] ?? '' }})
                                            </div>
                                            <div class="text-muted fs-8 mt-1">
                                                Current: ৳{{ number_format((float) ($f['current_price'] ?? 0), 2) }}
                                            </div>
                                            <div class="text-muted fs-8">
                                                30d ago: ৳{{ number_format((float) ($f['price_30d_ago'] ?? 0), 2) }}
                                            </div>
                                            <div class="mt-2">
                                                <span class="badge badge-light-{{ $badge }}">
                                                    {{ $chg >= 0 ? '+' : '' }}{{ number_format($chg, 2) }}
                                                    ({{ $chgPct >= 0 ? '+' : '' }}{{ number_format($chgPct, 2) }}%)
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-center text-muted py-10">
                                            No price trend data available
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
@endsection


@push('scripts')
    <script src="{{ asset('assets/plugins/apexcharts/apexcharts.bundle.js') }}"></script>

    <script>
        // -------------------------
        // Sales Trend (14 days)
        // -------------------------
        function initSalesTrendChart() {
            const el = document.getElementById('kt_chart_sales_trend');
            if (!el) return;

            const labels = @json($salesTrend14Days['labels'] ?? []);
            const amount = @json($salesTrend14Days['amount'] ?? []);
            const liters = @json($salesTrend14Days['liters'] ?? []);

            const options = {
                series: [{
                        name: 'Sales Amount',
                        data: amount
                    },
                    {
                        name: 'Sold Liters',
                        data: liters
                    }
                ],
                chart: {
                    type: 'line',
                    height: 360,
                    toolbar: {
                        show: false
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: labels
                },
                yaxis: [{
                        labels: {
                            formatter: (v) => '৳' + Number(v || 0).toLocaleString()
                        }
                    },
                    {
                        opposite: true,
                        labels: {
                            formatter: (v) => Number(v || 0).toLocaleString() + ' L'
                        }
                    }
                ],
                tooltip: {
                    shared: true,
                    y: {
                        formatter: function(val, opts) {
                            const s = opts.seriesIndex;
                            if (s === 0) return '৳' + Number(val || 0).toLocaleString();
                            return Number(val || 0).toLocaleString() + ' L';
                        }
                    }
                }
            };

            (new ApexCharts(el, options)).render();
        }

        // -------------------------
        // Stock Movement (14 days) IN vs OUT
        // -------------------------
        function initStockMoveChart() {
            const el = document.getElementById('kt_chart_stock_move');
            if (!el) return;

            const labels = @json($stockMoveTrend14['labels'] ?? []);
            const qtyIn = @json($stockMoveTrend14['qtyIn'] ?? []);
            const qtyOut = @json($stockMoveTrend14['qtyOut'] ?? []);

            const options = {
                series: [{
                        name: 'Stock IN',
                        data: qtyIn
                    },
                    {
                        name: 'Stock OUT',
                        data: qtyOut
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 360,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '55%'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: labels
                },
                yaxis: {
                    labels: {
                        formatter: (v) => Number(v || 0).toLocaleString() + ' L'
                    }
                },
                tooltip: {
                    y: {
                        formatter: (v) => Number(v || 0).toLocaleString() + ' L'
                    }
                }
            };

            (new ApexCharts(el, options)).render();
        }

        // -------------------------
        // Monthly Expenses (existing)
        // -------------------------
        function initExpensesChart() {
            const element = document.getElementById("kt_chart_expenses");
            if (!element) return;

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
                    }
                },
                yaxis: {
                    labels: {
                        formatter: (val) => '৳' + Number(val || 0).toLocaleString()
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
                        formatter: (val) => '৳' + Number(val || 0).toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })
                    }
                }
            };

            (new ApexCharts(element, options)).render();
        }

        // -------------------------
        // Stock Distribution (existing)
        // -------------------------
        function initStockDistributionChart() {
            const element = document.getElementById("kt_chart_stock_distribution");
            if (!element) return;

            const stockData = @json($fuelTypeStocks ?? []);
            const series = stockData.map(item => parseFloat(item.total_quantity));
            const labels = stockData.map(item => item.fuel_name);

            const options = {
                series,
                chart: {
                    type: 'donut',
                    height: 350
                },
                labels,
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
                    fontWeight: 400
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
                                    formatter: function(w) {
                                        const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        return total.toLocaleString() + ' L';
                                    }
                                }
                            }
                        }
                    }
                }
            };

            (new ApexCharts(element, options)).render();
        }

        // -------------------------
        // Mini trends (keep demo)
        // -------------------------
        function initMiniTrendCharts() {
            const categories = @json($monthlyExpensesByCategory ?? []);
            const colors = ['primary', 'success', 'warning', 'danger', 'info'];

            categories.forEach((category, index) => {
                const element = document.getElementById(`kt_table_widget_3_chart_${index}`);
                if (!element) return;

                const data = Array.from({
                    length: 7
                }, () => Math.floor(Math.random() * 1000) + 500);

                const options = {
                    series: [{
                        name: 'Trend',
                        data
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

                (new ApexCharts(element, options)).render();
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            initSalesTrendChart();
            initStockMoveChart();
            initExpensesChart();
            initStockDistributionChart();
            initMiniTrendCharts();
            initSalesVsTargetChart();
            initFuelPriceTrendChart();
        });
    </script>


    <script>
        // -------------------------
        // Sales vs Target (7 days)
        // -------------------------
        function initSalesVsTargetChart() {
            const el = document.getElementById('kt_chart_sales_vs_target');
            if (!el) return;

            const labels = @json($salesVsTarget['labels'] ?? []);
            const actual = @json($salesVsTarget['actual'] ?? []);
            const target = @json($salesVsTarget['target'] ?? []);

            const options = {
                series: [{
                        name: 'Actual Sales',
                        data: actual
                    },
                    {
                        name: 'Target',
                        data: target
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '55%'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: labels
                },
                yaxis: {
                    labels: {
                        formatter: (v) => '৳' + Number(v || 0).toLocaleString()
                    }
                },
                tooltip: {
                    y: {
                        formatter: (v) => '৳' + Number(v || 0).toLocaleString()
                    }
                }
            };

            (new ApexCharts(el, options)).render();
        }

        // -------------------------
        // Fuel Price Trend (30 days) multi-line
        // -------------------------
        function initFuelPriceTrendChart() {
            const el = document.getElementById('kt_chart_fuel_price_trend');
            if (!el) return;

            const trend = @json($fuelPriceTrends ?? []);

            // series: [{name: 'Diesel', data:[...]}, ...]
            const series = trend.map(f => {
                const points = (f.price_data || []).map(p => Number(p.avg_price || 0));
                return {
                    name: `${f.fuel_name}${f.fuel_code ? ' (' + f.fuel_code + ')' : ''}`,
                    data: points
                };
            });

            // x-axis labels based on first fuel price_data dates
            const categories = (trend[0] && trend[0].price_data) ?
                trend[0].price_data.map(p => {
                    const d = new Date(p.sale_date);
                    return isNaN(d.getTime()) ? p.sale_date : d.toLocaleDateString(undefined, {
                        day: '2-digit',
                        month: 'short'
                    });
                }) :
                [];

            const options = {
                series,
                chart: {
                    type: 'line',
                    height: 380,
                    toolbar: {
                        show: false
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories
                },
                yaxis: {
                    labels: {
                        formatter: (v) => '৳' + Number(v || 0).toLocaleString()
                    }
                },
                tooltip: {
                    y: {
                        formatter: (v) => '৳' + Number(v || 0).toLocaleString()
                    }
                }
            };

            (new ApexCharts(el, options)).render();
        }
    </script>


    <style>
        .card {
            border: 0;
            box-shadow: 0px 0px 20px 0px rgba(76, 87, 125, 0.02);
            transition: all 0.25s ease;
        }

        .hover-elevate-up:hover {
            box-shadow: 0px 0px 30px 0px rgba(76, 87, 125, 0.09);
            transform: translateY(-2px);
        }

        .timeline .timeline-item:last-child .timeline-line {
            display: none;
        }

        .timeline .timeline-line {
            border-left: 1px dashed #E4E6EF;
        }

        .progress-bar {
            transition: width 1s ease-in-out;
        }
    </style>
@endpush
