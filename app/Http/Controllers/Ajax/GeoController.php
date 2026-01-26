<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GeoController extends Controller
{
    public function governorates($regionUuid)
    {
        return DB::table('governorates')
            ->where('region_uuid', $regionUuid)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['uuid', 'name']);
    }

    public function centers($governorateUuid)
    {
        return DB::table('centers')
            ->where('governorate_uuid', $governorateUuid)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['uuid', 'name']);
    }

    public function cities($centerUuid)
    {
        return DB::table('cities')
            ->where('center_uuid', $centerUuid)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['uuid', 'name']);
    }
}
