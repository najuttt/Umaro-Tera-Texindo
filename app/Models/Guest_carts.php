<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guest_carts extends Model
{
    use HasFactory;

    protected $fillable = ['session_id'];

    public function guestCartItems()
    {
        return $this->hasMany(Guest_carts_item::class, 'guest_cart_id');
    }
}
