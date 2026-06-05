<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'sku', 'description', 'price', 'stock_quantity', 
        'category_id', 'outlet_id', 'image', 'is_available'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'stock_quantity' => 'integer'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isInStock()
    {
        return $this->stock_quantity > 0 && $this->is_available;
    }

    public function decreaseStock($quantity)
    {
        $this->stock_quantity -= $quantity;
        if ($this->stock_quantity <= 0) {
            $this->is_available = false;
        }
        $this->save();
    }

    public function increaseStock($quantity)
    {
        $this->stock_quantity += $quantity;
        if ($this->stock_quantity > 0 && !$this->is_available) {
            $this->is_available = true;
        }
        $this->save();
    }
}