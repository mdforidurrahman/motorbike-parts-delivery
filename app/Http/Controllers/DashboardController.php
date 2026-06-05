<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('head-office')) {
            return $this->headOfficeDashboard();
        } elseif ($user->hasRole('area-manager')) {
            return $this->areaManagerDashboard();
        } elseif ($user->hasRole('marketing-officer')) {
            return $this->marketingDashboard();
        } elseif ($user->hasRole('outlet-owner')) {
            return $this->outletDashboard();
        } elseif ($user->hasRole('rider')) {
            return $this->riderDashboard();
        }
        
        return view('dashboard');
    }
    
    public function adminDashboard()
    {
        $stats = [
            'total_outlets' => Outlet::count(),
            'total_riders' => User::whereHas('role', function($q) {
                $q->where('name', 'rider');
            })->count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
            'total_commission' => Order::where('status', 'delivered')->sum('commission_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'delivered_today' => Order::whereDate('delivered_at', today())->count(),
        ];
        
        $recent_orders = Order::with(['buyerOutlet', 'supplierOutlet'])
            ->latest()
            ->limit(10)
            ->get();
            
        return view('admin.dashboard', compact('stats', 'recent_orders'));
    }
    
    public function headOfficeDashboard()
    {
        $monthlyEarnings = Order::where('status', 'delivered')
            ->whereMonth('delivered_at', now()->month)
            ->select(DB::raw('SUM(total_amount) as total, SUM(commission_amount) as commission'))
            ->first();
            
        $topOutlets = Outlet::withCount(['ordersAsSupplier as completed_deliveries' => function($q) {
                $q->where('status', 'delivered');
            }])
            ->orderBy('completed_deliveries', 'desc')
            ->limit(5)
            ->get();
            
        return view('head-office.dashboard', compact('monthlyEarnings', 'topOutlets'));
    }
    
public function areaManagerDashboard()
{
    $areaId = auth()->user()->area_id;
    
    $stats = [
        'outlets' => Outlet::where('area_id', $areaId)->count(),
        'riders' => User::where('area_id', $areaId)
            ->whereHas('role', fn($q) => $q->where('name', 'rider'))
            ->count(),
        'orders' => Order::whereHas('buyerOutlet', fn($q) => $q->where('area_id', $areaId))->count(),
        'pending_orders' => Order::where('status', 'pending')
            ->whereHas('buyerOutlet', fn($q) => $q->where('area_id', $areaId))
            ->count(),
        'verified_outlets' => Outlet::where('area_id', $areaId)
            ->where('is_verified', true)
            ->count(),
        'active_riders' => User::where('area_id', $areaId)
            ->whereHas('role', fn($q) => $q->where('name', 'rider'))
            ->where('is_active', true)
            ->count(),
        'total_revenue' => Order::where('status', 'delivered')
            ->whereHas('buyerOutlet', fn($q) => $q->where('area_id', $areaId))
            ->sum('total_amount'),
        'delivered_today' => Order::whereDate('delivered_at', today())
            ->whereHas('buyerOutlet', fn($q) => $q->where('area_id', $areaId))
            ->count(),
    ];
    
    $recentOrders = Order::whereHas('buyerOutlet', fn($q) => $q->where('area_id', $areaId))
        ->with(['buyerOutlet', 'supplierOutlet', 'rider'])
        ->latest()
        ->limit(10)
        ->get();
        
    return view('area-manager.dashboard', compact('stats', 'recentOrders'));
}
    
    public function marketingDashboard()
    {
        $newOutlets = Outlet::where('is_verified', false)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
            
        $activeCampaigns = 0; // You can implement campaigns table
        $conversionRate = 0; // Calculate conversion rate
        
        return view('marketing.dashboard', compact('newOutlets', 'activeCampaigns', 'conversionRate'));
    }
    
    public function outletDashboard()
    {
        $outlet = auth()->user()->outlet;
        
        $stats = [
            'total_products' => $outlet->products()->count(),
            'orders_as_supplier' => $outlet->ordersAsSupplier()->where('status', 'delivered')->count(),
            'orders_as_buyer' => $outlet->ordersAsBuyer()->count(),
            'pending_orders' => $outlet->ordersAsSupplier()->where('status', 'pending')->count(),
            'wallet_balance' => $outlet->wallet_balance,
        ];
        
        $recentOrders = $outlet->ordersAsSupplier()
            ->with(['buyerOutlet', 'rider'])
            ->latest()
            ->limit(5)
            ->get();
            
        $lowStockProducts = $outlet->products()
            ->where('stock_quantity', '<', 5)
            ->where('is_available', true)
            ->get();
            
        return view('outlet.dashboard', compact('stats', 'recentOrders', 'lowStockProducts'));
    }
    
    public function riderDashboard()
    {
        $rider = auth()->user();
        
        $stats = [
            'total_deliveries' => $rider->deliveries()->count(),
            'completed_today' => $rider->deliveries()
                ->whereDate('delivered_at', today())
                ->count(),
            'total_earnings' => $rider->deliveries()
                ->where('status', 'delivered')
                ->sum('delivery_charge'),
            'wallet_balance' => $rider->wallet_balance,
        ];
        
        $availableOrders = Order::where('status', 'accepted')
            ->whereHas('supplierOutlet', fn($q) => $q->where('area_id', $rider->area_id))
            ->with(['buyerOutlet', 'supplierOutlet'])
            ->get();
            
        $myDeliveries = $rider->deliveries()
            ->whereIn('status', ['rider_assigned', 'picked_up'])
            ->with(['buyerOutlet', 'supplierOutlet'])
            ->get();
            
        return view('rider.dashboard', compact('stats', 'availableOrders', 'myDeliveries'));
    }
    
    public function dailyStats(Request $request)
    {
        $date = $request->get('date', now());
        
        $stats = [
            'orders' => Order::whereDate('created_at', $date)->count(),
            'revenue' => Order::whereDate('created_at', $date)->where('status', 'delivered')->sum('total_amount'),
            'deliveries' => Order::whereDate('delivered_at', $date)->count(),
        ];
        
        return response()->json($stats);
    }
    
    public function weeklyStats()
    {
        $stats = [];
        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays($i);
            $stats[] = [
                'date' => $date->format('Y-m-d'),
                'orders' => Order::whereDate('created_at', $date)->count(),
                'revenue' => Order::whereDate('created_at', $date)->where('status', 'delivered')->sum('total_amount'),
            ];
        }
        
        return response()->json($stats);
    }
}