<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiderController extends Controller
{
    public function index()
    {
        $riders = User::whereHas('role', fn($q) => $q->where('name', 'rider'))
            ->with('area')
            ->latest()
            ->paginate(20);
            
        return view('admin.riders.index', compact('riders'));
    }
    
    public function show($id)
    {
        $rider = User::findOrFail($id);
        
        $stats = [
            'total_deliveries' => $rider->deliveries()->count(),
            'completed_deliveries' => $rider->deliveries()->where('status', 'delivered')->count(),
            'total_earnings' => $rider->deliveries()->sum('delivery_charge'),
            'cancelled_deliveries' => $rider->deliveries()->where('status', 'cancelled')->count()
        ];
        
        $recentDeliveries = $rider->deliveries()
            ->with(['buyerOutlet', 'supplierOutlet'])
            ->latest()
            ->limit(10)
            ->get();
            
        return view('admin.riders.show', compact('rider', 'stats', 'recentDeliveries'));
    }
    
    public function create()
    {
        $areas = \App\Models\Area::all();
        return view('area-manager.riders.create', compact('areas'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'area_id' => 'required|exists:areas,id'
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt('password123'),
            'role_id' => 6, // rider role id
            'area_id' => $request->area_id,
            'is_active' => true,
            'wallet_balance' => 0
        ]);
        
        return redirect()->route('area-manager.riders.index')
            ->with('success', 'Rider created successfully! Password: password123');
    }
    
    public function edit($id)
    {
        $rider = User::findOrFail($id);
        $areas = \App\Models\Area::all();
        
        return view('area-manager.riders.edit', compact('rider', 'areas'));
    }
    
    public function update(Request $request, $id)
    {
        $rider = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'area_id' => 'required|exists:areas,id'
        ]);
        
        $rider->update($request->only(['name', 'phone', 'email', 'area_id']));
        
        return redirect()->route('area-manager.riders.index')
            ->with('success', 'Rider updated successfully!');
    }
    
    public function activate($id)
    {
        $rider = User::findOrFail($id);
        $rider->update(['is_active' => true]);
        
        return back()->with('success', 'Rider activated!');
    }
    
    public function deactivate($id)
    {
        $rider = User::findOrFail($id);
        $rider->update(['is_active' => false]);
        
        return back()->with('warning', 'Rider deactivated!');
    }
    
    public function areaRiders()
    {
        $areaId = auth()->user()->area_id;
        $riders = User::whereHas('role', fn($q) => $q->where('name', 'rider'))
            ->where('area_id', $areaId)
            ->latest()
            ->paginate(20);
            
        return view('area-manager.riders.index', compact('riders'));
    }
    
    public function dashboard()
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
            
        $completedDeliveries = $rider->deliveries()
            ->where('status', 'delivered')
            ->latest()
            ->limit(10)
            ->get();
            
        return view('rider.dashboard', compact('stats', 'availableOrders', 'myDeliveries', 'completedDeliveries'));
    }
    
    public function availableDeliveries()
    {
        $rider = auth()->user();
        
        $orders = Order::where('status', 'accepted')
            ->whereHas('supplierOutlet', fn($q) => $q->where('area_id', $rider->area_id))
            ->with(['buyerOutlet', 'supplierOutlet'])
            ->get();
            
        return view('rider.available-deliveries', compact('orders'));
    }
    
    public function myDeliveries()
    {
        $rider = auth()->user();
        
        $deliveries = $rider->deliveries()
            ->with(['buyerOutlet', 'supplierOutlet'])
            ->latest()
            ->paginate(20);
            
        return view('rider.my-deliveries', compact('deliveries'));
    }
    
    public function showDelivery($id)
    {
        $order = Order::with(['buyerOutlet', 'supplierOutlet', 'items.product'])
            ->findOrFail($id);
            
        if ($order->rider_id != auth()->id()) {
            abort(403);
        }
        
        return view('rider.delivery-details', compact('order'));
    }
    
    public function acceptDelivery($id)
    {
        $order = Order::findOrFail($id);
        $rider = auth()->user();
        
        if ($order->status !== 'accepted') {
            return back()->with('error', 'Order is not available for delivery.');
        }
        
        $order->update([
            'rider_id' => $rider->id,
            'status' => 'rider_assigned'
        ]);
        
        // Notify the supplier outlet
        Notification::create([
            'title' => 'Rider Assigned',
            'message' => "Rider {$rider->name} has been assigned to order #{$order->order_number}",
            'type' => 'delivery',
            'notifiable_type' => \App\Models\Outlet::class,
            'notifiable_id' => $order->supplier_outlet_id,
            'data' => json_encode(['order_id' => $order->id])
        ]);
        
        return redirect()->route('rider.deliveries.my')
            ->with('success', 'Delivery accepted successfully!');
    }
    
    public function markPickedUp($id)
    {
        $order = Order::findOrFail($id);
        
        if ($order->rider_id != auth()->id()) {
            abort(403);
        }
        
        if ($order->status !== 'rider_assigned') {
            return back()->with('error', 'Order not in correct status.');
        }
        
        $order->update(['status' => 'picked_up']);
        
        return back()->with('success', 'Order marked as picked up.');
    }
    
    public function markDelivered($id)
    {
        $order = Order::findOrFail($id);
        
        if ($order->rider_id != auth()->id()) {
            abort(403);
        }
        
        if ($order->status !== 'picked_up') {
            return back()->with('error', 'Order not picked up yet.');
        }
        
        DB::beginTransaction();
        
        try {
            $order->updateStatus('delivered');
            
            // Add delivery charge to rider wallet
            auth()->user()->increment('wallet_balance', $order->delivery_charge);
            
            DB::commit();
            
            return back()->with('success', 'Order delivered successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete delivery.');
        }
    }
    
    public function reportIssue(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'issue' => 'required|string'
        ]);
        
        // Create an issue report
        // You can create an issues table for this
        
        return back()->with('info', 'Issue reported to admin.');
    }
    
    public function earnings()
    {
        $rider = auth()->user();
        
        $dailyEarnings = $rider->deliveries()
            ->where('status', 'delivered')
            ->select(DB::raw('DATE(delivered_at) as date'), DB::raw('SUM(delivery_charge) as total'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
            
        return view('rider.earnings', compact('dailyEarnings'));
    }
    
    public function earningHistory()
    {
        $rider = auth()->user();
        
        $deliveries = $rider->deliveries()
            ->where('status', 'delivered')
            ->with(['buyerOutlet', 'supplierOutlet'])
            ->latest()
            ->paginate(20);
            
        return view('rider.earning-history', compact('deliveries'));
    }
    
    public function wallet()
    {
        $rider = auth()->user();
        
        $transactions = \App\Models\Transaction::where('user_id', $rider->id)
            ->latest()
            ->paginate(20);
            
        return view('rider.wallet', compact('rider', 'transactions'));
    }
    
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100'
        ]);
        
        $rider = auth()->user();
        
        if ($rider->wallet_balance < $request->amount) {
            return back()->with('error', 'Insufficient balance!');
        }
        
        DB::beginTransaction();
        
        try {
            $rider->decrement('wallet_balance', $request->amount);
            
            \App\Models\Transaction::create([
                'user_id' => $rider->id,
                'type' => 'withdrawal',
                'amount' => $request->amount,
                'status' => 'pending',
                'reference' => 'WID-' . strtoupper(uniqid())
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Withdrawal request submitted!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process withdrawal.');
        }
    }
    
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);
        
        $rider = auth()->user();
        $rider->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);
        
        return response()->json(['success' => true]);
    }
    
    public function getLocation($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        if (!$order->rider) {
            return response()->json(['error' => 'No rider assigned'], 404);
        }
        
        return response()->json([
            'latitude' => $order->rider->latitude,
            'longitude' => $order->rider->longitude,
            'status' => $order->status
        ]);
    }
    
    public function toggleAvailability()
    {
        $rider = auth()->user();
        $rider->update(['is_available_for_delivery' => !$rider->is_available_for_delivery]);
        
        $status = $rider->is_available_for_delivery ? 'available' : 'unavailable';
        
        return response()->json(['status' => $status]);
    }
}