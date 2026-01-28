<?php
// app/Models/Guest_carts.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest_carts extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'is_locked'
    ];

    protected $casts = [
        'is_locked' => 'boolean' 
    ];

    public function guestCartItems()
    {
        return $this->hasMany(Guest_carts_item::class, 'guest_cart_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}