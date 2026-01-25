<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use App\Services\FuelSalesDayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FuelSalesController extends Controller
{
    public function __construct(private FuelSalesDayService $service)
    {
    }

    // GET /api/v1/fuel-sales-days
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['fuel_station_uuid', 'sale_date', 'status', 'from', 'to']);
            $perPage = (int) ($request->get('per_page', 20));

            $days = $this->service->paginate($filters, $perPage);

            return ApiResponseService::success($days, 'Fuel sales days fetched');
        } catch (\Throwable $e) {
            Log::error('FuelSalesController@index error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to fetch fuel sales days');
        }
    }

    // POST /api/v1/fuel-sales-days  (Create DRAFT)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fuel_station_uuid'       => 'required|uuid',
            'sale_date'               => 'required|date',
            'note'                    => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.fuel_type_uuid'  => 'required|uuid',
            'items.*.nozzle_number'   => 'nullable|integer|min:1',
            'items.*.opening_reading' => 'required|numeric|min:0',
            'items.*.closing_reading' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validation($validator->errors());
        }

        try {
            $day = $this->service->createDraft($validator->validated());
            return ApiResponseService::success($day, 'Fuel sales day created as draft', 1, 201);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            // abort(422/403/...) bubbles here
            return ApiResponseService::error($e->getMessage(), 0, $e->getStatusCode());
        } catch (\Throwable $e) {
            Log::error('FuelSalesController@store error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to create fuel sales day');
        }
    }

    // GET /api/v1/fuel-sales-days/{uuid}
    public function show(string $uuid)
    {
        try {
            $day = $this->service->findOrFail($uuid);
            return ApiResponseService::success($day, 'Fuel sales day fetched');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseService::notFound('Fuel sales day not found');
        } catch (\Throwable $e) {
            Log::error('FuelSalesController@show error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to fetch fuel sales day');
        }
    }

    // PUT/PATCH /api/v1/fuel-sales-days/{uuid} (Update DRAFT only)
    public function update(Request $request, string $uuid)
    {
        $validator = Validator::make($request->all(), [
            'fuel_station_uuid'       => 'required|uuid',
            'sale_date'               => 'required|date',
            'note'                    => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.fuel_type_uuid'  => 'required|uuid',
            'items.*.nozzle_number'   => 'nullable|integer|min:1',
            'items.*.opening_reading' => 'required|numeric|min:0',
            'items.*.closing_reading' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validation($validator->errors());
        }

        try {
            $day = $this->service->updateDraft($uuid, $validator->validated());
            return ApiResponseService::success($day, 'Fuel sales day updated');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseService::notFound('Fuel sales day not found');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return ApiResponseService::error($e->getMessage(), 0, $e->getStatusCode());
        } catch (\Throwable $e) {
            Log::error('FuelSalesController@update error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to update fuel sales day');
        }
    }

    // POST /api/v1/fuel-sales-days/{uuid}/submit
    public function submit(Request $request, string $uuid)
    {
        $validator = Validator::make($request->all(), [
            'cash_amount' => 'required|numeric|min:0',
            'bank_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validation($validator->errors());
        }

        try {
            $cash = (float) $request->cash_amount;
            $bank = (float) $request->bank_amount;

            $day = $this->service->submit($uuid, $cash, $bank);
            return ApiResponseService::success($day, 'Sales day submitted and stock updated');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseService::notFound('Fuel sales day not found');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return ApiResponseService::error($e->getMessage(), 0, $e->getStatusCode());
        } catch (\Throwable $e) {
            Log::error('FuelSalesController@submit error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to submit sales day');
        }
    }

    // GET /api/v1/fuel-stations/{uuid}/fuel-prices
    public function stationFuelPrices(string $uuid)
    {
        try {
            $prices = $this->service->stationFuelPrices($uuid);
            return ApiResponseService::success($prices, 'Fuel prices fetched');
        } catch (\Throwable $e) {
            Log::error('FuelSalesController@stationFuelPrices error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to fetch fuel prices');
        }
    }
}






// 1) Create Sales Day (Draft)

// POST /api/v1/fuel-sales-days

// {
//   "fuel_station_uuid": "9f2d6f6a-5f1e-4c7a-9b4a-6b2a1f9c3c11",
//   "sale_date": "2026-01-25",
//   "note": "Day end sales",
//   "items": [
//     {
//       "fuel_type_uuid": "a1111111-2222-3333-4444-555555555555",
//       "nozzle_number": 1,
//       "opening_reading": 1200.5,
//       "closing_reading": 1350.75
//     },
//     {
//       "fuel_type_uuid": "b1111111-2222-3333-4444-555555555555",
//       "nozzle_number": 2,
//       "opening_reading": 900,
//       "closing_reading": 980
//     }
//   ]
// }



// 2) Update Sales Day (Draft only)

// PUT/PATCH /api/v1/fuel-sales-days/{uuid}

// {
//   "fuel_station_uuid": "9f2d6f6a-5f1e-4c7a-9b4a-6b2a1f9c3c11",
//   "sale_date": "2026-01-25",
//   "note": "Updated note",
//   "items": [
//     {
//       "fuel_type_uuid": "a1111111-2222-3333-4444-555555555555",
//       "nozzle_number": 1,
//       "opening_reading": 1200.5,
//       "closing_reading": 1360.0
//     }
//   ]
// }



// 3) Submit Sales Day (Final + Stock Out)

// POST /api/v1/fuel-sales-days/{uuid}/submit

// {
//   "cash_amount": 50000,
//   "bank_amount": 20000
// }



// 4) List Sales Days (with filters)

// GET /api/v1/fuel-sales-days?fuel_station_uuid=...&status=draft&from=2026-01-01&to=2026-01-31&per_page=20

// No body.

// Filters supported:

// fuel_station_uuid

// sale_date

// status (draft / submitted)

// from / to

// per_page



// 5) Show Single Sales Day

// GET /api/v1/fuel-sales-days/{uuid}





// 6) Get Station Fuel Prices (for mobile UI)

// GET /api/v1/fuel-stations/{uuid}/fuel-prices


// {
//   "success": true,
//   "response": { "code": 1, "meaning": "success", "message": "Fuel prices fetched" },
//   "data": {
//     "fuel-type-uuid-1": 112.5,
//     "fuel-type-uuid-2": 109.0
//   }
// }
