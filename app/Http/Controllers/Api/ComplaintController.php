<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\ApiResponseService;
use App\Services\ComplaintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function __construct(private ComplaintService $service)
    {
    }

    // GET /api/v1/complaints
    public function index(Request $request)
    {
        try {
            $filters = $request->only([
                'fuel_station_uuid',
                'category',
                'status',
                'is_active',
                'complaint_date',
                'from',
                'to',
            ]);

            $perPage = (int) ($request->get('per_page', 20));
            $data = $this->service->paginate($filters, $perPage);

            return ApiResponseService::success($data, 'Complaints fetched');
        } catch (\Throwable $e) {
            Log::error('Api ComplaintController@index error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to fetch complaints');
        }
    }

    // POST /api/v1/complaints
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fuel_station_uuid' => 'required|exists:fuel_stations,uuid',
            'category'          => 'nullable|string|max:100',
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'status'            => 'required|in:open,in_progress,resolved',
            'complaint_date'    => 'required|date',
            'resolved_date'     => 'nullable|date|after_or_equal:complaint_date',
            'is_active'         => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validation($validator->errors());
        }

        try {
            $complaint = $this->service->create($validator->validated());

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Created Fuel Station Complaint',
                'type'       => 'complaint_create',
                'item_id'    => $complaint->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return ApiResponseService::success($complaint, 'Complaint created', 1, 201);
        } catch (\Throwable $e) {
            Log::error('Api ComplaintController@store error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to create complaint');
        }
    }

    // GET /api/v1/complaints/{uuid}
    public function show(string $uuid)
    {
        try {
            $complaint = $this->service->findOrFail($uuid);
            return ApiResponseService::success($complaint, 'Complaint fetched');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseService::notFound('Complaint not found');
        } catch (\Throwable $e) {
            Log::error('Api ComplaintController@show error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to fetch complaint');
        }
    }

    // PUT/PATCH /api/v1/complaints/{uuid}
    public function update(Request $request, string $uuid)
    {
        $validator = Validator::make($request->all(), [
            'fuel_station_uuid' => 'required|exists:fuel_stations,uuid',
            'category'          => 'nullable|string|max:100',
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'status'            => 'required|in:open,in_progress,resolved',
            'complaint_date'    => 'required|date',
            'resolved_date'     => 'nullable|date|after_or_equal:complaint_date',
            'is_active'         => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validation($validator->errors());
        }

        try {
            $complaint = $this->service->update($uuid, $validator->validated());

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Updated Fuel Station Complaint',
                'type'       => 'complaint_update',
                'item_id'    => $complaint->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return ApiResponseService::success($complaint, 'Complaint updated');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseService::notFound('Complaint not found');
        } catch (\Throwable $e) {
            Log::error('Api ComplaintController@update error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to update complaint');
        }
    }

    // DELETE /api/v1/complaints/{uuid}
    public function destroy(Request $request, string $uuid)
    {
        try {
            $complaint = $this->service->findOrFail($uuid);
            $complaintId = $complaint->id;

            $this->service->delete($uuid);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Deleted Fuel Station Complaint',
                'type'       => 'complaint_delete',
                'item_id'    => $complaintId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return ApiResponseService::success(null, 'Complaint deleted');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseService::notFound('Complaint not found');
        } catch (\Throwable $e) {
            Log::error('Api ComplaintController@destroy error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to delete complaint');
        }
    }

    // POST /api/v1/complaints/{uuid}/status
    public function statusUpdate(Request $request, string $uuid)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:open,in_progress,resolved',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validation($validator->errors());
        }

        try {
            $complaint = $this->service->updateStatus($uuid, $request->status);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Updated Fuel Station Complaint Status',
                'type'       => 'complaint_status_update',
                'item_id'    => $complaint->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return ApiResponseService::success($complaint, 'Complaint status updated');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseService::notFound('Complaint not found');
        } catch (\Throwable $e) {
            Log::error('Api ComplaintController@statusUpdate error', ['e' => $e]);
            return ApiResponseService::serverError('Failed to update complaint status');
        }
    }
}





// 4) Request formats (mobile)
// Common headers
// Authorization: Bearer <access_token>
// Accept: application/json
// Content-Type: application/json

// Create complaint

// POST /api/v1/complaints

// {
//   "fuel_station_uuid": "9f2d6f6a-5f1e-4c7a-9b4a-6b2a1f9c3c11",
//   "category": "fuel_shortage",
//   "title": "Diesel shortage",
//   "description": "Diesel stock finished early today.",
//   "status": "open",
//   "complaint_date": "2026-01-25",
//   "resolved_date": null,
//   "is_active": true
// }

// Update complaint

// PUT/PATCH /api/v1/complaints/{uuid}

// {
//   "fuel_station_uuid": "9f2d6f6a-5f1e-4c7a-9b4a-6b2a1f9c3c11",
//   "category": "nozzle_issue",
//   "title": "Nozzle leak issue",
//   "description": "Nozzle #2 leaks during dispensing.",
//   "status": "in_progress",
//   "complaint_date": "2026-01-25",
//   "resolved_date": "2026-01-26",
//   "is_active": true
// }

// Update only status

// POST /api/v1/complaints/{uuid}/status

// {
//   "status": "resolved"
// }

// List with filters

// GET
// /api/v1/complaints?fuel_station_uuid=...&category=fuel_shortage&status=open&is_active=1&complaint_date=2026-01-25&from=2026-01-01&to=2026-01-31&per_page=20

// No body.

// Delete

// DELETE /api/v1/complaints/{uuid}

// No body.
