<?php

namespace App\Http\Controllers;

use App\Models\Guest_carts;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Snap;
use Midtrans\Config;

class PaymentController extends Controller
{
   
    public function snap(Request $request)
    {
        // ✅ wajib login
        if (!Auth::check()) return response()->json(['message'=>'Login diperlukan'], 401);

        // ambil cart dari session
        $cart = Guest_carts::with('items.item')->where('session_id', session()->getId())->first();
        if(!$cart || $cart->items->isEmpty()) return response()->json(['message'=>'Cart kosong'], 422);

        // hitung total
        $grossAmount = $cart->items->sum(fn($i) => $i->item->price * $i->quantity);

        // simpan order di DB dengan status pending
        $order = Order::create([
            'user_id' => Auth::id(),
            'total' => $grossAmount,
            'status' => 'pending',
            'payment_type' => 'midtrans',
        ]);

        // config Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => $order->id,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        // simpan snap token ke order untuk callback nanti
        $order->update(['midtrans_snap_token' => $snapToken]);

        return response()->json([
            'token' => $snapToken,
            'order_id' => $order->id
        ]);
    }

    public function midtransCallback(Request $request)
    {
        $notification = new \Midtrans\Notification();

        $orderId = $notification->order_id;
        $status = $notification->transaction_status;

        $order = Order::find($orderId);

        if(!$order) return response()->json(['message'=>'Order tidak ditemukan'], 404);

        if($status == 'capture' || $status == 'settlement'){
            $order->update(['status' => 'paid', 'midtrans_transaction_id' => $notification->transaction_id]);
        } elseif($status == 'cancel' || $status == 'deny' || $status == 'expire') {
            $order->update(['status' => 'failed']);
        }

        return response()->json(['message'=>'OK']);
    }

}


