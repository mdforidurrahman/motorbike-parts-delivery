<?php

namespace App\Models;

use App\Events\OrderStatusUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'buyer_outlet_id', 'supplier_outlet_id', 'rider_id',
        'total_amount', 'delivery_charge', 'commission_amount', 'status',
        'payment_status', 'delivery_address', 'delivered_at'
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'commission_amount' => 'decimal:2'
    ];

    // Remove this line if it exists
    // protected $dispatchesEvents = [
    //     'updated' => OrderStatusUpdated::class,
    // ];

    // Relationships
    public function buyerOutlet()
    {
        return $this->belongsTo(Outlet::class, 'buyer_outlet_id');
    }

    public function supplierOutlet()
    {
        return $this->belongsTo(Outlet::class, 'supplier_outlet_id');
    }

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeInDelivery($query)
    {
        return $query->whereIn('status', ['rider_assigned', 'picked_up']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'delivered');
    }

    // Helper Methods
    public function calculateCommission()
    {
        $this->commission_amount = $this->total_amount * 0.01; // 1% commission
        $this->save();
    }

    public function updateStatus($newStatus)
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;
        
        if ($newStatus === 'delivered' && $oldStatus !== 'delivered') {
            $this->delivered_at = now();
            $this->processPayment();
        }
        
        $this->save();
        
        // Trigger notification event
        event(new OrderStatusUpdated($this));
        
        return true;
    }

    private function processPayment()
    {
        // Transfer money to supplier (supplier gets total - commission)
        $supplierAmount = $this->total_amount - $this->commission_amount;
        
        if ($this->supplierOutlet) {
            $this->supplierOutlet->increment('wallet_balance', $supplierAmount);
        }
        
        // Add commission to admin
        $admin = User::whereHas('role', function($q) {
            $q->where('name', 'admin');
        })->first();
        
        if ($admin) {
            $admin->increment('wallet_balance', $this->commission_amount);
        }
        
        // Pay rider (if delivery charge exists)
        if ($this->rider_id && $this->delivery_charge > 0) {
            $this->rider->increment('wallet_balance', $this->delivery_charge);
        }
        
        $this->payment_status = 'paid';
        $this->save();
        
        return true;
    }

    public function canAccept()
    {
        return $this->status === 'pending';
    }

    public function canAssignRider()
    {
        return $this->status === 'accepted';
    }

    public function canPickup()
    {
        return $this->status === 'rider_assigned';
    }

    public function canDeliver()
    {
        return $this->status === 'picked_up';
    }
}