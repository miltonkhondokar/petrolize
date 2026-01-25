<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\ApiResponseService;
use App\Services\CostEntryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CostEntryController extends Controller
{
    public function __construct(private CostEntryService $service)
    {
    }

    // GET /api/v1/cost-entries
    public function index(Request $request)
    {
        try {
            $filters = $request->only([
                'fuel_station_uuid',
                'cost_category_uuid',
                'is_active',
                'expense_date',
                'from',
                'to'
            ]);

            $perPage = (int) ($request->get('per_page', 20));
            $data = $this->service->paginate($filters, $perPage);

            return ApiResponseService::success($data, 'Cost entries fetched');
        } catch (\Throwable $e) {
            Log::error('CostEntryController@index error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to fetch cost entries');
        }
    }

    // POST /api/v1/cost-entries
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fuel_station_uuid'   => 'required|exists:fuel_stations,uuid',
            'cost_category_uuid'  => 'required|exists:cost_categories,uuid',
            'amount'              => 'required|numeric|min:0',
            'expense_date'        => 'required|date',
            'reference_no'        => 'nullable|string|max:255',
            'note'                => 'nullable|string',
            'is_active'           => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validation($validator->errors());
        }

        try {
            $entry = $this->service->create($validator->validated());

            // audit
            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Created Cost Entry',
                'type'       => 'cost_entry_create',
                'item_id'    => $entry->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return ApiResponseService::success($entry, 'Cost entry created', 1, 201);
        } catch (\Throwable $e) {
            Log::error('CostEntryController@store error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to create cost entry');
        }
    }

    // GET /api/v1/cost-entries/{uuid}
    public function show(string $uuid)
    {
        try {
            $entry = $this->service->findOrFail($uuid);
            return ApiResponseService::success($entry, 'Cost entry fetched');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseService::notFound('Cost entry not found');
        } catch (\Throwable $e) {
            Log::error('CostEntryController@show error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to fetch cost entry');
        }
    }

    // PUT/PATCH /api/v1/cost-entries/{uuid}
    public function update(Request $request, string $uuid)
    {
        $validator = Validator::make($request->all(), [
            'fuel_station_uuid'   => 'required|exists:fuel_stations,uuid',
            'cost_category_uuid'  => 'required|exists:cost_categories,uuid',
            'amount'              => 'required|numeric|min:0',
            'expense_date'        => 'required|date',
            'reference_no'        => 'nullable|string|max:255',
            'note'                => 'nullable|string',
            'is_active'           => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validation($validator->errors());
        }

        try {
            $entry = $this->service->update($uuid, $validator->validated());

            // audit
            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Updated Cost Entry',
                'type'       => 'cost_entry_update',
                'item_id'    => $entry->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return ApiResponseService::success($entry, 'Cost entry updated');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseService::notFound('Cost entry not found');
        } catch (\Throwable $e) {
            Log::error('CostEntryController@update error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to update cost entry');
        }
    }

    // DELETE /api/v1/cost-entries/{uuid}
    public function destroy(Request $request, string $uuid)
    {
        try {
            // fetch first for audit item_id
            $entry = $this->service->findOrFail($uuid);
            $entryId = $entry->id;

            $this->service->delete($uuid);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Deleted Cost Entry',
                'type'       => 'cost_entry_delete',
                'item_id'    => $entryId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return ApiResponseService::success(null, 'Cost entry deleted');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseService::notFound('Cost entry not found');
        } catch (\Throwable $e) {
            Log::error('CostEntryController@destroy error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to delete cost entry');
        }
    }
}





// 4) Request formats (mobile)
// Common headers
// Authorization: Bearer <access_token>
// Accept: application/json
// Content-Type: application/json

// Create

// POST /api/v1/cost-entries

// {
//   "fuel_station_uuid": "9f2d6f6a-5f1e-4c7a-9b4a-6b2a1f9c3c11",
//   "cost_category_uuid": "c1111111-2222-3333-4444-555555555555",
//   "amount": 1500.75,
//   "expense_date": "2026-01-25",
//   "reference_no": "INV-1001",
//   "note": "Generator oil change",
//   "is_active": true
// }

// Update

// PUT/PATCH /api/v1/cost-entries/{uuid}

// {
//   "fuel_station_uuid": "9f2d6f6a-5f1e-4c7a-9b4a-6b2a1f9c3c11",
//   "cost_category_uuid": "c1111111-2222-3333-4444-555555555555",
//   "amount": 2000,
//   "expense_date": "2026-01-25",
//   "reference_no": "INV-1001-REV",
//   "note": "Updated note",
//   "is_active": true
// }

// List with filters

// GET
// /api/v1/cost-entries?fuel_station_uuid=...&cost_category_uuid=...&is_active=1&expense_date=2026-01-25&from=2026-01-01&to=2026-01-31&per_page=20

// No body.

// Delete

// DELETE /api/v1/cost-entries/{uuid}

// No body.
