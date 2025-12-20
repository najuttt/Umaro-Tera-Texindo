<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RefundItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_request_id',
        'item_id',
        'qty',
    ];

    public function refundRequest()
    {
        return $this->belongsTo(RefundRequest::class, 'refund_request_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
