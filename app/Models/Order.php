<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_name',
        'customer_phone',
        'customer_address',
        'status'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function refund()
    {
        return $this->hasOne(RefundRequest::class);
    }

    public function getTotalQtyAttribute()
    {
        return $this->orderItems->sum('quantity');
    }

    public function getTotalSaleAttribute()
    {
        return $this->orderItems->sum(function ($item) {
            return ($item->item->price ?? 0) * $item->quantity;
        });
    }

    public function isRefunded()
    {
        return in_array($this->status, ['refunded', 'partial_refund']);
    }

    public static function generateOrderCode()
    {
        $date = now()->format('Ymd');

        $lastOrder = self::whereDate('created_at', now()->toDateString())
            ->latest('id')
            ->first();

        $number = $lastOrder
            ? intval(substr($lastOrder->order_code, -4)) + 1
            : 1;

        return 'UMT-' . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}