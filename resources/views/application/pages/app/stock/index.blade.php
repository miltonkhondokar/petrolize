@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            {{-- Filter Section --}}
            <div class="card card-custom gutter-b mb-5 mb-xl-8 shadow-sm">
                <div class="card-header bg-light-primary">
                    <div class="card-title">
                        <h3 class="card-label">
                            <i class="fas fa-filter"></i> Filter
                            <small>filter stock status</small>
                        </h3>
                    </div>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('stock.index') }}">
                        <div class="row g-3">

                            <div class="col-md-2">
                                <select name="region_uuid" class="form-select form-select-solid">
                                    <option value="">All Regions</option>
                                    @foreach ($regions as $r)
                                        <option value="{{ $r->uuid }}"
                                            {{ isset($filters['region_uuid']) && $filters['region_uuid'] == $r->uuid ? 'selected' : '' }}>
                                            {{ $r->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="governorate_uuid" class="form-select form-select-solid">
                                    <option value="">All Governorates</option>
                                    @foreach ($governorates as $g)
                                        <option value="{{ $g->uuid }}"
                                            {{ isset($filters['governorate_uuid']) && $filters['governorate_uuid'] == $g->uuid ? 'selected' : '' }}>
                                            {{ $g->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="center_uuid" class="form-select form-select-solid">
                                    <option value="">All Centers</option>
                                    @foreach ($centers as $c)
                                        <option value="{{ $c->uuid }}"
                                            {{ isset($filters['center_uuid']) && $filters['center_uuid'] == $c->uuid ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="city_uuid" class="form-select form-select-solid">
                                    <option value="">All Cities</option>
                                    @foreach ($cities as $ci)
                                        <option value="{{ $ci->uuid }}"
                                            {{ isset($filters['city_uuid']) && $filters['city_uuid'] == $ci->uuid ? 'selected' : '' }}>
                                            {{ $ci->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="fuel_station_uuid" class="form-select form-select-solid">
                                    <option value="">All Stations</option>
                                    @foreach ($fuelStations as $station)
                                        <option value="{{ $station->uuid }}"
                                            {{ isset($filters['fuel_station_uuid']) && $filters['fuel_station_uuid'] == $station->uuid ? 'selected' : '' }}>
                                            {{ $station->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="fuel_type_uuid" class="form-select form-select-solid">
                                    <option value="">All Fuel Types</option>
                                    @foreach ($fuelTypes as $ft)
                                        <option value="{{ $ft->uuid }}"
                                            {{ isset($filters['fuel_type_uuid']) && $filters['fuel_type_uuid'] == $ft->uuid ? 'selected' : '' }}>
                                            {{ $ft->name }} ({{ $ft->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 text-end">
                                <button class="btn btn-info w-100"><i
                                        class="ki-duotone ki-filter fs-3 me-2"></i>Filter</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('stock.index') }}" class="btn btn-warning w-100">
                                    <i class="ki-duotone ki-reload fs-3 me-2"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Stock Table --}}
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light-primary">
                    <h3 class="card-title fw-bold fs-3 mb-1">Stock Status</h3>

                    <div class="d-flex">
                        <span class="badge badge-light-success fs-6 me-3">
                            As of: {{ \Carbon\Carbon::parse($asOfDate)->format('d M Y') }}
                        </span>
                        <span class="badge badge-light-info fs-6">
                            Rows: {{ $stockRows->total() }}
                        </span>
                    </div>
                </div>

                <div class="card-body py-3">
                    <div class="table-responsive overflow-visible">
                        <table class="table align-middle table-row-dashed table-bordered fs-6 gy-4">
                            <thead class="bg-light-dark">
                                <tr class="fw-bold text-gray-700">
                                    <th style="width:60px;">#</th>
                                    <th>Center</th>
                                    <th>Fuel Station</th>
                                    <th>Fuel Type</th>
                                    <th>Unit</th>
                                    <th class="text-end">Current Stock</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Line Total</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($stockRows as $index => $row)
                                    <tr>
                                        <td>{{ $stockRows->firstItem() + $index }}</td>

                                        <td>
                                            <div class="fw-semibold text-gray-800">
                                                {{ $row->center_label ?? 'N/A' }}
                                            </div>
                                            <div class="text-muted fs-7">
                                                @if($row->region_name) {{ $row->region_name }} @endif
                                                @if($row->governorate_name) / {{ $row->governorate_name }} @endif
                                                @if($row->city_name) / {{ $row->city_name }} @endif
                                            </div>
                                        </td>

                                        <td>
                                            <div class="fw-bold text-gray-800">{{ $row->station_name }}</div>
                                            <div class="text-muted fs-7">{{ $row->station_location ?? '' }}</div>
                                        </td>

                                        <td>
                                            {{ $row->fuel_name }}
                                            <span class="text-muted">({{ $row->fuel_code }})</span>
                                        </td>

                                        <td>{{ $row->unit_abbr ?? '-' }}</td>

                                        <td class="text-end">
                                            <span class="badge badge-light-success fw-bold">
                                                {{ number_format((float)$row->current_stock, 3) }}
                                            </span>
                                        </td>

                                        <td class="text-end">
                                            <span class="fw-semibold">
                                                {{ number_format((float)$row->unit_price, 2) }}
                                            </span>
                                        </td>

                                        <td class="text-end fw-bold">
                                            {{ number_format((float)$row->line_total, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-10">
                                            <i class="ki-duotone ki-information-2 fs-2x text-gray-400 mb-2"></i><br>
                                            No stock records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            @if($stockRows->count() > 0)
                                <tfoot>
                                    <tr>
                                        <td colspan="7" class="text-end fw-bold">Grand Total</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format((float)$pageGrandTotal, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $stockRows->appends($filters)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
