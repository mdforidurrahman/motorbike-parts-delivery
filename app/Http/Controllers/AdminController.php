<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Area;
use App\Models\Outlet;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_outlets' => Outlet::count(),
            'total_riders' => User::whereHas('role', fn($q) => $q->where('name', 'rider'))->count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
            'total_commission' => Order::where('status', 'delivered')->sum('commission_amount'),
            'pending_withdrawals' => Transaction::where('type', 'withdrawal')->where('status', 'pending')->sum('amount'),
        ];
        
        return view('admin.dashboard', compact('stats'));
    }
    
    /**
     * Display a listing of users
     */
    public function index()
    {
        $users = User::with('role', 'area')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }
    
    /**
     * Show form to create new user
     */
    public function create()
    {
        $roles = Role::all();
        $areas = Area::all();
        return view('admin.users.create', compact('roles', 'areas'));
    }
    
    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'nullable|min:8',
            'role_id' => 'required|exists:roles,id',
            'area_id' => 'nullable|exists:areas,id',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password ?? 'password123'),
            'role_id' => $request->role_id,
            'area_id' => $request->area_id,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
            'wallet_balance' => 0
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully! Password: ' . ($request->password ?? 'password123'));
    }
    
    /**
     * Display the specified user
     */
    public function show($id)
    {
        $user = User::with(['role', 'area'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }
    
    /**
     * Show form to edit user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $areas = Area::all();
        return view('admin.users.edit', compact('user', 'roles', 'areas'));
    }
    
    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|unique:users,phone,' . $id,
            'role_id' => 'required|exists:roles,id',
            'area_id' => 'nullable|exists:areas,id',
        ]);
        
        $user->update($request->only(['name', 'email', 'phone', 'role_id', 'area_id', 'is_active']));
        
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }
    
    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Don't allow deleting yourself
        if ($user->id == auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }
    
    /**
     * Activate a user
     */
    public function activateUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => true]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User activated successfully!');
    }
    
    /**
     * Deactivate a user
     */
    public function deactivateUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => false]);
        
        return redirect()->route('admin.users.index')
            ->with('warning', 'User deactivated successfully!');
    }
    
    /**
     * Impersonate a user
     */
    public function impersonate($id)
    {
        $user = User::findOrFail($id);
        
        // Store original user id in session
        session(['impersonated_by' => auth()->id()]);
        auth()->login($user);
        
        return redirect()->route('dashboard')
            ->with('success', 'Now impersonating ' . $user->name);
    }
    
    /**
     * Stop impersonating
     */
    public function stopImpersonate()
    {
        $originalUserId = session('impersonated_by');
        
        if ($originalUserId) {
            $originalUser = User::find($originalUserId);
            auth()->login($originalUser);
            session()->forget('impersonated_by');
            
            return redirect()->route('admin.dashboard')
                ->with('success', 'Stopped impersonating.');
        }
        
        return redirect()->route('dashboard');
    }
    
    /**
     * Bulk activate users
     */
    public function bulkActivate(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:users,id'
        ]);
        
        User::whereIn('id', $request->users)->update(['is_active' => true]);
        return response()->json(['success' => true]);
    }
    
    /**
     * Bulk deactivate users
     */
    public function bulkDeactivate(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:users,id'
        ]);
        
        User::whereIn('id', $request->users)->update(['is_active' => false]);
        return response()->json(['success' => true]);
    }
    
    /**
     * Bulk delete users
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:users,id'
        ]);
        
        // Don't allow deleting yourself
        $usersToDelete = array_diff($request->users, [auth()->id()]);
        
        User::whereIn('id', $usersToDelete)->delete();
        return response()->json(['success' => true]);
    }
    
    /**
     * Export users to CSV
     */
    public function exportUsers()
    {
        $users = User::with('role', 'area')->get();
        
        $filename = 'users_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 (fix Bengali character issue)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'ID', 
                'Name', 
                'Email', 
                'Phone', 
                'Role', 
                'Area', 
                'City', 
                'Status', 
                'Email Verified', 
                'Wallet Balance',
                'Joined Date',
                'Last Updated'
            ]);
            
            // Data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone,
                    $user->role->display_name ?? 'N/A',
                    $user->area->name ?? 'N/A',
                    $user->area->city ?? 'N/A',
                    $user->is_active ? 'Active' : 'Inactive',
                    $user->email_verified_at ? 'Yes' : 'No',
                    $user->wallet_balance ?? 0,
                    $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : 'N/A',
                    $user->updated_at ? $user->updated_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Role Management
     */
public function roles()
{
    // Use paginate() instead of get()
    $roles = Role::withCount('users')->paginate(20);
    return view('admin.roles.index', compact('roles'));
}
    
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles',
            'display_name' => 'required|string',
        ]);
        
        Role::create($request->only(['name', 'display_name']));
        
        return redirect()->route('admin.roles')
            ->with('success', 'Role created successfully!');
    }
    
    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'display_name' => 'required|string',
        ]);
        
        $role->update($request->only(['name', 'display_name']));
        
        return redirect()->route('admin.roles')
            ->with('success', 'Role updated successfully!');
    }
    
        public function editRole($id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role);
    }





    
    public function destroyRole($id)
    {
        $role = Role::findOrFail($id);
        
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role with assigned users.');
        }
        
        $role->delete();
        
        return redirect()->route('admin.roles')
            ->with('success', 'Role deleted successfully!');
    }

    public function getRoleUsersCount($id)
    {
        $role = Role::findOrFail($id);
        return response()->json(['count' => $role->users()->count()]);
    }
    
    /**
     * Settings
     */
    public function settings()
    {
        return view('admin.settings');
    }
    
    public function updateSettings(Request $request)
    {
        // Implement settings update logic
        return back()->with('success', 'Settings updated successfully!');
    }
    
    /**
     * Withdrawal Management
     */
    public function withdrawals()
    {
        $withdrawals = Transaction::where('type', 'withdrawal')
            ->with(['outlet', 'outlet.owner'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $stats = [
            'pending' => Transaction::where('type', 'withdrawal')->where('status', 'pending')->count(),
            'processing' => Transaction::where('type', 'withdrawal')->where('status', 'processing')->count(),
            'completed' => Transaction::where('type', 'withdrawal')->where('status', 'completed')->sum('amount'),
            'total_requested' => Transaction::where('type', 'withdrawal')->sum('amount'),
        ];
        
        return view('admin.withdrawals.index', compact('withdrawals', 'stats'));
    }
    
    public function showWithdrawal($id)
    {
        $withdrawal = Transaction::where('type', 'withdrawal')
            ->with(['outlet', 'outlet.owner', 'approver'])
            ->findOrFail($id);
            
        return view('admin.withdrawals.show', compact('withdrawal'));
    }
    
    public function approveWithdrawal($id)
    {
        $withdrawal = Transaction::where('type', 'withdrawal')->findOrFail($id);
        
        if ($withdrawal->status != 'pending') {
            return back()->with('error', 'This withdrawal request cannot be approved.');
        }
        
        DB::beginTransaction();
        
        try {
            $withdrawal->markAsCompleted(auth()->id());
            
            Notification::create([
                'title' => 'Withdrawal Approved',
                'message' => "Your withdrawal request of ৳" . number_format($withdrawal->amount, 2) . " has been approved and processed.",
                'type' => 'withdrawal',
                'notifiable_type' => Outlet::class,
                'notifiable_id' => $withdrawal->outlet_id,
                'data' => json_encode(['transaction_id' => $withdrawal->id])
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.withdrawals.index')
                ->with('success', 'Withdrawal request approved successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve withdrawal: ' . $e->getMessage());
        }
    }
    
    public function rejectWithdrawal($id)
    {
        $withdrawal = Transaction::where('type', 'withdrawal')->findOrFail($id);
        
        if ($withdrawal->status != 'pending') {
            return back()->with('error', 'This withdrawal request cannot be rejected.');
        }
        
        DB::beginTransaction();
        
        try {
            if ($withdrawal->outlet) {
                $withdrawal->outlet->increment('wallet_balance', $withdrawal->amount);
            }
            
            $withdrawal->markAsRejected();
            
            Notification::create([
                'title' => 'Withdrawal Rejected',
                'message' => "Your withdrawal request of ৳" . number_format($withdrawal->amount, 2) . " has been rejected. Amount has been added back to your wallet.",
                'type' => 'withdrawal',
                'notifiable_type' => Outlet::class,
                'notifiable_id' => $withdrawal->outlet_id,
                'data' => json_encode(['transaction_id' => $withdrawal->id])
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.withdrawals.index')
                ->with('success', 'Withdrawal request rejected successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject withdrawal: ' . $e->getMessage());
        }
    }
    
    public function markAsProcessing($id)
    {
        $withdrawal = Transaction::where('type', 'withdrawal')->findOrFail($id);
        
        if ($withdrawal->status != 'pending') {
            return back()->with('error', 'This withdrawal request cannot be marked as processing.');
        }
        
        $withdrawal->markAsProcessing();
        
        Notification::create([
            'title' => 'Withdrawal Processing',
            'message' => "Your withdrawal request of ৳" . number_format($withdrawal->amount, 2) . " is now being processed.",
            'type' => 'withdrawal',
            'notifiable_type' => Outlet::class,
            'notifiable_id' => $withdrawal->outlet_id,
            'data' => json_encode(['transaction_id' => $withdrawal->id])
        ]);
        
        return back()->with('success', 'Withdrawal marked as processing!');
    }
}