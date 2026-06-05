<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('outlet-owner')) {
            $notifications = Notification::where('notifiable_type', 'App\Models\Outlet')
                ->where('notifiable_id', $user->outlet->id)
                ->latest()
                ->paginate(20);
        } else {
            $notifications = Notification::where('notifiable_type', 'App\Models\User')
                ->where('notifiable_id', $user->id)
                ->latest()
                ->paginate(20);
        }
        
        return view('notifications.index', compact('notifications'));
    }
    
    public function unreadCount(Request $request)
    {
        $user = Auth::user();
        
        if ($user->hasRole('outlet-owner')) {
            $count = Notification::where('notifiable_type', 'App\Models\Outlet')
                ->where('notifiable_id', $user->outlet->id)
                ->where('is_read', false)
                ->count();
                
            $notifications = Notification::where('notifiable_type', 'App\Models\Outlet')
                ->where('notifiable_id', $user->outlet->id)
                ->where('is_read', false)
                ->latest()
                ->limit(5)
                ->get();
        } else {
            $count = Notification::where('notifiable_type', 'App\Models\User')
                ->where('notifiable_id', $user->id)
                ->where('is_read', false)
                ->count();
                
            $notifications = Notification::where('notifiable_type', 'App\Models\User')
                ->where('notifiable_id', $user->id)
                ->where('is_read', false)
                ->latest()
                ->limit(5)
                ->get();
        }
        
        if ($request->ajax()) {
            return response()->json([
                'count' => $count,
                'notifications' => $notifications
            ]);
        }
        
        return response()->json(['count' => $count]);
    }
    
    public function latest(Request $request)
    {
        $user = Auth::user();
        
        if ($user->hasRole('outlet-owner')) {
            $notifications = Notification::where('notifiable_type', 'App\Models\Outlet')
                ->where('notifiable_id', $user->outlet->id)
                ->latest()
                ->limit(10)
                ->get();
        } else {
            $notifications = Notification::where('notifiable_type', 'App\Models\User')
                ->where('notifiable_id', $user->id)
                ->latest()
                ->limit(10)
                ->get();
        }
        
        return response()->json($notifications);
    }
    
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        $user = Auth::user();
        if ($user->hasRole('outlet-owner')) {
            if ($notification->notifiable_type == 'App\Models\Outlet' && $notification->notifiable_id == $user->outlet->id) {
                $notification->markAsRead();
            }
        } else {
            if ($notification->notifiable_type == 'App\Models\User' && $notification->notifiable_id == $user->id) {
                $notification->markAsRead();
            }
        }
        
        return response()->json(['success' => true]);
    }
    
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        if ($user->hasRole('outlet-owner')) {
            Notification::where('notifiable_type', 'App\Models\Outlet')
                ->where('notifiable_id', $user->outlet->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
        } else {
            Notification::where('notifiable_type', 'App\Models\User')
                ->where('notifiable_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
        }
        
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
    
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        
        $user = Auth::user();
        if ($user->hasRole('outlet-owner')) {
            if ($notification->notifiable_type == 'App\Models\Outlet' && $notification->notifiable_id == $user->outlet->id) {
                $notification->delete();
            }
        } else {
            if ($notification->notifiable_type == 'App\Models\User' && $notification->notifiable_id == $user->id) {
                $notification->delete();
            }
        }
        
        return response()->json(['success' => true]);
    }
    
    public function testNotification()
    {
        $user = Auth::user();
        
        Notification::create([
            'title' => 'Test Notification',
            'message' => 'This is a test notification for debugging',
            'type' => 'test',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'data' => json_encode(['test' => true])
        ]);
        
        return response()->json(['success' => true]);
    }
}