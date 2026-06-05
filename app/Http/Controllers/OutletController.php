<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Area;
use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class OutletController extends Controller
{
    public function index()
    {
        $outlets = Outlet::with(['area', 'owner'])->latest()->paginate(20);
        return view('admin.outlets.index', compact('outlets'));
    }
    
    public function create()
    {
        $areas = Area::all();
        return view('area-manager.outlets.create', compact('areas'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:outlets',
            'email' => 'nullable|email|unique:outlets',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'area_id' => 'required|exists:areas,id',
            'type' => 'required|in:small,large,contracted'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create user account for outlet owner
            $user = User::create([
                'name' => $request->shop_name,
                'email' => $request->email ?? $request->phone . '@motolink.com',
                'phone' => $request->phone,
                'password' => Hash::make('password123'),
                'role_id' => 5, // outlet-owner role id
                'area_id' => $request->area_id,
                'is_active' => true
            ]);
            
            // Create outlet
            $outlet = Outlet::create([
                'name' => $request->owner_name ?? $request->shop_name,
                'shop_name' => $request->shop_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'area_id' => $request->area_id,
                'user_id' => $user->id,
                'type' => $request->type,
                'is_verified' => $request->type == 'contracted' ? false : true,
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
    
    public function show($id)
    {
        $outlet = Outlet::with(['area', 'owner', 'products'])->findOrFail($id);
        
        $stats = [
            'total_orders' => $outlet->ordersAsSupplier()->count(),
            'completed_orders' => $outlet->ordersAsSupplier()->where('status', 'delivered')->count(),
            'total_earnings' => $outlet->ordersAsSupplier()->where('status', 'delivered')->sum('total_amount'),
            'total_products' => $outlet->products()->count()
        ];
        
        $recentOrders = $outlet->ordersAsSupplier()
            ->with(['buyerOutlet', 'rider'])
            ->latest()
            ->limit(10)
            ->get();
            
        return view('admin.outlets.show', compact('outlet', 'stats', 'recentOrders'));
    }
    
    public function edit($id)
    {
        $outlet = Outlet::findOrFail($id);
        $areas = Area::all();
        
        return view('area-manager.outlets.edit', compact('outlet', 'areas'));
    }
    
public function update(Request $request, $id)
{
    $user = auth()->user();
    $outlet = Outlet::findOrFail($id);
    
    // Check permission for area manager
    if ($user->hasRole('area-manager') && $outlet->area_id != $user->area_id) {
        abort(403, 'You can only update outlets in your area.');
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
    
    // Update outlet
    $outlet->update([
        'shop_name' => $request->shop_name,
        'phone' => $request->phone,
        'email' => $request->email,
        'address' => $request->address,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'type' => $request->type
    ]);
    
    // Update owner name if provided
    if ($request->has('owner_name') && !empty($request->owner_name)) {
        $outlet->owner->update(['name' => $request->owner_name]);
    }
    
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.outlets.index')
            ->with('success', 'Outlet updated successfully!');
    } else {
        return redirect()->route('area-manager.outlets.index')
            ->with('success', 'Outlet updated successfully!');
    }
}
    
    public function verify($id)
    {
        $outlet = Outlet::findOrFail($id);
        $outlet->is_verified = true;
        $outlet->save();
        
        return back()->with('success', 'Outlet verified successfully!');
    }
    
    public function suspend($id)
    {
        $outlet = Outlet::findOrFail($id);
        $outlet->is_verified = false;
        $outlet->save();
        
        // Also suspend user
        $outlet->owner->update(['is_active' => false]);
        
        return back()->with('warning', 'Outlet suspended!');
    }
    
    public function areaOutlets()
    {
        $areaId = auth()->user()->area_id;
        $outlets = Outlet::where('area_id', $areaId)->with('owner')->latest()->paginate(20);
        
        return view('area-manager.outlets.index', compact('outlets'));
    }
    
    public function allOutlets()
    {
        $outlets = Outlet::with(['area', 'owner'])->latest()->paginate(50);
        return view('head-office.outlets', compact('outlets'));
    }
    
    public function dashboard()
    {
        $outlet = auth()->user()->outlet;
        
        $stats = [
            'total_products' => $outlet->products()->count(),
            'orders_received' => $outlet->ordersAsSupplier()->count(),
            'orders_placed' => $outlet->ordersAsBuyer()->count(),
            'pending_orders' => $outlet->ordersAsSupplier()->where('status', 'pending')->count(),
            'wallet_balance' => $outlet->wallet_balance
        ];
        
        $recentOrders = $outlet->ordersAsSupplier()
            ->with(['buyerOutlet', 'rider'])
            ->latest()
            ->limit(5)
            ->get();
            
        $lowStockProducts = $outlet->products()
            ->where('stock_quantity', '<', 5)
            ->get();
            
        return view('outlet.dashboard', compact('stats', 'recentOrders', 'lowStockProducts'));
    }
    
    public function wallet()
    {
        $outlet = auth()->user()->outlet;
        
        $transactions = Transaction::where('outlet_id', $outlet->id)
            ->latest()
            ->paginate(20);
            
        return view('outlet.wallet', compact('outlet', 'transactions'));
    }
    
    public function transactions()
    {
        $outlet = auth()->user()->outlet;
        
        $transactions = $outlet->transactions()->latest()->paginate(20);
        
        return response()->json($transactions);
    }
    
    public function withdrawForm()
    {
        $outlet = auth()->user()->outlet;
        return view('outlet.withdraw', compact('outlet'));
    }
    
public function withdraw(Request $request)
{
    try {
        \Log::info('Withdraw request started', $request->all());
        
        $outlet = auth()->user()->outlet;
        
        $request->validate([
            'amount' => 'required|numeric|min:500|max:' . $outlet->wallet_balance,
            'payment_method' => 'required',
        ]);
        
        DB::beginTransaction();
        
        $outlet->decrement('wallet_balance', $request->amount);
        
        Transaction::create([
            'outlet_id' => $outlet->id,
            'type' => 'withdrawal',
            'amount' => $request->amount,
            'status' => 'pending',
            'reference' => 'WID-' . strtoupper(uniqid()),
            'payment_method' => $request->payment_method,
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'account_holder' => $request->account_holder,
            'branch_name' => $request->branch_name,
            'note' => $request->note,
        ]);
        
        DB::commit();
        
        return back()->with('success', 'Withdrawal request submitted successfully!');
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Withdraw error: ' . $e->getMessage());
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}

public function cancelWithdrawal($id)
{
    $transaction = Transaction::findOrFail($id);
    $outlet = auth()->user()->outlet;
    
    // Check if transaction belongs to this outlet
    if ($transaction->outlet_id != $outlet->id) {
        abort(403, 'Unauthorized action.');
    }
    
    // Check if transaction is still pending
    if ($transaction->status != 'pending') {
        return redirect()->back()->with('error', 'This withdrawal request cannot be cancelled.');
    }
    
    DB::beginTransaction();
    
    try {
        // Add amount back to wallet
        $outlet->increment('wallet_balance', $transaction->amount);
        
        // Cancel transaction
        $transaction->markAsCancelled();
        
        DB::commit();
        
        return redirect()->back()->with('success', 'Withdrawal request cancelled successfully!');
        
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Failed to cancel withdrawal!');
    }
}
    
    public function qrCode()
    {
        $outlet = auth()->user()->outlet;
        $qrCode = QrCode::size(200)->generate(route('outlet.show', $outlet->id));
        
        return view('outlet.qrcode', compact('qrCode', 'outlet'));
    }
    
    public function getOutletsByArea($areaId)
    {
        $outlets = Outlet::where('area_id', $areaId)
            ->where('is_verified', true)
            ->get(['id', 'shop_name', 'latitude', 'longitude']);
            
        return response()->json($outlets);
    }
}