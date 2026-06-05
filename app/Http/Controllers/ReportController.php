<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Admin Reports Dashboard
     */
    public function index()
    {
        $totalRevenue = Order::where('status', 'delivered')->sum('total_amount');
        $totalCommission = Order::where('status', 'delivered')->sum('commission_amount');
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalOutlets = Outlet::count();
        $totalRiders = User::whereHas('role', fn($q) => $q->where('name', 'rider'))->count();
        
        // Monthly data for chart
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = Order::where('status', 'delivered')
                ->whereMonth('delivered_at', $i)
                ->whereYear('delivered_at', date('Y'))
                ->sum('total_amount');
        }
        
        return view('admin.reports.index', compact(
            'totalRevenue', 'totalCommission', 'totalOrders', 
            'pendingOrders', 'totalOutlets', 'totalRiders', 'monthlyData'
        ));
    }
    
    /**
     * Sales Report
     */
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now());
        
        $sales = Order::where('status', 'delivered')
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->selectRaw('DATE(delivered_at) as date, SUM(total_amount) as total, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->paginate(30);
        
        $totalSales = Order::where('status', 'delivered')
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->sum('total_amount');
        
        $totalOrders = Order::where('status', 'delivered')
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->count();
        
        return view('admin.reports.sales', compact('sales', 'totalSales', 'totalOrders', 'startDate', 'endDate'));
    }
    
    /**
     * Commission Report
     */
    public function commission(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now());
        
        $commissions = Order::where('status', 'delivered')
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->selectRaw('DATE(delivered_at) as date, SUM(commission_amount) as total, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->paginate(30);
        
        $totalCommission = Order::where('status', 'delivered')
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->sum('commission_amount');
        
        return view('admin.reports.commission', compact('commissions', 'totalCommission', 'startDate', 'endDate'));
    }
    
    /**
     * Rider Performance Report
     */
    public function riders(Request $request)
    {
        $riders = User::whereHas('role', fn($q) => $q->where('name', 'rider'))
            ->with(['area'])
            ->withCount(['deliveries as total_deliveries'])
            ->withSum(['deliveries as total_earnings' => function($q) {
                $q->where('status', 'delivered');
            }], 'delivery_charge')
            ->orderBy('total_deliveries', 'desc')
            ->paginate(20);
        
        return view('admin.reports.riders', compact('riders'));
    }
    
    /**
     * Outlet Performance Report
     */
    public function outlets(Request $request)
    {
        $outlets = Outlet::with(['area'])
            ->withCount(['ordersAsSupplier as total_orders'])
            ->withSum(['ordersAsSupplier as total_revenue' => function($q) {
                $q->where('status', 'delivered');
            }], 'total_amount')
            ->orderBy('total_revenue', 'desc')
            ->paginate(20);
        
        return view('admin.reports.outlets', compact('outlets'));
    }
    
    /**
     * Head Office Financial Report
     */
    public function financial()
    {
        $totalRevenue = Order::where('status', 'delivered')->sum('total_amount');
        $totalCommission = Order::where('status', 'delivered')->sum('commission_amount');
        $totalOrders = Order::count();
        $activeOutlets = Outlet::where('is_verified', true)->count();
        
        // Monthly commission data
        $monthlyCommission = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyCommission[] = Order::where('status', 'delivered')
                ->whereMonth('delivered_at', $i)
                ->whereYear('delivered_at', date('Y'))
                ->sum('commission_amount');
        }
        
        return view('head-office.financial', compact(
            'totalRevenue', 'totalCommission', 'totalOrders', 
            'activeOutlets', 'monthlyCommission'
        ));
    }
    
    /**
     * Commission Report for Head Office
     */
    public function commissionReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now());
        
        $commissions = Order::where('status', 'delivered')
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->with(['supplierOutlet', 'buyerOutlet'])
            ->select('order_number', 'total_amount', 'commission_amount', 'delivered_at', 'supplier_outlet_id', 'buyer_outlet_id')
            ->orderBy('delivered_at', 'desc')
            ->paginate(30);
        
        $totalCommission = Order::where('status', 'delivered')
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->sum('commission_amount');
        
        return view('head-office.commission', compact('commissions', 'totalCommission', 'startDate', 'endDate'));
    }
    
    /**
     * Daily Report for Head Office
     */
    public function dailyReport(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        
        $dailyStats = [
            'total_orders' => Order::whereDate('created_at', $date)->count(),
            'completed_orders' => Order::whereDate('delivered_at', $date)->count(),
            'total_revenue' => Order::whereDate('delivered_at', $date)->where('status', 'delivered')->sum('total_amount'),
            'total_commission' => Order::whereDate('delivered_at', $date)->where('status', 'delivered')->sum('commission_amount'),
            'new_outlets' => Outlet::whereDate('created_at', $date)->count(),
            'new_riders' => User::whereHas('role', fn($q) => $q->where('name', 'rider'))
                ->whereDate('created_at', $date)
                ->count(),
        ];
        
        $recentOrders = Order::whereDate('created_at', $date)
            ->with(['buyerOutlet', 'supplierOutlet'])
            ->latest()
            ->limit(20)
            ->get();
        
        return view('head-office.daily-report', compact('dailyStats', 'recentOrders', 'date'));
    }
    
    /**
     * Monthly Report for Head Office
     */
    public function monthlyReport(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $monthlyStats = [
            'total_orders' => Order::whereMonth('created_at', $month)->whereYear('created_at', $year)->count(),
            'completed_orders' => Order::whereMonth('delivered_at', $month)->whereYear('delivered_at', $year)->count(),
            'total_revenue' => Order::whereMonth('delivered_at', $month)->whereYear('delivered_at', $year)
                ->where('status', 'delivered')
                ->sum('total_amount'),
            'total_commission' => Order::whereMonth('delivered_at', $month)->whereYear('delivered_at', $year)
                ->where('status', 'delivered')
                ->sum('commission_amount'),
            'new_outlets' => Outlet::whereMonth('created_at', $month)->whereYear('created_at', $year)->count(),
            'new_riders' => User::whereHas('role', fn($q) => $q->where('name', 'rider'))
                ->whereMonth('created_at', $month)->whereYear('created_at', $year)
                ->count(),
        ];
        
        // Daily breakdown for the month
        $dailyBreakdown = Order::whereMonth('delivered_at', $month)->whereYear('delivered_at', $year)
            ->where('status', 'delivered')
            ->selectRaw('DAY(delivered_at) as day, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('day')
            ->orderBy('day')
            ->get();
        
        return view('head-office.monthly-report', compact('monthlyStats', 'dailyBreakdown', 'month', 'year'));
    }
    
    /**
     * Area Manager Reports
     */
    public function areaReports()
    {
        $areaId = auth()->user()->area_id;
        
        $stats = [
            'total_orders' => Order::whereHas('buyerOutlet', fn($q) => $q->where('area_id', $areaId))->count(),
            'completed_orders' => Order::whereHas('buyerOutlet', fn($q) => $q->where('area_id', $areaId))
                ->where('status', 'delivered')
                ->count(),
            'total_revenue' => Order::whereHas('buyerOutlet', fn($q) => $q->where('area_id', $areaId))
                ->where('status', 'delivered')
                ->sum('total_amount'),
            'active_outlets' => Outlet::where('area_id', $areaId)->where('is_verified', true)->count(),
            'active_riders' => User::where('area_id', $areaId)
                ->whereHas('role', fn($q) => $q->where('name', 'rider'))
                ->where('is_active', true)
                ->count(),
        ];
        
        return view('area-manager.reports', compact('stats'));
    }
    
    /**
     * Delivery Report for Area Manager
     */
    public function deliveryReport(Request $request)
    {
        $areaId = auth()->user()->area_id;
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now());
        
        $deliveries = Order::whereHas('buyerOutlet', fn($q) => $q->where('area_id', $areaId))
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->with(['rider', 'buyerOutlet', 'supplierOutlet'])
            ->orderBy('delivered_at', 'desc')
            ->paginate(30);
        
        $totalDeliveries = Order::whereHas('buyerOutlet', fn($q) => $q->where('area_id', $areaId))
            ->whereBetween('delivered_at', [$startDate, $endDate])
            ->count();
        
        return view('area-manager.delivery-report', compact('deliveries', 'totalDeliveries', 'startDate', 'endDate'));
    }
}