<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RefundRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'reason',
        'proof_file',
        'proof_type',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

     public function items()
    {
        return $this->hasMany(RefundItem::class);
    }
}