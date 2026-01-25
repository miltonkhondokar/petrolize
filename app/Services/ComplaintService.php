<?php

namespace App\Services;

use App\Models\FuelStationComplaint;
use Illuminate\Support\Facades\DB;

class ComplaintService
{
    public function paginate(array $filters, int $perPage = 20)
    {
        $q = FuelStationComplaint::with('fuelStation')->latest();

        if (!empty($filters['fuel_station_uuid'])) {
            $q->where('fuel_station_uuid', $filters['fuel_station_uuid']);
        }
        if (!empty($filters['category'])) {
            $q->where('category', $filters['category']);
        }
        if (!empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        if (array_key_exists('is_active', $filters) && $filters['is_active'] !== '' && $filters['is_active'] !== null) {
            $q->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $filters['is_active']);
        }
        if (!empty($filters['complaint_date'])) {
            $q->whereDate('complaint_date', $filters['complaint_date']);
        }
        if (!empty($filters['from'])) {
            $q->whereDate('complaint_date', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $q->whereDate('complaint_date', '<=', $filters['to']);
        }

        return $q->paginate($perPage)->withQueryString();
    }

    public function findOrFail(string $uuid): FuelStationComplaint
    {
        return FuelStationComplaint::with('fuelStation')
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function create(array $payload): FuelStationComplaint
    {
        return DB::transaction(function () use ($payload) {
            $complaint = FuelStationComplaint::create($payload);
            return $complaint->fresh()->load('fuelStation');
        });
    }

    public function update(string $uuid, array $payload): FuelStationComplaint
    {
        return DB::transaction(function () use ($uuid, $payload) {
            $complaint = FuelStationComplaint::where('uuid', $uuid)->lockForUpdate()->firstOrFail();
            $complaint->update($payload);
            return $complaint->fresh()->load('fuelStation');
        });
    }

    public function delete(string $uuid): void
    {
        DB::transaction(function () use ($uuid) {
            $complaint = FuelStationComplaint::where('uuid', $uuid)->lockForUpdate()->firstOrFail();
            $complaint->delete();
        });
    }

    public function updateStatus(string $uuid, string $status): FuelStationComplaint
    {
        return DB::transaction(function () use ($uuid, $status) {
            $complaint = FuelStationComplaint::where('uuid', $uuid)->lockForUpdate()->firstOrFail();
            $complaint->update(['status' => $status]);
            return $complaint->fresh()->load('fuelStation');
        });
    }
}
