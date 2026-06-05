<?php

namespace App\Events;

use App\Models\User;
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RiderNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rider;
    public $order;

    public function __construct(User $rider, Order $order)
    {
        $this->rider = $rider;
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return new Channel('user.' . $this->rider->id);
    }

    public function broadcastWith()
    {
        return [
            'title' => 'New Delivery Available',
            'message' => "Order #{$this->order->order_number} needs delivery in your area. Earn {$this->order->delivery_charge} Taka delivery charge.",
            'order_id' => $this->order->id
        ];
    }
}