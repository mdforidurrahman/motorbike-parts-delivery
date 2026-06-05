<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AreaManagerController extends Controller
{
    /**
     * Display area manager dashboard
     */
    public function dashboard()
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
        ];
        
        $recentOrders = Order::whereHas('buyerOutlet', fn($q) => $q->where('area_id', $areaId))
            ->with(['buyerOutlet', 'supplierOutlet', 'rider'])
            ->latest()
            ->limit(10)
            ->get();
            
        return view('area-manager.dashboard', compact('stats', 'recentOrders'));
    }
    
    /**
     * Display a listing of areas (for admin)
     */
    public function index()
    {
        $areas = Area::withCount(['outlets', 'users'])->latest()->paginate(20);
        return view('admin.areas.index', compact('areas'));
    }
    
    /**
     * Show form to create new area
     */
    public function create()
    {
        return view('admin.areas.create');
    }
    
    /**
     * Store a newly created area
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:areas',
            'city' => 'required|string|max:255',
            'delivery_charge' => 'required|numeric|min:0',
        ]);
        
        Area::create($request->all());
        
        return redirect()->route('admin.areas.index')
            ->with('success', 'Area created successfully!');
    }
    
    /**
     * Display the specified area
     */
    public function show($id)
    {
        $area = Area::with(['outlets', 'users'])->findOrFail($id);
        return view('admin.areas.show', compact('area'));
    }
    
    /**
     * Show form to edit area
     */
    public function edit($id)
    {
        $area = Area::findOrFail($id);
        return view('admin.areas.edit', compact('area'));
    }
    
    /**
     * Update the specified area
     */
    public function update(Request $request, $id)
    {
        $area = Area::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:areas,name,' . $id,
            'city' => 'required|string|max:255',
            'delivery_charge' => 'required|numeric|min:0',
        ]);
        
        $area->update($request->all());
        
        return redirect()->route('admin.areas.index')
            ->with('success', 'Area updated successfully!');
    }
    
    /**
     * Remove the specified area
     */
    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        
        // Check if area has outlets
        if ($area->outlets()->count() > 0) {
            return back()->with('error', 'Cannot delete area with existing outlets.');
        }
        
        $area->delete();
        
        return redirect()->route('admin.areas.index')
            ->with('success', 'Area deleted successfully!');
    }
    
    /**
     * Get all areas for API
     */
    public function getAreas()
    {
        $areas = Area::all();
        return response()->json($areas);
    }
    
    /**
     * Get area details
     */
    public function getArea($id)
    {
        $area = Area::findOrFail($id);
        return response()->json($area);
    }
    
    // ==================== Outlet Management Methods ====================
    
    public function outlets()
    {
        $areaId = auth()->user()->area_id;
        $outlets = Outlet::where('area_id', $areaId)->with('owner')->latest()->paginate(20);
        return view('area-manager.outlets.index', compact('outlets'));
    }
    
    public function createOutlet()
    {
        $areas = Area::all();
        return view('area-manager.outlets.create', compact('areas'));
    }
    
    public function storeOutlet(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:outlets',
            'email' => 'nullable|email|unique:outlets',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:small,large,contracted'
        ]);
        
        DB::beginTransaction();
        
        try {
            $ownerUser = User::create([
                'name' => $request->owner_name ?? $request->shop_name,
                'email' => $request->email ?? $request->phone . '@motolink.com',
                'phone' => $request->phone,
                'password' => Hash::make('password123'),
                'role_id' => 5,
                'area_id' => $user->area_id,
                'is_active' => true,
                'wallet_balance' => 0
            ]);
            
            $outlet = Outlet::create([
                'name' => $request->owner_name ?? $request->shop_name,
                'shop_name' => $request->shop_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'area_id' => $user->area_id,
                'user_id' => $ownerUser->id,
                'type' => $request->type,
                'is_verified' => false,
                'wallet_balance' => 0
            ]);
            
            DB::commit();
            
            return redirect()->route('area-manager.outlets.index')
                ->with('success', 'Outlet created successfully! Password: password123');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create outlet: ' . $e->getMessage());
        }
    }
    
    public function showOutlet($id)
    {
        $user = auth()->user();
        $outlet = Outlet::with(['owner', 'area'])->findOrFail($id);
        
        if ($outlet->area_id != $user->area_id) {
            abort(403);
        }
        
        $stats = [
            'total_orders' => $outlet->ordersAsSupplier()->count(),
            'completed_orders' => $outlet->ordersAsSupplier()->where('status', 'delivered')->count(),
            'total_earnings' => $outlet->ordersAsSupplier()->where('status', 'delivered')->sum('total_amount'),
        ];
        
        $recentOrders = $outlet->ordersAsSupplier()
            ->with(['buyerOutlet', 'rider'])
            ->latest()
            ->limit(10)
            ->get();
        
        return view('area-manager.outlets.show', compact('outlet', 'stats', 'recentOrders'));
    }
    
    public function editOutlet($id)
    {
        $user = auth()->user();
        $outlet = Outlet::findOrFail($id);
        
        if ($outlet->area_id != $user->area_id) {
            abort(403);
        }
        
        $areas = Area::all();
        return view('area-manager.outlets.edit', compact('outlet', 'areas'));
    }
    
    public function updateOutlet(Request $request, $id)
    {
        $user = auth()->user();
        $outlet = Outlet::findOrFail($id);
        
        if ($outlet->area_id != $user->area_id) {
            abort(403);
        }
        
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:outlets,phone,' . $id,
            'email' => 'nullable|email|unique:outlets,email,' . $id,
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:small,large,contracted'
        ]);
        
        $outlet->update($request->except('_token', '_method'));
        
        if ($request->has('owner_name')) {
            $outlet->owner->update(['name' => $request->owner_name]);
        }
        
        return redirect()->route('area-manager.outlets.index')
            ->with('success', 'Outlet updated successfully!');
    }
    
    public function deleteOutlet($id)
    {
        $user = auth()->user();
        $outlet = Outlet::findOrFail($id);
        
        if ($outlet->area_id != $user->area_id) {
            abort(403);
        }
        
        if ($outlet->owner) {
            $outlet->owner->delete();
        }
        
        $outlet->delete();
        
        return redirect()->route('area-manager.outlets.index')
            ->with('success', 'Outlet deleted successfully!');
    }
    
    // ==================== Rider Management Methods ====================
    
    public function riders()
    {
        $areaId = auth()->user()->area_id;
        $riders = User::whereHas('role', fn($q) => $q->where('name', 'rider'))
            ->where('area_id', $areaId)
            ->latest()
            ->paginate(20);
            
        return view('area-manager.riders.index', compact('riders'));
    }
    
    public function createRider()
    {
        $areas = Area::all();
        return view('area-manager.riders.create', compact('areas'));
    }
    
    public function storeRider(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
        ]);
        
        $rider = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make('password123'),
            'role_id' => 6,
            'area_id' => $user->area_id,
            'is_active' => true,
            'wallet_balance' => 0
        ]);
        
        return redirect()->route('area-manager.riders.index')
            ->with('success', 'Rider created successfully! Password: password123');
    }
    
    public function showRider($id)
    {
        $rider = User::findOrFail($id);
        
        $stats = [
            'total_deliveries' => $rider->deliveries()->count(),
            'completed_deliveries' => $rider->deliveries()->where('status', 'delivered')->count(),
            'total_earnings' => $rider->deliveries()->sum('delivery_charge'),
        ];
        
        $recentDeliveries = $rider->deliveries()
            ->with(['buyerOutlet', 'supplierOutlet'])
            ->latest()
            ->limit(10)
            ->get();
        
        return view('area-manager.riders.show', compact('rider', 'stats', 'recentDeliveries'));
    }
    
    public function editRider($id)
    {
        $rider = User::findOrFail($id);
        $areas = Area::all();
        return view('area-manager.riders.edit', compact('rider', 'areas'));
    }
    
    public function updateRider(Request $request, $id)
    {
        $rider = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
        ]);
        
        $rider->update($request->only(['name', 'phone', 'email']));
        
        return redirect()->route('area-manager.riders.index')
            ->with('success', 'Rider updated successfully!');
    }
    
    public function deleteRider($id)
    {
        $rider = User::findOrFail($id);
        $rider->delete();
        
        return redirect()->route('area-manager.riders.index')
            ->with('success', 'Rider deleted successfully!');
    }
    
    public function activateRider($id)
    {
        $rider = User::findOrFail($id);
        $rider->update(['is_active' => true]);
        
        return back()->with('success', 'Rider activated successfully!');
    }
    
    public function deactivateRider($id)
    {
        $rider = User::findOrFail($id);
        $rider->update(['is_active' => false]);
        
        return back()->with('warning', 'Rider deactivated!');
    }
}