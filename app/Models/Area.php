<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'city', 'delivery_charge'];

    protected $casts = [
        'delivery_charge' => 'decimal:2'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function outlets()
    {
        return $this->hasMany(Outlet::class);
    }

    public function riders()
    {
        return $this->hasMany(User::class)->whereHas('role', function($q) {
            $q->where('name', 'rider');
        });
    }
}