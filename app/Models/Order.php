<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_code',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'total_price',
        'status',
        'payment_method',
        'payment_reference'
    ];

    protected $dates = ['deleted_at'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function refund()
    {
        return $this->hasOne(RefundRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalQtyAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function getTotalSaleAttribute()
    {
        return $this->items->sum(function ($item) {
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