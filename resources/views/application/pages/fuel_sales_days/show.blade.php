@extends('application.layouts.app')

@section('page-title')
    @include('application.partials.page-title', ['breadcrumb' => $breadcrumb])
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        @if ($errors->any())
            <div class="alert alert-warning p-5">
                <h4 class="mb-2">Error</h4>
                <ul class="mb-0">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-light-primary d-flex justify-content-between">
                <h3 class="card-title">Fuel Sales Day Details</h3>
                <div class="d-flex gap-2">
                    <a href="{{ route('fuel_sales_days.index') }}" class="btn btn-sm btn-primary">Back</a>
                    @if($day->status=='draft')
                        <a href="{{ route('fuel_sales_days.edit', $day->uuid) }}" class="btn btn-sm btn-warning">Edit</a>
                    @endif
                </div>
            </div>

            <div class="card-body">

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div><strong>Station:</strong> {{ $day->station->name ?? '-' }}</div>
                        <div><strong>Date:</strong> {{ $day->sale_date?->format('d M Y') }}</div>
                        <div><strong>Status:</strong>
                            <span class="badge badge-light-{{ $day->status=='draft'?'warning':($day->status=='submitted'?'info':'success') }}">
                                {{ ucfirst($day->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div><strong>Total:</strong> {{ number_format((float)$day->total_amount,2) }}</div>
                        <div><strong>Cash:</strong> {{ number_format((float)$day->cash_amount,2) }}</div>
                        <div><strong>Bank:</strong> {{ number_format((float)$day->bank_amount,2) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div><strong>Manager:</strong> {{ $day->manager->name ?? '-' }}</div>
                        <div><strong>Note:</strong> {{ $day->note ?? '-' }}</div>
                        <div><strong>UUID:</strong> {{ $day->uuid }}</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Fuel</th>
                                <th>Nozzle</th>
                                <th class="text-end">Opening</th>
                                <th class="text-end">Closing</th>
                                <th class="text-end">Sold</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($day->items as $it)
                                <tr>
                                    <td>{{ $it->fuelType->name ?? '-' }}</td>
                                    <td>{{ $it->nozzle_number ?? '-' }}</td>
                                    <td class="text-end">{{ number_format((float)$it->opening_reading,3) }}</td>
                                    <td class="text-end">{{ number_format((float)$it->closing_reading,3) }}</td>
                                    <td class="text-end">{{ number_format((float)$it->sold_qty,3) }}</td>
                                    <td class="text-end">{{ number_format((float)$it->price_per_unit,2) }}</td>
                                    <td class="text-end">{{ number_format((float)$it->line_total,2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($day->status=='draft')
                    <hr class="my-5">
                    <h4>Submit Day End</h4>
                    <form method="POST" action="{{ route('fuel_sales_days.submit', $day->uuid) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label required">Cash Amount</label>
                                <input type="number" step="0.01" name="cash_amount"
                                       class="form-control form-control-solid"
                                       value="{{ old('cash_amount', $day->cash_amount) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Bank Amount</label>
                                <input type="number" step="0.01" name="bank_amount"
                                       class="form-control form-control-solid"
                                       value="{{ old('bank_amount', $day->bank_amount) }}" required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button class="btn btn-success w-100">Submit & Deduct Stock</button>
                            </div>
                        </div>
                        <div class="form-text mt-2">
                            Note: Cash + Bank must equal Total amount. Stock ledger will be updated on submit.
                        </div>
                    </form>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection
