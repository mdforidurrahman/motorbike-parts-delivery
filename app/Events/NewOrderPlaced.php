<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderPlaced // implements ShouldBroadcast - এই লাইনটি কমেন্ট করুন
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    // broadcastOn মেথডটি কমেন্ট করুন
    // public function broadcastOn()
    // {
    //     return new Channel('orders');
    // }
}