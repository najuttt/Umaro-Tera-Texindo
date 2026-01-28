<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Config;

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        Config::$serverKey = config('services.midtrans.server_key');

        $signature = hash(
            'sha512',
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            config('services.midtrans.server_key')
        );

        if ($signature !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::where('order_code', $request->order_id)->first();
        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        switch ($request->transaction_status) {
            case 'capture':
            case 'settlement':
                $order->update(['status' => 'paid']);
                break;

            case 'expire':
                $order->update(['status' => 'expired']);
                break;

            case 'cancel':
            case 'deny':
                $order->update(['status' => 'failed']);
                break;
        }

        return response()->json(['message' => 'Callback processed']);
    }
}
