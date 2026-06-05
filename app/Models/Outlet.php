<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'shop_name', 'phone', 'email', 'address', 'latitude', 'longitude',
        'area_id', 'user_id', 'type', 'is_verified', 'wallet_balance'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'wallet_balance' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // এই দোকান যেই অর্ডার করেছে (ক্রেতা হিসেবে)
    public function ordersAsBuyer()
    {
        return $this->hasMany(Order::class, 'buyer_outlet_id');
    }

    // এই দোকান যেই অর্ডার সাপ্লাই করেছে (বিক্রেতা হিসেবে)
    public function ordersAsSupplier()
    {
        return $this->hasMany(Order::class, 'supplier_outlet_id');
    }

    // সব অর্ডার একসাথে পেতে (ক্রেতা + বিক্রেতা)
    public function orders()
    {
        // ইউনিয়ন ব্যবহার করে দুটি রিলেশন একসাথে
        $buyerOrders = $this->ordersAsBuyer()->get();
        $supplierOrders = $this->ordersAsSupplier()->get();
        
        return $buyerOrders->concat($supplierOrders);
    }
    
    // অর্ডার কাউন্ট (ক্রেতা + বিক্রেতা)
    public function getOrdersCountAttribute()
    {
        return $this->ordersAsBuyer()->count() + $this->ordersAsSupplier()->count();
    }

    public function isContracted()
    {
        return $this->type === 'contracted';
    }

    public function isSmall()
    {
        return $this->type === 'small';
    }

    public function getNotifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')->latest();
    }
}