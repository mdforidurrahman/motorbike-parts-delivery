<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'user_id',
        'order_id',
        'type',
        'amount',
        'status',
        'reference',
        'payment_method',
        'account_number',
        'bank_name',
        'account_holder',
        'branch_name',
        'note',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Helper Methods
    public function markAsPending()
    {
        $this->status = 'pending';
        $this->save();
    }
    
    public function markAsProcessing()
    {
        $this->status = 'processing';
        $this->save();
    }
    
    public function markAsCompleted($approvedBy = null)
    {
        $this->status = 'completed';
        if ($approvedBy) {
            $this->approved_by = $approvedBy;
            $this->approved_at = now();
        }
        $this->save();
    }
    
    public function markAsRejected()
    {
        $this->status = 'rejected';
        $this->save();
    }
    
    public function markAsCancelled()
    {
        $this->status = 'cancelled';
        $this->save();
    }
    
    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    public function scopeWithdrawal($query)
    {
        return $query->where('type', 'withdrawal');
    }
}