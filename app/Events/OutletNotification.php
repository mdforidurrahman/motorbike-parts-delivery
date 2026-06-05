<?php

namespace App\Events;

use App\Models\Outlet;
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OutletNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $outlet;
    public $order;

    public function __construct(Outlet $outlet, Order $order)
    {
        $this->outlet = $outlet;
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return new Channel('outlet.' . $this->outlet->id);
    }

    public function broadcastWith()
    {
        return [
            'title' => 'New Order Available',
            'message' => "Order #{$this->order->order_number} needs delivery. Amount: {$this->order->total_amount} Taka",
            'order_id' => $this->order->id
        ];
    }
}