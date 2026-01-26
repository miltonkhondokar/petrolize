<?php

namespace App\Http\Controllers\App\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\FuelStation;
use App\Models\FuelType;
use App\Models\CostEntry;
use App\Models\FuelStationComplaint;
use App\Models\FuelSalesDay;
use App\Models\FuelPurchase;
use App\Models\FuelStockLedger;
use App\Models\VendorPaymentAllocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // =========================
        // SUMMARY
        // =========================
        $totalPumps    = FuelStation::count();
        $activePumps   = FuelStation::where('is_active', true)->count();
        $inactivePumps = FuelStation::where('is_active', false)->count();

        $totalFuelTypes    = FuelType::count();
        $activeFuelTypes   = FuelType::where('is_active', true)->count();
        $inactiveFuelTypes = $totalFuelTypes - $activeFuelTypes;

        // Today's Expenses
        $todayExpenses = (float) CostEntry::whereDate('expense_date', today())
            ->where('is_active', true)
            ->sum('amount');

        $todayExpenseCount = (int) CostEntry::whereDate('expense_date', today())
            ->where('is_active', true)
            ->count();

        // Complaints
        $openComplaints = (int) FuelStationComplaint::where('status', 'open')
            ->where('is_active', true)
            ->count();

        $resolvedComplaints = (int) FuelStationComplaint::where('status', 'resolved')
            ->where('is_active', true)
            ->count();

        // =========================
        // CHARTS
        // =========================
        $monthlyExpensesChart      = $this->getMonthlyExpensesChart();   // last 6 months
        $stockValueChart           = $this->getStockValueByPumpChart();  // top 10 stations by CURRENT stock value (LEDGER balance * latest cost)
        $monthlyExpensesByCategory = $this->getMonthlyExpensesByCategory();
        $fuelTypeStocks            = $this->getFuelTypeStocks();         // CURRENT stock distribution by fuel type (LEDGER balance)
        $pumpPerformance           = $this->getPumpPerformance();        // top 5 stations by CURRENT stock value + complaints

        $fuelEfficiency = $this->getFuelEfficiencyReport();
        $salesVsTarget = $this->getSalesVsTargetReport();
        $vendorPerformance = $this->getVendorPerformanceReport();
        $stationProfitability = $this->getStationProfitabilityReport();
        $fuelPriceTrends = $this->getFuelPriceTrendReport();
        // =========================
        // RECENT LISTS
        // IMPORTANT:
        // You deleted fuel_station_stocks table, so we can't use FuelStationStock model anymore.
        // We'll show recent "received" purchases instead (still meaningful for dashboard).
        // =========================
        $recentStocks = FuelPurchase::with(['fuelStation', 'vendor'])
            ->whereIn('status', ['received', 'received_full', 'received_partial'])
            ->orderBy('purchase_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentComplaints = FuelStationComplaint::with('fuelStation')
            ->where('is_active', true)
            ->orderBy('complaint_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $fuelTypes = FuelType::where('is_active', true)
            ->select('name', 'code', 'uuid')
            ->get();

        // ✅ Total CURRENT stock value from ledger balance * latest known cost (does not modify any data)
        $totalStockValue = (float) $this->getCurrentTotalStockValue();

        // =========================
        // SALES + STOCK INTELLIGENCE
        // =========================
        [$todaySalesAmount, $todaySoldLiters] = $this->getTodaySalesKpis();
        [$mtdSalesAmount, $mtdSoldLiters]     = $this->getMonthToDateSalesKpis();

        $salesTrend14Days  = $this->getSalesTrend14Days();        // amount + liters
        $stockMoveTrend14  = $this->getStockMoveTrend14Days();    // qty_in + qty_out

        $topFuelsMtd       = $this->getTopFuelsMtd();             // liters + revenue
        $topStationsMtd    = $this->getTopStationsMtd();          // revenue

        $vendorPayables    = $this->getVendorPayablesSummary();   // total_purchases, paid, due
        $lowStockAlerts    = $this->getLowStockAlerts(5);         // threshold liters

        // OPTIONAL: Profit snapshot (simple)
        $profitSnapshot = $this->getMtdProfitSnapshot();

        // =========================
        // NEW: CAPACITY REPORTS (READ-ONLY)
        // =========================
        $capacityUtilizationTop = $this->getCapacityUtilizationTop(10);     // top 10 by utilization
        $missingCapacityConfigs = $this->getMissingCapacityConfigs(10);     // top 10 missing configs
        $recentCapacityChanges  = $this->getRecentCapacityChanges(30, 10); // last 30 days changes

        return view('application.pages.app.dashboard.dashboard', compact(
            'totalPumps',
            'activePumps',
            'inactivePumps',
            'totalFuelTypes',
            'activeFuelTypes',
            'inactiveFuelTypes',
            'todayExpenses',
            'todayExpenseCount',
            'openComplaints',
            'resolvedComplaints',
            'monthlyExpensesChart',
            'stockValueChart',
            'recentStocks',
            'recentComplaints',
            'monthlyExpensesByCategory',
            'fuelTypeStocks',
            'pumpPerformance',
            'fuelTypes',
            'totalStockValue',

            // EXISTING
            'todaySalesAmount',
            'todaySoldLiters',
            'mtdSalesAmount',
            'mtdSoldLiters',
            'salesTrend14Days',
            'stockMoveTrend14',
            'topFuelsMtd',
            'topStationsMtd',
            'vendorPayables',
            'lowStockAlerts',
            'profitSnapshot',

            // NEW
            'capacityUtilizationTop',
            'missingCapacityConfigs',
            'recentCapacityChanges',
            'fuelEfficiency',
            'salesVsTarget',
            'vendorPerformance',
            'stationProfitability',
            'fuelPriceTrends'
        ));
    }

    // =========================================================
    // LEDGER HELPERS (read-only)
    // =========================================================

    /**
     * Latest ledger row per (station,fuel) -> gives us current balance_after.
     */
    private function latestLedgerIdsSubquery()
    {
        return DB::table('fuel_stock_ledgers')
            ->selectRaw('MAX(id) as id')
            ->groupBy('fuel_station_uuid', 'fuel_type_uuid');
    }

    /**
     * Latest cost per (station,fuel) from RECEIVED purchases.
     * We use fuel_purchase_items.unit_price as "cost".
     */
    private function latestCostIdsSubquery()
    {
        return DB::table('fuel_purchase_items as i')
            ->join('fuel_purchases as p', 'i.fuel_purchase_uuid', '=', 'p.uuid')
            ->where('i.is_active', true)
            ->whereIn('p.status', ['received', 'received_full', 'received_partial'])
            ->selectRaw('p.fuel_station_uuid, i.fuel_type_uuid, MAX(i.id) as id')
            ->groupBy('p.fuel_station_uuid', 'i.fuel_type_uuid');
    }

    /**
     * Total CURRENT stock value = SUM(ledger.balance_after * latest_cost.unit_price)
     */
    private function getCurrentTotalStockValue(): float
    {
        $latestLedgerIds = $this->latestLedgerIdsSubquery();
        $latestCostIds   = $this->latestCostIdsSubquery();

        $row = DB::table('fuel_stock_ledgers as l')
            ->joinSub($latestLedgerIds, 'lx', fn ($j) => $j->on('l.id', '=', 'lx.id'))
            ->leftJoinSub($latestCostIds, 'cx', function ($j) {
                $j->on('l.fuel_station_uuid', '=', 'cx.fuel_station_uuid')
                    ->on('l.fuel_type_uuid', '=', 'cx.fuel_type_uuid');
            })
            ->leftJoin('fuel_purchase_items as pi', 'pi.id', '=', 'cx.id')
            ->join('fuel_stations as s', 'l.fuel_station_uuid', '=', 's.uuid')
            ->where('s.is_active', true)
            ->selectRaw('SUM( (l.balance_after) * COALESCE(pi.unit_price, 0) ) as total_value')
            ->first();

        return (float) ($row->total_value ?? 0);
    }

    // =========================================================
    // Existing chart: last 6 months expenses
    // =========================================================
    private function getMonthlyExpensesChart()
    {
        $startDate = Carbon::now()->subMonths(5)->startOfMonth();
        $endDate   = Carbon::now()->endOfMonth();

        $expenses = CostEntry::where('is_active', true)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = [];
        $data   = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $monthKey = $current->format('Y-m');
            $labels[] = $current->format('M Y');
            $data[]   = isset($expenses[$monthKey]) ? (float)$expenses[$monthKey]->total : 0;
            $current->addMonth();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    // =========================================================
    // UPDATED: top 10 stations by CURRENT stock value
    // =========================================================
    private function getStockValueByPumpChart()
    {
        $latestLedgerIds = $this->latestLedgerIdsSubquery();
        $latestCostIds   = $this->latestCostIdsSubquery();

        $rows = DB::table('fuel_stock_ledgers as l')
            ->joinSub($latestLedgerIds, 'lx', fn ($j) => $j->on('l.id', '=', 'lx.id'))
            ->join('fuel_stations as p', 'l.fuel_station_uuid', '=', 'p.uuid')
            ->leftJoinSub($latestCostIds, 'cx', function ($j) {
                $j->on('l.fuel_station_uuid', '=', 'cx.fuel_station_uuid')
                    ->on('l.fuel_type_uuid', '=', 'cx.fuel_type_uuid');
            })
            ->leftJoin('fuel_purchase_items as pi', 'pi.id', '=', 'cx.id')
            ->where('p.is_active', true)
            ->selectRaw('p.name as pump_name, SUM( (l.balance_after) * COALESCE(pi.unit_price, 0) ) as total_value')
            ->groupBy('p.uuid', 'p.name')
            ->orderByDesc('total_value')
            ->limit(10)
            ->get();

        return [
            'labels' => $rows->pluck('pump_name')->toArray(),
            'data'   => $rows->pluck('total_value')->map(fn ($v) => (float)$v)->toArray(),
        ];
    }

    // =========================================================
    // Existing: current month expenses by category
    // =========================================================
    private function getMonthlyExpensesByCategory()
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();

        return DB::table('cost_entries as ce')
            ->leftJoin('cost_categories as cc', 'ce.cost_category_uuid', '=', 'cc.uuid')
            ->where('ce.is_active', true)
            ->whereBetween('ce.expense_date', [$start, $end])
            ->selectRaw('cc.name as category_name, cc.description, COUNT(ce.id) as count, SUM(ce.amount) as total')
            ->groupBy('cc.uuid', 'cc.name', 'cc.description')
            ->orderBy('total', 'desc')
            ->get();
    }

    // =========================================================
    // UPDATED: CURRENT stock distribution by fuel type (ledger truth)
    // =========================================================
    private function getFuelTypeStocks()
    {
        $latestLedgerIds = $this->latestLedgerIdsSubquery();
        $latestCostIds   = $this->latestCostIdsSubquery();

        return DB::table('fuel_stock_ledgers as l')
            ->joinSub($latestLedgerIds, 'lx', fn ($j) => $j->on('l.id', '=', 'lx.id'))
            ->join('fuel_types as ft', 'l.fuel_type_uuid', '=', 'ft.uuid')
            ->join('fuel_stations as s', 'l.fuel_station_uuid', '=', 's.uuid')
            ->leftJoinSub($latestCostIds, 'cx', function ($j) {
                $j->on('l.fuel_station_uuid', '=', 'cx.fuel_station_uuid')
                    ->on('l.fuel_type_uuid', '=', 'cx.fuel_type_uuid');
            })
            ->leftJoin('fuel_purchase_items as pi', 'pi.id', '=', 'cx.id')
            ->where('s.is_active', true)
            ->where('ft.is_active', true)
            ->selectRaw('
                ft.name as fuel_name,
                ft.code as fuel_code,
                SUM(l.balance_after) as total_quantity,
                SUM( (l.balance_after) * COALESCE(pi.unit_price, 0) ) as total_value
            ')
            ->groupBy('ft.uuid', 'ft.name', 'ft.code')
            ->orderByDesc('total_quantity')
            ->get();
    }

    // =========================================================
    // UPDATED: top 5 stations by CURRENT stock value + complaints
    // =========================================================
    private function getPumpPerformance()
    {
        $latestLedgerIds = $this->latestLedgerIdsSubquery();
        $latestCostIds   = $this->latestCostIdsSubquery();

        $stationValues = DB::table('fuel_stock_ledgers as l')
            ->joinSub($latestLedgerIds, 'lx', fn ($j) => $j->on('l.id', '=', 'lx.id'))
            ->leftJoinSub($latestCostIds, 'cx', function ($j) {
                $j->on('l.fuel_station_uuid', '=', 'cx.fuel_station_uuid')
                    ->on('l.fuel_type_uuid', '=', 'cx.fuel_type_uuid');
            })
            ->leftJoin('fuel_purchase_items as pi', 'pi.id', '=', 'cx.id')
            ->join('fuel_stations as s', 'l.fuel_station_uuid', '=', 's.uuid')
            ->where('s.is_active', true)
            ->selectRaw('s.uuid as station_uuid, SUM( (l.balance_after) * COALESCE(pi.unit_price, 0) ) as stock_value')
            ->groupBy('s.uuid')
            ->pluck('stock_value', 'station_uuid')
            ->toArray();

        $fuelStations = FuelStation::where('is_active', true)
            ->select('uuid', 'name', 'location', 'is_active')
            ->withCount(['complaints' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->map(function ($fuelStation) use ($stationValues) {
                $stockValue = (float) ($stationValues[$fuelStation->uuid] ?? 0);

                return (object)[
                    'uuid'            => $fuelStation->uuid,
                    'name'            => $fuelStation->name,
                    'location'        => $fuelStation->location,
                    'complaint_count' => $fuelStation->complaints_count,
                    'stock_value'     => $stockValue,
                    'is_active'       => (bool)$fuelStation->is_active,
                ];
            })
            ->sortByDesc('stock_value')
            ->take(5);

        return $fuelStations;
    }

    // =========================================================
    // Sales KPIs
    // =========================================================
    private function getTodaySalesKpis(): array
    {
        $today = Carbon::today();

        $amount = (float) FuelSalesDay::whereDate('sale_date', $today)
            ->whereIn('status', ['submitted', 'approved'])
            ->sum('total_amount');

        $liters = (float) DB::table('fuel_sales_items as i')
            ->join('fuel_sales_days as d', 'i.fuel_sales_day_uuid', '=', 'd.uuid')
            ->whereDate('d.sale_date', $today)
            ->whereIn('d.status', ['submitted', 'approved'])
            ->sum('i.sold_qty');

        return [$amount, $liters];
    }

    private function getMonthToDateSalesKpis(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfDay();

        $amount = (float) FuelSalesDay::whereBetween('sale_date', [$start, $end])
            ->whereIn('status', ['submitted', 'approved'])
            ->sum('total_amount');

        $liters = (float) DB::table('fuel_sales_items as i')
            ->join('fuel_sales_days as d', 'i.fuel_sales_day_uuid', '=', 'd.uuid')
            ->whereBetween('d.sale_date', [$start, $end])
            ->whereIn('d.status', ['submitted', 'approved'])
            ->sum('i.sold_qty');

        return [$amount, $liters];
    }

    // =========================================================
    // Sales Trend (last 14 days)
    // =========================================================
    private function getSalesTrend14Days(): array
    {
        $start = Carbon::today()->subDays(13);
        $end   = Carbon::today();

        $amountMap = FuelSalesDay::whereBetween('sale_date', [$start, $end])
            ->whereIn('status', ['submitted', 'approved'])
            ->selectRaw('DATE(sale_date) as d, SUM(total_amount) as total')
            ->groupBy('d')
            ->pluck('total', 'd')
            ->toArray();

        $literMap = DB::table('fuel_sales_items as i')
            ->join('fuel_sales_days as d', 'i.fuel_sales_day_uuid', '=', 'd.uuid')
            ->whereBetween('d.sale_date', [$start, $end])
            ->whereIn('d.status', ['submitted', 'approved'])
            ->selectRaw('DATE(d.sale_date) as dd, SUM(i.sold_qty) as liters')
            ->groupBy('dd')
            ->pluck('liters', 'dd')
            ->toArray();

        $labels = [];
        $amount = [];
        $liters = [];

        $cur = $start->copy();
        while ($cur <= $end) {
            $k = $cur->format('Y-m-d');
            $labels[] = $cur->format('d M');
            $amount[] = isset($amountMap[$k]) ? (float)$amountMap[$k] : 0;
            $liters[] = isset($literMap[$k]) ? (float)$literMap[$k] : 0;
            $cur->addDay();
        }

        return compact('labels', 'amount', 'liters');
    }

    // =========================================================
    // Stock Move Trend (last 14 days) from ledger
    // =========================================================
    private function getStockMoveTrend14Days(): array
    {
        $start = Carbon::today()->subDays(13);
        $end   = Carbon::today()->endOfDay();

        $inMap = FuelStockLedger::whereBetween('txn_date', [$start, $end])
            ->selectRaw('DATE(txn_date) as d, SUM(qty_in) as qty_in')
            ->groupBy('d')
            ->pluck('qty_in', 'd')
            ->toArray();

        $outMap = FuelStockLedger::whereBetween('txn_date', [$start, $end])
            ->selectRaw('DATE(txn_date) as d, SUM(qty_out) as qty_out')
            ->groupBy('d')
            ->pluck('qty_out', 'd')
            ->toArray();

        $labels = [];
        $qtyIn  = [];
        $qtyOut = [];

        $cur = $start->copy();
        while ($cur->startOfDay() <= $end) {
            $k = $cur->format('Y-m-d');
            $labels[] = $cur->format('d M');
            $qtyIn[]  = isset($inMap[$k]) ? (float)$inMap[$k] : 0;
            $qtyOut[] = isset($outMap[$k]) ? (float)$outMap[$k] : 0;
            $cur->addDay();
        }

        return compact('labels', 'qtyIn', 'qtyOut');
    }

    // =========================================================
    // Top fuels MTD
    // =========================================================
    private function getTopFuelsMtd()
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfDay();

        return DB::table('fuel_sales_items as i')
            ->join('fuel_sales_days as d', 'i.fuel_sales_day_uuid', '=', 'd.uuid')
            ->join('fuel_types as ft', 'i.fuel_type_uuid', '=', 'ft.uuid')
            ->whereBetween('d.sale_date', [$start, $end])
            ->whereIn('d.status', ['submitted', 'approved'])
            ->selectRaw('ft.name as fuel_name, ft.code as fuel_code, SUM(i.sold_qty) as liters, SUM(i.line_total) as revenue')
            ->groupBy('ft.uuid', 'ft.name', 'ft.code')
            ->orderByDesc('liters')
            ->limit(6)
            ->get();
    }

    // =========================================================
    // Top stations MTD
    // =========================================================
    private function getTopStationsMtd()
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfDay();

        return DB::table('fuel_sales_days as d')
            ->join('fuel_stations as s', 'd.fuel_station_uuid', '=', 's.uuid')
            ->whereBetween('d.sale_date', [$start, $end])
            ->whereIn('d.status', ['submitted', 'approved'])
            ->selectRaw('s.name as station_name, SUM(d.total_amount) as revenue')
            ->groupBy('s.uuid', 's.name')
            ->orderByDesc('revenue')
            ->limit(6)
            ->get();
    }

    // =========================================================
    // Vendor payable summary
    // =========================================================
    private function getVendorPayablesSummary(): array
    {
        $totalPurchases = (float) FuelPurchase::whereIn('status', ['received', 'received_full', 'received_partial'])
            ->sum('total_amount');

        $paid = (float) VendorPaymentAllocation::sum('allocated_amount');

        return [
            'total_purchases' => $totalPurchases,
            'paid' => $paid,
            'due' => max(0, $totalPurchases - $paid),
        ];
    }

    // =========================================================
    // Low stock alerts based on latest ledger balance
    // =========================================================
    private function getLowStockAlerts(float $thresholdLiters = 5.0)
    {
        $latestIds = DB::table('fuel_stock_ledgers')
            ->selectRaw('MAX(id) as id')
            ->groupBy('fuel_station_uuid', 'fuel_type_uuid');

        return DB::table('fuel_stock_ledgers as l')
            ->joinSub($latestIds, 'x', function ($j) {
                $j->on('l.id', '=', 'x.id');
            })
            ->join('fuel_stations as s', 'l.fuel_station_uuid', '=', 's.uuid')
            ->join('fuel_types as ft', 'l.fuel_type_uuid', '=', 'ft.uuid')
            ->where('s.is_active', true)
            ->where('ft.is_active', true)
            ->where('l.balance_after', '<=', $thresholdLiters)
            ->orderBy('l.balance_after', 'asc')
            ->limit(10)
            ->get([
                's.name as station_name',
                'ft.name as fuel_name',
                'ft.code as fuel_code',
                'l.balance_after',
            ]);
    }

    // =========================================================
    // Simple MTD profit snapshot
    // Profit = Sales - Purchases - Expenses (MTD)
    // =========================================================
    private function getMtdProfitSnapshot(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfDay();

        $sales = (float) FuelSalesDay::whereBetween('sale_date', [$start, $end])
            ->whereIn('status', ['submitted', 'approved'])
            ->sum('total_amount');

        $purchases = (float) FuelPurchase::whereBetween('purchase_date', [$start, $end])
            ->whereIn('status', ['received', 'received_full', 'received_partial'])
            ->sum('total_amount');

        $expenses = (float) CostEntry::whereBetween('expense_date', [$start, $end])
            ->where('is_active', true)
            ->sum('amount');

        return [
            'sales' => $sales,
            'purchases' => $purchases,
            'expenses' => $expenses,
            'profit' => ($sales - $purchases - $expenses),
        ];
    }

    // =========================================================
    // NEW: CAPACITY REPORTS (READ-ONLY, NO EXISTING LOGIC CHANGED)
    // =========================================================

    /**
     * Latest ACTIVE capacity effective_from per (station,fuel).
     * NOTE: does not filter by <= today (keeps your data logic untouched).
     */
    private function latestCapacityEffectiveSubquery()
    {
        return DB::table('fuel_station_capacity_logs')
            ->where('is_active', true)
            ->selectRaw('fuel_station_uuid, fuel_type_uuid, MAX(effective_from) as max_eff')
            ->groupBy('fuel_station_uuid', 'fuel_type_uuid');
    }

    /**
     * Top station+fuel by utilization: (current stock / capacity) * 100
     */
    private function getCapacityUtilizationTop(int $limit = 10)
    {
        $latestLedgerIds = $this->latestLedgerIdsSubquery();
        $latestCapEff    = $this->latestCapacityEffectiveSubquery();

        return DB::table('fuel_stock_ledgers as l')
            ->joinSub($latestLedgerIds, 'lx', fn ($j) => $j->on('l.id', '=', 'lx.id'))
            ->join('fuel_stations as s', 'l.fuel_station_uuid', '=', 's.uuid')
            ->join('fuel_types as ft', 'l.fuel_type_uuid', '=', 'ft.uuid')
            ->leftJoinSub($latestCapEff, 'cx', function ($j) {
                $j->on('l.fuel_station_uuid', '=', 'cx.fuel_station_uuid')
                    ->on('l.fuel_type_uuid', '=', 'cx.fuel_type_uuid');
            })
            ->leftJoin('fuel_station_capacity_logs as c', function ($j) {
                $j->on('c.fuel_station_uuid', '=', 'cx.fuel_station_uuid')
                    ->on('c.fuel_type_uuid', '=', 'cx.fuel_type_uuid')
                    ->on('c.effective_from', '=', 'cx.max_eff')
                    ->where('c.is_active', '=', 1);
            })
            ->where('s.is_active', true)
            ->where('ft.is_active', true)
            ->selectRaw('
                s.name as station_name,
                s.location,
                ft.name as fuel_name,
                ft.code as fuel_code,
                l.balance_after as current_stock,
                COALESCE(c.capacity_liters, 0) as capacity_liters,
                CASE WHEN COALESCE(c.capacity_liters, 0) > 0
                    THEN (l.balance_after / c.capacity_liters) * 100
                    ELSE NULL
                END as utilization_percent,
                c.effective_from as capacity_effective_from
            ')
            ->orderByDesc(DB::raw('utilization_percent'))
            ->limit($limit)
            ->get();
    }

    /**
     * Station+fuel combos with ledger but no capacity config.
     */
    private function getMissingCapacityConfigs(int $limit = 10)
    {
        $latestLedgerIds = $this->latestLedgerIdsSubquery();
        $latestCapEff    = $this->latestCapacityEffectiveSubquery();

        return DB::table('fuel_stock_ledgers as l')
            ->joinSub($latestLedgerIds, 'lx', fn ($j) => $j->on('l.id', '=', 'lx.id'))
            ->join('fuel_stations as s', 'l.fuel_station_uuid', '=', 's.uuid')
            ->join('fuel_types as ft', 'l.fuel_type_uuid', '=', 'ft.uuid')
            ->leftJoinSub($latestCapEff, 'cx', function ($j) {
                $j->on('l.fuel_station_uuid', '=', 'cx.fuel_station_uuid')
                    ->on('l.fuel_type_uuid', '=', 'cx.fuel_type_uuid');
            })
            ->where('s.is_active', true)
            ->where('ft.is_active', true)
            ->whereNull('cx.max_eff')
            ->select([
                's.name as station_name',
                's.location',
                'ft.name as fuel_name',
                'ft.code as fuel_code',
                'l.balance_after as current_stock',
            ])
            ->orderByDesc('l.balance_after')
            ->limit($limit)
            ->get();
    }

    /**
     * Recent capacity changes (last N days), latest entries.
     */
    private function getRecentCapacityChanges(int $days = 30, int $limit = 10)
    {
        $start = Carbon::today()->subDays($days);

        return DB::table('fuel_station_capacity_logs as c')
            ->join('fuel_stations as s', 'c.fuel_station_uuid', '=', 's.uuid')
            ->join('fuel_types as ft', 'c.fuel_type_uuid', '=', 'ft.uuid')
            ->whereDate('c.effective_from', '>=', $start)
            ->selectRaw('
                c.uuid,
                c.effective_from,
                c.capacity_liters,
                c.is_active,
                c.note,
                s.name as station_name,
                s.location,
                ft.name as fuel_name,
                ft.code as fuel_code
            ')
            ->orderByDesc('c.effective_from')
            ->orderByDesc('c.id')
            ->limit($limit)
            ->get();
    }

    // =========================================================
    // JSON endpoint (kept)
    // =========================================================
    public function getDashboardStats()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        $totalPumps = FuelStation::count();
        $activePumps = FuelStation::where('is_active', true)->count();
        $inactivePumps = FuelStation::where('is_active', false)->count();

        $totalFuelTypes = FuelType::count();
        $activeFuelTypes = FuelType::where('is_active', true)->count();
        $inactiveFuelTypes = $totalFuelTypes - $activeFuelTypes;

        $todayExpenses = (float) CostEntry::whereDate('expense_date', today())
            ->where('is_active', true)
            ->sum('amount');

        $todayExpenseCount = (int) CostEntry::whereDate('expense_date', today())
            ->where('is_active', true)
            ->count();

        $monthlyExpenses = (float) CostEntry::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->where('is_active', true)
            ->sum('amount');

        $openComplaints = (int) FuelStationComplaint::where('status', 'open')
            ->where('is_active', true)
            ->count();

        $resolvedComplaints = (int) FuelStationComplaint::where('status', 'resolved')
            ->where('is_active', true)
            ->count();

        $totalStockValue = (float) $this->getCurrentTotalStockValue();

        return response()->json([
            'success' => true,
            'data' => compact(
                'totalPumps',
                'activePumps',
                'inactivePumps',
                'totalFuelTypes',
                'activeFuelTypes',
                'inactiveFuelTypes',
                'todayExpenses',
                'todayExpenseCount',
                'monthlyExpenses',
                'openComplaints',
                'resolvedComplaints',
                'totalStockValue'
            )
        ]);
    }

    /**
     * Calculate fuel efficiency/loss MTD
     * Formula: (Opening Stock + Purchases) - (Sales + Closing Stock) = Variance (Loss/Gain)
     *
     * NOTE:
     * - Your fuel_stock_ledgers table does NOT have balance_before, so we use:
     *   Opening = balance_after from the FIRST ledger row in this month (per station+fuel)
     *   Closing = balance_after from the LATEST ledger row (per station+fuel)
     */
    private function getFuelEfficiencyReport()
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfDay();

        // ----------------------------
        // Opening balance (first ledger entry within this month)
        // ----------------------------
        $openingBalances = DB::table('fuel_stock_ledgers as l')
            ->join(DB::raw('(
            SELECT fuel_station_uuid, fuel_type_uuid, MIN(id) as first_id
            FROM fuel_stock_ledgers
            WHERE DATE(txn_date) >= "' . $start->format('Y-m-d') . '"
            GROUP BY fuel_station_uuid, fuel_type_uuid
        ) as first'), function ($j) {
                $j->on('l.id', '=', 'first.first_id');
            })
            ->selectRaw('
            l.fuel_station_uuid,
            l.fuel_type_uuid,
            l.balance_after as opening_qty
        ')
            ->get()
            ->groupBy(fn ($item) => $item->fuel_station_uuid . '-' . $item->fuel_type_uuid)
            ->map(fn ($g) => (float) $g->first()->opening_qty);

        // ----------------------------
        // Purchases MTD
        // ----------------------------
        $purchases = DB::table('fuel_purchase_items as i')
            ->join('fuel_purchases as p', 'i.fuel_purchase_uuid', '=', 'p.uuid')
            ->whereBetween('p.purchase_date', [$start, $end])
            ->whereIn('p.status', ['received', 'received_full', 'received_partial'])
            ->selectRaw('
            p.fuel_station_uuid,
            i.fuel_type_uuid,
            SUM(i.received_qty) as purchased_qty
        ')
            ->groupBy('p.fuel_station_uuid', 'i.fuel_type_uuid')
            ->get()
            ->groupBy(fn ($item) => $item->fuel_station_uuid . '-' . $item->fuel_type_uuid)
            ->map(fn ($g) => (float) $g->first()->purchased_qty);

        // ----------------------------
        // Sales MTD (liters)
        // ----------------------------
        $sales = DB::table('fuel_sales_items as i')
            ->join('fuel_sales_days as d', 'i.fuel_sales_day_uuid', '=', 'd.uuid')
            ->whereBetween('d.sale_date', [$start, $end])
            ->whereIn('d.status', ['submitted', 'approved'])
            ->selectRaw('
            d.fuel_station_uuid,
            i.fuel_type_uuid,
            SUM(i.sold_qty) as sold_qty
        ')
            ->groupBy('d.fuel_station_uuid', 'i.fuel_type_uuid')
            ->get()
            ->groupBy(fn ($item) => $item->fuel_station_uuid . '-' . $item->fuel_type_uuid)
            ->map(fn ($g) => (float) $g->first()->sold_qty);

        // ----------------------------
        // Closing balance (latest ledger entry overall)
        // ----------------------------
        $latestLedgerIds = $this->latestLedgerIdsSubquery();

        $closingBalances = DB::table('fuel_stock_ledgers as l')
            ->joinSub($latestLedgerIds, 'lx', fn ($j) => $j->on('l.id', '=', 'lx.id'))
            ->selectRaw('
            l.fuel_station_uuid,
            l.fuel_type_uuid,
            l.balance_after as closing_qty
        ')
            ->get()
            ->groupBy(fn ($item) => $item->fuel_station_uuid . '-' . $item->fuel_type_uuid)
            ->map(fn ($g) => (float) $g->first()->closing_qty);

        // ----------------------------
        // Compute variance per (station+fuel)
        // ----------------------------
        $efficiency = [];

        foreach ($closingBalances as $key => $closing) {
            [$stationUuid, $fuelUuid] = explode('-', $key);

            $opening   = (float) ($openingBalances[$key] ?? 0);
            $purchased = (float) ($purchases[$key] ?? 0);
            $sold      = (float) ($sales[$key] ?? 0);

            // Expected closing = Opening + Purchases - Sales
            $expectedClosing = $opening + $purchased - $sold;

            // Variance = Expected - Actual (positive = loss, negative = gain)
            $variance = $expectedClosing - (float) $closing;

            // % based on expected closing (avoid /0)
            $variancePercent = $expectedClosing > 0 ? ($variance / $expectedClosing) * 100 : 0;

            // Only show meaningful variance
            if (abs($variance) > 0.5) {
                $station = FuelStation::where('uuid', $stationUuid)->first(['name']);
                $fuel    = FuelType::where('uuid', $fuelUuid)->first(['name', 'code']);

                $efficiency[] = [
                    'station_name'     => $station->name ?? 'Unknown',
                    'fuel_name'        => $fuel->name ?? 'Unknown',
                    'fuel_code'        => $fuel->code ?? 'N/A',
                    'opening_qty'      => round($opening, 2),
                    'purchased_qty'    => round($purchased, 2),
                    'sold_qty'         => round($sold, 2),
                    'expected_closing' => round($expectedClosing, 2),
                    'actual_closing'   => round((float) $closing, 2),
                    'variance'         => round($variance, 2),
                    'variance_percent' => round($variancePercent, 2),
                    'status'           => $variance > 0.5 ? 'loss' : ($variance < -0.5 ? 'gain' : 'normal'),
                ];
            }
        }

        // Sort by variance (highest loss first)
        usort($efficiency, fn ($a, $b) => $b['variance'] <=> $a['variance']);

        return collect($efficiency)->take(10);
    }

    /**
     * Daily sales performance vs target (last 7 days)
     */
    private function getSalesVsTargetReport()
    {
        $start = Carbon::today()->subDays(6);
        $end = Carbon::today();

        $actualSales = FuelSalesDay::whereBetween('sale_date', [$start, $end])
            ->whereIn('status', ['submitted', 'approved'])
            ->selectRaw('DATE(sale_date) as sale_date, SUM(total_amount) as actual_amount')
            ->groupBy('sale_date')
            ->pluck('actual_amount', 'sale_date')
            ->toArray();

        // Assuming daily target of 500,000 (adjust as needed or fetch from targets table)
        $dailyTarget = 500000;

        $labels = [];
        $actual = [];
        $target = [];
        $achievement = [];

        $cur = $start->copy();
        while ($cur <= $end) {
            $dateKey = $cur->format('Y-m-d');
            $actualAmount = isset($actualSales[$dateKey]) ? (float)$actualSales[$dateKey] : 0;

            $labels[] = $cur->format('D, d M');
            $actual[] = $actualAmount;
            $target[] = $dailyTarget;
            $achievement[] = $dailyTarget > 0 ? round(($actualAmount / $dailyTarget) * 100, 1) : 0;

            $cur->addDay();
        }

        return compact('labels', 'actual', 'target', 'achievement');
    }


    /**
     * Vendor performance analysis (last 3 months)
     * FIX: removed v.company_name because column doesn't exist in vendors table
     */
    private function getVendorPerformanceReport()
    {
        $start = Carbon::now()->subMonths(3);

        return DB::table('fuel_purchases as p')
            ->join('vendors as v', 'p.vendor_uuid', '=', 'v.uuid')
            ->leftJoin('vendor_payment_allocations as vpa', 'p.uuid', '=', 'vpa.fuel_purchase_uuid')
            ->where('p.created_at', '>=', $start)
            ->selectRaw('
            v.uuid as vendor_uuid,
            v.name as vendor_name,
            COUNT(DISTINCT p.uuid) as total_orders,
            SUM(p.total_amount) as total_purchase_value,
            SUM(COALESCE(vpa.allocated_amount, 0)) as total_paid,
            (SUM(p.total_amount) - SUM(COALESCE(vpa.allocated_amount, 0))) as outstanding,
            AVG(DATEDIFF(p.updated_at, p.purchase_date)) as avg_fulfillment_days,
            SUM(CASE WHEN p.status IN ("received", "received_full") THEN 1 ELSE 0 END) as completed_orders,
            ROUND(
                (SUM(CASE WHEN p.status IN ("received", "received_full") THEN 1 ELSE 0 END) / NULLIF(COUNT(p.uuid),0)) * 100,
                1
            ) as completion_rate
        ')
            ->groupBy('v.uuid', 'v.name')
            ->orderByDesc('total_purchase_value')
            ->limit(10)
            ->get();
    }


    /**
     * Station-wise profitability (MTD)
     */
    private function getStationProfitabilityReport()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfDay();

        // Sales per station
        $salesByStation = FuelSalesDay::whereBetween('sale_date', [$start, $end])
            ->whereIn('status', ['submitted', 'approved'])
            ->selectRaw('fuel_station_uuid, SUM(total_amount) as sales')
            ->groupBy('fuel_station_uuid')
            ->pluck('sales', 'fuel_station_uuid');

        // Cost per station (purchases at cost price)
        $costsByStation = DB::table('fuel_purchase_items as i')
            ->join('fuel_purchases as p', 'i.fuel_purchase_uuid', '=', 'p.uuid')
            ->whereBetween('p.purchase_date', [$start, $end])
            ->whereIn('p.status', ['received', 'received_full', 'received_partial'])
            ->selectRaw('p.fuel_station_uuid, SUM(i.received_qty * i.unit_price) as cost')
            ->groupBy('p.fuel_station_uuid')
            ->pluck('cost', 'fuel_station_uuid');

        // Expenses per station
        $expensesByStation = CostEntry::whereBetween('expense_date', [$start, $end])
            ->where('is_active', true)
            ->selectRaw('fuel_station_uuid, SUM(amount) as expenses')
            ->groupBy('fuel_station_uuid')
            ->pluck('expenses', 'fuel_station_uuid');

        $stations = FuelStation::where('is_active', true)->get(['uuid', 'name', 'location']);

        $profitability = $stations->map(function ($station) use ($salesByStation, $costsByStation, $expensesByStation) {
            $sales = (float)($salesByStation[$station->uuid] ?? 0);
            $costs = (float)($costsByStation[$station->uuid] ?? 0);
            $expenses = (float)($expensesByStation[$station->uuid] ?? 0);

            $grossProfit = $sales - $costs;
            $netProfit = $grossProfit - $expenses;
            $profitMargin = $sales > 0 ? ($netProfit / $sales) * 100 : 0;

            return [
                'station_name' => $station->name,
                'location' => $station->location,
                'sales' => round($sales, 2),
                'costs' => round($costs, 2),
                'expenses' => round($expenses, 2),
                'gross_profit' => round($grossProfit, 2),
                'net_profit' => round($netProfit, 2),
                'profit_margin' => round($profitMargin, 2),
            ];
        })->sortByDesc('net_profit')->take(10);

        return $profitability;
    }

    /**
     * Fuel price trends (last 30 days)
     * FIX: fuel_sales_items has price_per_unit (NOT unit_price)
     */
    private function getFuelPriceTrendReport()
    {
        $start = Carbon::today()->subDays(29);
        $end   = Carbon::today();

        $fuelTypes = FuelType::where('is_active', true)->get(['uuid', 'name', 'code']);

        $result = [];

        foreach ($fuelTypes as $fuelType) {
            $prices = DB::table('fuel_sales_items as i')
                ->join('fuel_sales_days as d', 'i.fuel_sales_day_uuid', '=', 'd.uuid')
                ->where('i.fuel_type_uuid', $fuelType->uuid)
                ->whereBetween('d.sale_date', [$start, $end])
                ->whereIn('d.status', ['submitted', 'approved'])
                ->where('i.sold_qty', '>', 0)
                ->selectRaw('
                DATE(d.sale_date) as sale_date,
                AVG(i.price_per_unit) as avg_price,
                MIN(i.price_per_unit) as min_price,
                MAX(i.price_per_unit) as max_price
            ')
                ->groupBy('sale_date')
                ->orderBy('sale_date')
                ->get();

            if ($prices->isNotEmpty()) {
                $firstAvg = (float) $prices->first()->avg_price;
                $lastAvg  = (float) $prices->last()->avg_price;

                $result[] = [
                    'fuel_name'            => $fuelType->name,
                    'fuel_code'            => $fuelType->code,
                    'current_price'        => round($lastAvg, 2),
                    'price_30d_ago'        => round($firstAvg, 2),
                    'price_change'         => round($lastAvg - $firstAvg, 2),
                    'price_change_percent' => $firstAvg > 0
                        ? round((($lastAvg - $firstAvg) / $firstAvg) * 100, 2)
                        : 0,
                    'price_data'           => $prices,
                ];
            }
        }

        return collect($result);
    }


    /**
     * Sales pattern by hour (last 7 days)
     */
    private function getHourlySalesPattern()
    {
        $start = Carbon::today()->subDays(6);

        // This assumes you have a 'created_at' or 'sale_time' field
        $pattern = DB::table('fuel_sales_days')
            ->where('sale_date', '>=', $start)
            ->whereIn('status', ['submitted', 'approved'])
            ->selectRaw('
            HOUR(created_at) as hour,
            COUNT(*) as transaction_count,
            SUM(total_amount) as total_sales,
            AVG(total_amount) as avg_transaction
        ')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return $pattern;
    }



}
