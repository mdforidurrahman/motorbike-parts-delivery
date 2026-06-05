<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Notification;
use App\Events\NewOrderPlaced;
use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\Role;  // যদি Role মডেল ব্যবহার করেন
use App\Models\Area;  // যদি Area মডেল ব্যবহার করেন
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['buyerOutlet', 'supplierOutlet', 'rider'])->latest()->paginate(20);
        return view('orders.index', compact('orders'));
    }
    
    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'delivery_address' => 'required|string'
            ]);
            
            $product = Product::findOrFail($request->product_id);
            $buyerOutlet = auth()->user()->outlet;
            
            DB::beginTransaction();
            
            try {
                $totalAmount = $product->price * $request->quantity;
                
                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                    'buyer_outlet_id' => $buyerOutlet->id,
                    'total_amount' => $totalAmount,
                    'delivery_charge' => 50,
                    'delivery_address' => $request->delivery_address,
                    'status' => 'pending',
                    'payment_status' => 'pending'
                ]);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'price' => $product->price
                ]);
                
                $order->calculateCommission();
                
                DB::commit();
                
                event(new NewOrderPlaced($order));
                
                return redirect()->route('outlet.orders.buyer')
                    ->with('success', 'Order placed successfully!');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Failed to create order: ' . $e->getMessage());
            }
        }
        
        $products = Product::where('is_available', true)->where('stock_quantity', '>', 0)->get();
        return view('orders.request-product', compact('products'));
    }
    
    public function show($id)
    {
        $order = Order::with(['buyerOutlet', 'supplierOutlet', 'rider', 'items.product'])->findOrFail($id);
        
        // Check authorization
        $user = auth()->user();
        if ($user->hasRole('outlet-owner')) {
            $outletId = $user->outlet->id;
            if ($order->buyer_outlet_id != $outletId && $order->supplier_outlet_id != $outletId) {
                abort(403);
            }
        } elseif ($user->hasRole('rider') && $order->rider_id != $user->id) {
            abort(403);
        }
        
        return view('orders.show', compact('order'));
    }
    
    public function acceptOrder($id)
    {
        $order = Order::findOrFail($id);
        $outlet = auth()->user()->outlet;
        
        if (!$order->canAccept()) {
            return back()->with('error', 'Order cannot be accepted.');
        }
        
        if ($outlet->type !== 'contracted') {
            return back()->with('error', 'Only contracted outlets can accept orders.');
        }
        
        $order->update([
            'supplier_outlet_id' => $outlet->id,
            'status' => 'accepted'
        ]);
        
        Notification::create([
            'title' => 'Order Accepted',
            'message' => "Your order #{$order->order_number} has been accepted by {$outlet->shop_name}",
            'type' => 'order',
            'notifiable_type' => Outlet::class,
            'notifiable_id' => $order->buyer_outlet_id,
            'data' => json_encode(['order_id' => $order->id])
        ]);
        
        return back()->with('success', 'Order accepted successfully!');
    }
    
    public function rejectOrder($id)
    {
        $order = Order::findOrFail($id);
        
        if (!$order->canAccept()) {
            return back()->with('error', 'Order cannot be rejected.');
        }
        
        $order->update(['status' => 'cancelled']);
        
        return back()->with('success', 'Order rejected.');
    }
    
    public function markAsReady($id)
    {
        $order = Order::findOrFail($id);
        
        if ($order->status !== 'accepted') {
            return back()->with('error', 'Order not in accepted status.');
        }
        
        // Find available rider
        $rider = User::whereHas('role', fn($q) => $q->where('name', 'rider'))
            ->where('area_id', auth()->user()->area_id)
            ->where('is_active', true)
            ->first();
            
        if ($rider) {
            $order->update([
                'rider_id' => $rider->id,
                'status' => 'rider_assigned'
            ]);
            
            Notification::create([
                'title' => 'New Delivery Assignment',
                'message' => "You have been assigned to deliver order #{$order->order_number}",
                'type' => 'delivery',
                'notifiable_type' => User::class,
                'notifiable_id' => $rider->id,
                'data' => json_encode(['order_id' => $order->id])
            ]);
        }
        
        return back()->with('success', 'Order marked as ready for delivery.');
    }
    
    public function supplierOrders()
    {
        $outletId = auth()->user()->outlet->id;
        $orders = Order::where('supplier_outlet_id', $outletId)
            ->with(['buyerOutlet', 'rider'])
            ->latest()
            ->paginate(20);
            
        return view('orders.supplier-orders', compact('orders'));
    }
    
    public function buyerOrders()
    {
        $outletId = auth()->user()->outlet->id;
        $orders = Order::where('buyer_outlet_id', $outletId)
            ->with(['supplierOutlet', 'rider'])
            ->latest()
            ->paginate(20);
            
        return view('orders.buyer-orders', compact('orders'));
    }
    
public function areaOrders()
{
    $areaId = auth()->user()->area_id;
    
    $orders = Order::whereHas('buyerOutlet', fn($q) => $q->where('area_id', $areaId))
        ->with(['buyerOutlet', 'supplierOutlet', 'rider', 'items.product'])
        ->latest()
        ->paginate(20);
    
    // Get contracted outlets for this area (for assignment)
    $contractedOutlets = Outlet::where('area_id', $areaId)
        ->where('type', 'contracted')
        ->where('is_verified', true)
        ->get();
    
    // Get available riders for this area
    $availableRiders = User::whereHas('role', fn($q) => $q->where('name', 'rider'))
        ->where('area_id', $areaId)
        ->where('is_active', true)
        ->get();
    
    return view('area-manager.orders.index', compact('orders', 'contractedOutlets', 'availableRiders'));
}
    
    public function adminOrders()
    {
        $orders = Order::with(['buyerOutlet', 'supplierOutlet', 'rider'])
            ->latest()
            ->paginate(30);
            
        return view('admin.orders', compact('orders'));
    }
    
    public function adminCancel($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'cancelled']);
        
        return back()->with('success', 'Order cancelled.');
    }
    
    public function nearbyOrders(Request $request)
    {
        $rider = auth()->user();
        $orders = Order::where('status', 'accepted')
            ->whereHas('supplierOutlet', fn($q) => $q->where('area_id', $rider->area_id))
            ->with(['buyerOutlet', 'supplierOutlet'])
            ->get();
            
        return response()->json($orders);
    }
    
    public function trackOrder($id)
    {
        $order = Order::with(['rider'])->findOrFail($id);
        
        return response()->json([
            'status' => $order->status,
            'rider_location' => $order->rider ? [
                'lat' => $order->rider->latitude ?? null,
                'lng' => $order->rider->longitude ?? null
            ] : null,
            'estimated_delivery' => $order->delivered_at ?? null
        ]);
    }

/**
 * Show form to request a product (for small shops)
 */
public function requestProductForm()
{
    $products = Product::where('is_available', true)
        ->where('stock_quantity', '>', 0)
        ->with('outlet')
        ->get();
    
    return view('outlet.request-product', compact('products'));
}

/**
 * Store order request from small shop
 */
public function createOrder(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
        'delivery_address' => 'required|string'
    ]);
    
    $product = Product::findOrFail($request->product_id);
    $buyerOutlet = auth()->user()->outlet;
    
    DB::beginTransaction();
    
    try {
        $totalAmount = $product->price * $request->quantity;
        
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'buyer_outlet_id' => $buyerOutlet->id,
            'total_amount' => $totalAmount,
            'delivery_charge' => 50,
            'delivery_address' => $request->delivery_address,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);
        
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'price' => $product->price
        ]);
        
        // Calculate 1% commission
        $order->calculateCommission();
        
        DB::commit();
        
        // Trigger real-time notifications
        event(new NewOrderPlaced($order));
        
        return redirect()->route('outlet.orders.buyer')
            ->with('success', 'Order placed successfully!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to create order: ' . $e->getMessage());
    }
}

/**
 * Assign a supplier to an order (Area Manager)
 */
public function assignSupplier(Request $request, $id)
{
    $order = Order::findOrFail($id);
    $user = auth()->user();
    
    // Check if area manager has permission
    if ($user->hasRole('area-manager')) {
        $areaId = $user->area_id;
        if ($order->buyerOutlet->area_id != $areaId) {
            return back()->with('error', 'You can only assign suppliers for orders in your area.');
        }
    }
    
    $request->validate([
        'supplier_outlet_id' => 'required|exists:outlets,id'
    ]);
    
    $supplier = Outlet::find($request->supplier_outlet_id);
    
    // Check if supplier is contracted and in same area
    if ($supplier->type != 'contracted') {
        return back()->with('error', 'Only contracted outlets can be assigned as suppliers.');
    }
    
    if ($supplier->area_id != $order->buyerOutlet->area_id) {
        return back()->with('error', 'Supplier must be in the same area as the buyer.');
    }
    
    $order->update([
        'supplier_outlet_id' => $supplier->id,
        'status' => 'accepted'
    ]);
    
    // Create notification for supplier
    Notification::create([
        'title' => 'New Order Assigned',
        'message' => "Order #{$order->order_number} has been assigned to you.",
        'type' => 'order',
        'notifiable_type' => Outlet::class,
        'notifiable_id' => $supplier->id,
        'data' => json_encode(['order_id' => $order->id])
    ]);
    
    return redirect()->route('area-manager.orders.index')
        ->with('success', 'Supplier assigned successfully!');
}

/**
 * Assign a rider to an order (Area Manager)
 */
public function assignRider(Request $request, $id)
{
    $order = Order::findOrFail($id);
    $user = auth()->user();
    
    // Check if area manager has permission
    if ($user->hasRole('area-manager')) {
        $areaId = $user->area_id;
        if ($order->buyerOutlet->area_id != $areaId) {
            return back()->with('error', 'You can only assign riders for orders in your area.');
        }
    }
    
    $request->validate([
        'rider_id' => 'required|exists:users,id'
    ]);
    
    $rider = User::find($request->rider_id);
    
    // Check if rider is active and in same area
    if (!$rider->is_active) {
        return back()->with('error', 'This rider is not active.');
    }
    
    if ($rider->area_id != $order->buyerOutlet->area_id) {
        return back()->with('error', 'Rider must be in the same area as the buyer.');
    }
    
    $order->update([
        'rider_id' => $rider->id,
        'status' => 'rider_assigned'
    ]);
    
    // Create notification for rider
    Notification::create([
        'title' => 'New Delivery Assignment',
        'message' => "You have been assigned to deliver order #{$order->order_number}. Delivery fee: {$order->delivery_charge} TK",
        'type' => 'delivery',
        'notifiable_type' => User::class,
        'notifiable_id' => $rider->id,
        'data' => json_encode(['order_id' => $order->id])
    ]);
    
    return redirect()->route('area-manager.orders.index')
        ->with('success', 'Rider assigned successfully!');
}

}