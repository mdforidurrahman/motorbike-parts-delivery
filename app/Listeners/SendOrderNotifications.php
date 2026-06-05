<?php

namespace App\Listeners;

use App\Events\NewOrderPlaced;
use App\Models\Notification;
use App\Models\User;
use App\Models\Outlet;
use Illuminate\Support\Facades\Log;

class SendOrderNotifications
{
    public function handle(NewOrderPlaced $event)
    {
        $order = $event->order;
        $buyerArea = $order->buyerOutlet->area_id;
        
        // 1. Find contracted outlets in same area with the product
        $availableSuppliers = Outlet::where('type', 'contracted')
            ->where('area_id', $buyerArea)
            ->whereHas('products', function($query) use ($order) {
                // Assuming order items relation exists
                $query->whereIn('id', $order->items->pluck('product_id'));
            })
            ->get();
        
        // 2. Send notification to contracted outlets
        foreach ($availableSuppliers as $supplier) {
            Notification::create([
                'title' => 'New Order Available',
                'message' => "Order #{$order->order_number} needs delivery in your area. Amount: {$order->total_amount} Taka",
                'type' => 'order',
                'notifiable_type' => Outlet::class,
                'notifiable_id' => $supplier->id,
                'data' => json_encode(['order_id' => $order->id])
            ]);
            
            // Also send real-time via broadcast
            broadcast(new \App\Events\OutletNotification($supplier, $order));
        }
        
        // 3. Find available riders in same area
        $availableRiders = User::whereHas('role', function($query) {
            $query->where('name', 'rider');
        })->where('area_id', $buyerArea)
          ->where('is_active', true)
          ->get();
        
        // 4. Send notification to riders
        foreach ($availableRiders as $rider) {
            Notification::create([
                'title' => 'Delivery Opportunity',
                'message' => "New delivery available in your area. Order #{$order->order_number}. Earn 50 Taka delivery charge.",
                'type' => 'delivery',
                'notifiable_type' => User::class,
                'notifiable_id' => $rider->id,
                'data' => json_encode(['order_id' => $order->id])
            ]);
            
            broadcast(new \App\Events\RiderNotification($rider, $order));
        }
        
        Log::info("Notifications sent for order: {$order->order_number}");
    }
}