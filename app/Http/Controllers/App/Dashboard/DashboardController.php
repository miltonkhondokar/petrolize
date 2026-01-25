<?php

namespace App\Http\Controllers\App\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\FuelStation;
use App\Models\FuelType;
use App\Models\CostEntry;
use App\Models\FuelStationComplaint;
use App\Models\FuelStationStock;
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
        // CHARTS (existing)
        // =========================
        $monthlyExpensesChart      = $this->getMonthlyExpensesChart();      // last 6 months
        $stockValueChart           = $this->getStockValueByPumpChart();     // top 10 stations by stock value
        $monthlyExpensesByCategory = $this->getMonthlyExpensesByCategory(); // current month category summary
        $fuelTypeStocks            = $this->getFuelTypeStocks();            // stock distribution by fuel type
        $pumpPerformance           = $this->getPumpPerformance();           // top 5 stations by stock value

        // =========================
        // RECENT LISTS
        // =========================
        $recentStocks = FuelStationStock::with(['fuelStation', 'fuelType', 'fuelUnit'])
            ->where('is_active', true)
            ->orderBy('stock_date', 'desc')
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

        $totalStockValue = (float) FuelStationStock::where('is_active', true)->sum('total_cost');

        // =========================
        // NEW: SALES + STOCK INTELLIGENCE
        // =========================
        [$todaySalesAmount, $todaySoldLiters] = $this->getTodaySalesKpis();
        [$mtdSalesAmount, $mtdSoldLiters]     = $this->getMonthToDateSalesKpis();

        $salesTrend14Days  = $this->getSalesTrend14Days();        // amount + liters
        $stockMoveTrend14  = $this->getStockMoveTrend14Days();    // qty_in + qty_out

        $topFuelsMtd       = $this->getTopFuelsMtd();             // liters + revenue
        $topStationsMtd    = $this->getTopStationsMtd();          // revenue

        $vendorPayables    = $this->getVendorPayablesSummary();   // total_purchases, paid, due
        $lowStockAlerts    = $this->getLowStockAlerts(5);         // threshold liters

        // =========================
        // OPTIONAL: Profit snapshot (very simple)
        // Profit = Sales - Purchases - Expenses (MTD)
        // =========================
        $profitSnapshot = $this->getMtdProfitSnapshot();

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

            // NEW
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
            'profitSnapshot'
        ));
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
    // Existing chart: top 10 stations by stock value
    // =========================================================
    private function getStockValueByPumpChart()
    {
        $stockValues = DB::table('fuel_station_stocks as pfs')
            ->join('fuel_stations as p', 'pfs.fuel_station_uuid', '=', 'p.uuid')
            ->where('pfs.is_active', true)
            ->where('p.is_active', true)
            ->selectRaw('p.name as pump_name, SUM(pfs.total_cost) as total_value')
            ->groupBy('p.uuid', 'p.name')
            ->orderBy('total_value', 'desc')
            ->limit(10)
            ->get();

        return [
            'labels' => $stockValues->pluck('pump_name')->toArray(),
            'data'   => $stockValues->pluck('total_value')->map(fn ($v) => (float)$v)->toArray(),
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
    // Existing: stock distribution by fuel type
    // =========================================================
    private function getFuelTypeStocks()
    {
        return DB::table('fuel_station_stocks as pfs')
            ->join('fuel_types as ft', 'pfs.fuel_type_uuid', '=', 'ft.uuid')
            ->where('pfs.is_active', true)
            ->selectRaw('
                ft.name as fuel_name,
                ft.code as fuel_code,
                SUM(pfs.quantity) as total_quantity,
                SUM(pfs.total_cost) as total_value
            ')
            ->groupBy('ft.uuid', 'ft.name', 'ft.code')
            ->orderBy('total_quantity', 'desc')
            ->get();
    }

    // =========================================================
    // Existing: top 5 stations by stock value + complaints
    // =========================================================
    private function getPumpPerformance()
    {
        $fuelStations = FuelStation::where('is_active', true)
            ->select('uuid', 'name', 'location', 'is_active')
            ->withCount(['complaints' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->map(function ($fuelStation) {
                $stockValue = (float) FuelStationStock::where('fuel_station_uuid', $fuelStation->uuid)
                    ->where('is_active', true)
                    ->sum('total_cost');

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
    // NEW: Sales KPIs
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
    // NEW: Sales Trend (last 14 days)
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
    // NEW: Stock Move Trend (last 14 days) from ledger
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
    // NEW: Top fuels MTD
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
    // NEW: Top stations MTD
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
    // NEW: Vendor payable summary
    // =========================================================
    private function getVendorPayablesSummary(): array
    {
        $totalPurchases = (float) FuelPurchase::whereIn('status', ['received'])
            ->sum('total_amount');

        $paid = (float) VendorPaymentAllocation::sum('allocated_amount');

        return [
            'total_purchases' => $totalPurchases,
            'paid' => $paid,
            'due' => max(0, $totalPurchases - $paid),
        ];
    }

    // =========================================================
    // NEW: Low stock alerts based on latest ledger balance
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
            ->join('fuel_types as ft', 'l.fuel_type_uuid', '=', 'ft.uuid') // ✅ fixed
            ->where('s.is_active', true)
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
    // NEW: Simple MTD profit snapshot
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
            ->whereIn('status', ['received'])
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
    // If you still want the JSON endpoint (optional)
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

        $totalStockValue = (float) FuelStationStock::where('is_active', true)->sum('total_cost');

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
}
