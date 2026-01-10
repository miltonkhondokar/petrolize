<?php

namespace App\Http\Controllers;

use App\Models\Pump;
use App\Models\FuelType;
use App\Models\CostEntry;
use App\Models\PumpComplaint;
use App\Models\PumpFuelStock;
use App\Models\CostCategory;
use App\Models\PumpFuelPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        // Summary Statistics
        $totalPumps = Pump::count();
        $activePumps = Pump::where('is_active', true)->count();
        $inactivePumps = Pump::where('is_active', false)->count();

        $totalFuelTypes = FuelType::count();
        $activeFuelTypes = FuelType::where('is_active', true)->count();
        $inactiveFuelTypes = $totalFuelTypes - $activeFuelTypes;

        // Today's Expenses
        $todayExpenses = CostEntry::whereDate('expense_date', today())
            ->where('is_active', true)
            ->sum('amount');
        $todayExpenseCount = CostEntry::whereDate('expense_date', today())
            ->where('is_active', true)
            ->count();

        // Complaints
        $openComplaints = PumpComplaint::where('status', 'open')
            ->where('is_active', true)
            ->count();
        $resolvedComplaints = PumpComplaint::where('status', 'resolved')
            ->where('is_active', true)
            ->count();

        // Monthly Expenses Chart (Last 6 months - changed from 30 days to match blade)
        $monthlyExpensesChart = $this->getMonthlyExpensesChart();

        // Stock Value by Pump Chart
        $stockValueChart = $this->getStockValueByPumpChart();

        // Recent Stock Additions
        $recentStocks = PumpFuelStock::with(['pump', 'fuelType', 'fuelUnit'])
            ->where('is_active', true)
            ->orderBy('stock_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent Complaints
        $recentComplaints = PumpComplaint::with('pump')
            ->where('is_active', true)
            ->orderBy('complaint_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Monthly Expenses by Category
        $monthlyExpensesByCategory = $this->getMonthlyExpensesByCategory();

        // NEW: Fuel Type Stocks for Distribution Chart
        $fuelTypeStocks = $this->getFuelTypeStocks();

        // NEW: Pump Performance Data
        $pumpPerformance = $this->getPumpPerformance();

        // NEW: Fuel Types for distribution
        $fuelTypes = FuelType::where('is_active', true)
            ->select('name', 'code', 'uuid')
            ->get();

        // Get total stock value for dashboard stats
        $totalStockValue = PumpFuelStock::where('is_active', true)->sum('total_cost');

        return view('application.pages.dashboard.dashboard', compact(
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
            'totalStockValue'
        ));
    }

    /**
     * Get monthly expenses chart data for last 6 months (changed to 6 months)
     */
    private function getMonthlyExpensesChart()
    {
        $startDate = Carbon::now()->subMonths(5)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $expenses = CostEntry::where('is_active', true)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->selectRaw("TO_CHAR(expense_date, 'YYYY-MM') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = [];
        $data = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $monthKey = $current->format('Y-m');
            $labels[] = $current->format('M Y');
            $data[] = isset($expenses[$monthKey])
                ? (float) $expenses[$monthKey]->total
                : 0;
            $current->addMonth();
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }


    /**
     * Get stock value by pump chart data
     */
    private function getStockValueByPumpChart()
    {
        $stockValues = DB::table('pump_fuel_stocks as pfs')
            ->join('pumps as p', 'pfs.pump_uuid', '=', 'p.uuid')
            ->where('pfs.is_active', true)
            ->where('p.is_active', true)
            ->selectRaw('p.name as pump_name, SUM(pfs.total_cost) as total_value')
            ->groupBy('p.uuid', 'p.name')
            ->orderBy('total_value', 'desc')
            ->limit(10)
            ->get();

        $labels = $stockValues->pluck('pump_name')->toArray();
        $data = $stockValues->pluck('total_value')->map(fn ($v) => (float) $v)->toArray();

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get monthly expenses by category
     */
    private function getMonthlyExpensesByCategory()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return DB::table('cost_entries as ce')
            ->leftJoin('cost_categories as cc', 'ce.cost_category_uuid', '=', 'cc.uuid')
            ->where('ce.is_active', true)
            ->whereBetween('ce.expense_date', [$startOfMonth, $endOfMonth])
            ->selectRaw('cc.name as category_name, cc.description, COUNT(ce.id) as count, SUM(ce.amount) as total')
            ->groupBy('cc.uuid', 'cc.name', 'cc.description')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
     * Get fuel type stocks for distribution chart
     */
    private function getFuelTypeStocks()
    {
        return DB::table('pump_fuel_stocks as pfs')
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

    /**
     * Get pump performance data
     */
    private function getPumpPerformance()
    {
        $pumps = Pump::where('is_active', true)
            ->select('uuid', 'name', 'location', 'is_active')
            ->withCount(['complaints' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->map(function ($pump) {
                // Get stock value for this pump
                $stockValue = PumpFuelStock::where('pump_uuid', $pump->uuid)
                    ->where('is_active', true)
                    ->sum('total_cost');

                return (object) [
                    'uuid' => $pump->uuid,
                    'name' => $pump->name,
                    'location' => $pump->location,
                    'complaint_count' => $pump->complaints_count,
                    'stock_value' => $stockValue ?? 0,
                    'is_active' => $pump->is_active
                ];
            })
            ->sortByDesc('stock_value')
            ->take(5); // Limit to top 5 pumps by stock value

        return $pumps;
    }

    /**
     * Get dashboard summary statistics
     */
    public function getDashboardStats()
    {
        // Get current month's start and end dates
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Get total pumps
        $totalPumps = Pump::count();
        $activePumps = Pump::where('is_active', true)->count();
        $inactivePumps = Pump::where('is_active', false)->count();

        // Get fuel types
        $totalFuelTypes = FuelType::count();
        $activeFuelTypes = FuelType::where('is_active', true)->count();
        $inactiveFuelTypes = $totalFuelTypes - $activeFuelTypes;

        // Get today's expenses
        $todayExpenses = CostEntry::whereDate('expense_date', today())
            ->where('is_active', true)
            ->sum('amount');
        $todayExpenseCount = CostEntry::whereDate('expense_date', today())
            ->where('is_active', true)
            ->count();

        // Get monthly expenses
        $monthlyExpenses = CostEntry::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->where('is_active', true)
            ->sum('amount');

        // Get complaints
        $openComplaints = PumpComplaint::where('status', 'open')
            ->where('is_active', true)
            ->count();
        $resolvedComplaints = PumpComplaint::where('status', 'resolved')
            ->where('is_active', true)
            ->count();

        // Get total stock value
        $totalStockValue = PumpFuelStock::where('is_active', true)->sum('total_cost');

        return response()->json([
            'success' => true,
            'data' => [
                'totalPumps' => $totalPumps,
                'activePumps' => $activePumps,
                'inactivePumps' => $inactivePumps,
                'totalFuelTypes' => $totalFuelTypes,
                'activeFuelTypes' => $activeFuelTypes,
                'inactiveFuelTypes' => $inactiveFuelTypes,
                'todayExpenses' => $todayExpenses,
                'todayExpenseCount' => $todayExpenseCount,
                'monthlyExpenses' => $monthlyExpenses,
                'openComplaints' => $openComplaints,
                'resolvedComplaints' => $resolvedComplaints,
                'totalStockValue' => $totalStockValue
            ]
        ]);
    }
}
