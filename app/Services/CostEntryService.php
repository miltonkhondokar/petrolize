<?php

namespace App\Services;

use App\Models\CostEntry;
use Illuminate\Support\Facades\DB;

class CostEntryService
{
    public function paginate(array $filters, int $perPage = 20)
    {
        $q = CostEntry::with(['fuelStation', 'category'])
            ->latest();

        if (!empty($filters['fuel_station_uuid'])) {
            $q->where('fuel_station_uuid', $filters['fuel_station_uuid']);
        }
        if (!empty($filters['cost_category_uuid'])) {
            $q->where('cost_category_uuid', $filters['cost_category_uuid']);
        }
        if (array_key_exists('is_active', $filters) && $filters['is_active'] !== '' && $filters['is_active'] !== null) {
            $q->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $filters['is_active']);
        }
        if (!empty($filters['expense_date'])) {
            $q->whereDate('expense_date', $filters['expense_date']);
        }
        if (!empty($filters['from'])) {
            $q->whereDate('expense_date', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $q->whereDate('expense_date', '<=', $filters['to']);
        }

        return $q->paginate($perPage)->withQueryString();
    }

    public function findOrFail(string $uuid): CostEntry
    {
        return CostEntry::with(['fuelStation', 'category'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function create(array $payload): CostEntry
    {
        return DB::transaction(function () use ($payload) {
            $entry = CostEntry::create($payload);
            return $entry->fresh()->load(['fuelStation', 'category']);
        });
    }

    public function update(string $uuid, array $payload): CostEntry
    {
        return DB::transaction(function () use ($uuid, $payload) {
            $entry = CostEntry::where('uuid', $uuid)->lockForUpdate()->firstOrFail();
            $entry->update($payload);
            return $entry->fresh()->load(['fuelStation', 'category']);
        });
    }

    public function delete(string $uuid): void
    {
        DB::transaction(function () use ($uuid) {
            $entry = CostEntry::where('uuid', $uuid)->lockForUpdate()->firstOrFail();
            $entry->delete();
        });
    }
}
