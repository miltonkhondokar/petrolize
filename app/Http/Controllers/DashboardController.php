<?php

namespace App\Http\Controllers;

use App\Models\FuelStation;
use App\Models\FuelType;
use App\Models\CostEntry;
use App\Models\FuelStationComplaint;
use App\Models\FuelStationStock;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function index()
    {
        // Summary Statistics
        $totalPumps = FuelStation::count();
        $activePumps = FuelStation::where('is_active', true)->count();
        $inactivePumps = FuelStation::where('is_active', false)->count();

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
        $openComplaints = FuelStationComplaint::where('status', 'open')
            ->where('is_active', true)
            ->count();
        $resolvedComplaints = FuelStationComplaint::where('status', 'resolved')
            ->where('is_active', true)
            ->count();

        // Monthly Expenses Chart (Last 6 months - changed from 30 days to match blade)
        $monthlyExpensesChart = $this->getMonthlyExpensesChart();

        // Stock Value by Fuel Station Chart
        $stockValueChart = $this->getStockValueByPumpChart();

        // Recent Stock Additions
        $recentStocks = FuelStationStock::with(['fuelStation', 'fuelType', 'fuelUnit'])
            ->where('is_active', true)
            ->orderBy('stock_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent Complaints
        $recentComplaints = FuelStationComplaint::with('fuelStation')
            ->where('is_active', true)
            ->orderBy('complaint_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Monthly Expenses by Category
        $monthlyExpensesByCategory = $this->getMonthlyExpensesByCategory();

        // NEW: Fuel Type Stocks for Distribution Chart
        $fuelTypeStocks = $this->getFuelTypeStocks();

        // NEW: Fuel Station Performance Data
        $pumpPerformance = $this->getPumpPerformance();

        // NEW: Fuel Types for distribution
        $fuelTypes = FuelType::where('is_active', true)
            ->select('name', 'code', 'uuid')
            ->get();

        // Get total stock value for dashboard stats
        $totalStockValue = FuelStationStock::where('is_active', true)->sum('total_cost');

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
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as total")
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
     * Get stock value by fuel stations chart data
     */
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

    /**
     * Get fuel station performance data
     */
    private function getPumpPerformance()
    {
        $fuelStations = FuelStation::where('is_active', true)
            ->select('uuid', 'name', 'location', 'is_active')
            ->withCount(['complaints' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->map(function ($fuelStation) {
            // Get stock value for this fuel station
            $stockValue = FuelStationStock::where('fuel_station_uuid', $fuelStation->uuid)
                    ->where('is_active', true)
                    ->sum('total_cost');

                return (object) [
                    'uuid' => $fuelStation->uuid,
                    'name' => $fuelStation->name,
                    'location' => $fuelStation->location,
                    'complaint_count' => $fuelStation->complaints_count,
                    'stock_value' => $stockValue ?? 0,
                    'is_active' => $fuelStation->is_active
                ];
            })
            ->sortByDesc('stock_value')
            ->take(5); // Limit to top 5 pumps by stock value

        return $fuelStations;
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
        $totalPumps = FuelStation::count();
        $activePumps = FuelStation::where('is_active', true)->count();
        $inactivePumps = FuelStation::where('is_active', false)->count();

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
        $openComplaints = FuelStationComplaint::where('status', 'open')
            ->where('is_active', true)
            ->count();
        $resolvedComplaints = FuelStationComplaint::where('status', 'resolved')
            ->where('is_active', true)
            ->count();

        // Get total stock value
        $totalStockValue = FuelStationStock::where('is_active', true)->sum('total_cost');

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
