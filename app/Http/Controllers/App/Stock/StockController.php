<?php

namespace App\Http\Controllers\App\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'region_uuid'        => ['nullable', 'uuid'],
            'governorate_uuid'   => ['nullable', 'uuid'],
            'center_uuid'        => ['nullable', 'uuid'],
            'city_uuid'          => ['nullable', 'uuid'],
            'fuel_station_uuid'  => ['nullable', 'uuid'],
            'fuel_type_uuid'     => ['nullable', 'uuid'],
        ]);

        $asOfDate = now()->toDateString();

        $breadcrumb = [
            "page_header" => "Stock / Ledger",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Stock",
            "second_item_link" => route('stock.index'),
            "second_item_icon" => "fa-boxes-stacked",
        ];

        /**
         * 1) Filtered ledger base
         */
        $filteredLedger = DB::table('fuel_stock_ledgers as l')
            ->join('fuel_stations as fs', 'fs.uuid', '=', 'l.fuel_station_uuid')
            ->whereDate('l.txn_date', '<=', $asOfDate);

        // Geo filters
        if (!empty($filters['region_uuid'])) {
            $filteredLedger->where('fs.region_uuid', $filters['region_uuid']);
        }
        if (!empty($filters['governorate_uuid'])) {
            $filteredLedger->where('fs.governorate_uuid', $filters['governorate_uuid']);
        }
        if (!empty($filters['center_uuid'])) {
            $filteredLedger->where('fs.center_uuid', $filters['center_uuid']);
        }
        if (!empty($filters['city_uuid'])) {
            $filteredLedger->where('fs.city_uuid', $filters['city_uuid']);
        }

        // Station/fuel filters
        if (!empty($filters['fuel_station_uuid'])) {
            $filteredLedger->where('l.fuel_station_uuid', $filters['fuel_station_uuid']);
        }
        if (!empty($filters['fuel_type_uuid'])) {
            $filteredLedger->where('l.fuel_type_uuid', $filters['fuel_type_uuid']);
        }

        /**
         * Latest ledger id per (station, fuel)
         */
        $latestLedgerIdsSub = $filteredLedger
            ->selectRaw('MAX(l.id) as last_id, l.fuel_station_uuid, l.fuel_type_uuid')
            ->groupBy('l.fuel_station_uuid', 'l.fuel_type_uuid');

        /**
         * 2) Latest purchase item per (station, fuel) where received_qty > 0
         * This matches your DB (status = received) and avoids using station_prices.
         */
        $latestPurchaseItemIdsSub = DB::table('fuel_purchase_items as pi')
            ->join('fuel_purchases as fp', 'fp.uuid', '=', 'pi.fuel_purchase_uuid')
            ->where('pi.is_active', 1)
            ->where('pi.received_qty', '>', 0)
            ->selectRaw('MAX(pi.id) as last_item_id, fp.fuel_station_uuid, pi.fuel_type_uuid')
            ->groupBy('fp.fuel_station_uuid', 'pi.fuel_type_uuid');

        /**
         * 3) Main stock query
         */
        $stockRowsQuery = DB::table('fuel_stock_ledgers as l')
            ->joinSub($latestLedgerIdsSub, 'x', function ($join) {
                $join->on('x.fuel_station_uuid', '=', 'l.fuel_station_uuid')
                    ->on('x.fuel_type_uuid', '=', 'l.fuel_type_uuid')
                    ->on('x.last_id', '=', 'l.id');
            })
            ->join('fuel_stations as fs', 'fs.uuid', '=', 'l.fuel_station_uuid')
            ->join('fuel_types as ft', 'ft.uuid', '=', 'l.fuel_type_uuid')
            ->leftJoin('fuel_units as fu', 'fu.uuid', '=', 'l.fuel_unit_uuid')

            // location joins
            ->leftJoin('regions as r', 'r.uuid', '=', 'fs.region_uuid')
            ->leftJoin('governorates as g', 'g.uuid', '=', 'fs.governorate_uuid')
            ->leftJoin('centers as c', 'c.uuid', '=', 'fs.center_uuid')
            ->leftJoin('cities as ci', 'ci.uuid', '=', 'fs.city_uuid')

            // latest received purchase item
            ->leftJoinSub($latestPurchaseItemIdsSub, 'lp', function ($join) {
                $join->on('lp.fuel_station_uuid', '=', 'l.fuel_station_uuid')
                    ->on('lp.fuel_type_uuid', '=', 'l.fuel_type_uuid');
            })
            ->leftJoin('fuel_purchase_items as last_pi', 'last_pi.id', '=', 'lp.last_item_id')

            ->select([
                'l.uuid',
                'l.fuel_station_uuid',
                'l.fuel_type_uuid',
                'l.txn_date',
                DB::raw('COALESCE(l.balance_after, 0) as current_stock'),

                'fs.name as station_name',
                'fs.location as station_location',

                'ft.name as fuel_name',
                'ft.code as fuel_code',

                'fu.abbreviation as unit_abbr',

                'r.name as region_name',
                'g.name as governorate_name',
                'c.name as center_name',
                'ci.name as city_name',

                DB::raw('COALESCE(last_pi.unit_price, 0) as unit_price'),
                DB::raw('(COALESCE(l.balance_after,0) * COALESCE(last_pi.unit_price,0)) as line_total'),

                // ✅ FIX: Center must come only from centers/cities tables (NO fs.location)
                DB::raw("COALESCE(c.name, ci.name, 'N/A') as center_label"),
            ])
            ->orderBy('r.name')
            ->orderBy('g.name')
            ->orderBy('c.name')
            ->orderBy('fs.name')
            ->orderBy('ft.name');

        $stockRows = $stockRowsQuery
            ->paginate(25)
            ->appends($filters);

        $pageGrandTotal = $stockRows->getCollection()->sum('line_total');

        // Dropdown data
        $regions      = DB::table('regions')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name']);
        $governorates = DB::table('governorates')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name', 'region_uuid']);
        $centers      = DB::table('centers')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name', 'governorate_uuid']);
        $cities       = DB::table('cities')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name', 'center_uuid']);

        $fuelStations = DB::table('fuel_stations')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name']);
        $fuelTypes    = DB::table('fuel_types')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name', 'code']);

        return view('application.pages.app.stock.index', compact(
            'breadcrumb',
            'filters',
            'asOfDate',
            'stockRows',
            'pageGrandTotal',
            'regions',
            'governorates',
            'centers',
            'cities',
            'fuelStations',
            'fuelTypes'
        ));
    }
}
