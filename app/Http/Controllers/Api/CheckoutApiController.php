<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Guest_carts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutApiController extends Controller
{
    private function getDeviceId(Request $request)
    {
        return $request->header('device_id') ?? $request->ip();
    }

    private function getCart(Request $request)
    {
        if (Auth::check()) {
            return Guest_carts::with('guestCartItems.item')
                ->where('user_id', Auth::id())
                ->where('is_locked', false)
                ->first();
        } else {
            return Guest_carts::with('guestCartItems.item')
                ->where('device_id', $this->getDeviceId($request))
                ->where('is_locked', false)
                ->first();
        }
    }

    // 💳 MIDTRANS (WAJIB LOGIN)
    public function midtrans(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Harus login dulu untuk pembayaran'
            ], 401);
        }

        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $cart = $this->getCart($request);

        if (!$cart || $cart->guestCartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart kosong'
            ], 400);
        }

        $total = $cart->guestCartItems->sum(
            fn($i) => (int)$i->item->price * $i->quantity
        );

        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        DB::beginTransaction();

        try {

            $order = Order::create([
                'order_code'       => Order::generateOrderCode(),
                'user_id'          => $user->id,
                'customer_name'    => $request->customer_name,
                'customer_phone'   => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'total_price'      => $total,
                'status'           => 'pending',
                'payment_method'   => 'midtrans',
            ]);

            foreach ($cart->guestCartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id'  => $cartItem->item_id,
                    'quantity' => $cartItem->quantity,
                ]);
            }

            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id'     => $order->order_code,
                    'gross_amount' => (int) $total,
                ],
            ]);

            $cart->guestCartItems()->delete();
            $cart->update(['is_locked' => true]);

            DB::commit();

            return response()->json([
                'success'     => true,
                'snap_token'  => $snapToken,
                'order_code'  => $order->order_code,
                'total_price' => $total
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Checkout gagal',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // 📱 WHATSAPP (TANPA LOGIN)
    public function whatsapp(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required',
            'customer_phone'   => 'required',
            'customer_address' => 'required',
        ]);

        $cart = $this->getCart($request);

        if (!$cart || $cart->guestCartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart kosong'
            ], 400);
        }

        DB::beginTransaction();

        try {

            $total = $cart->guestCartItems->sum(
                fn($i) => (int)$i->item->price * $i->quantity
            );

            $order = Order::create([
                'order_code'       => Order::generateOrderCode(),
                'user_id'          => Auth::id(), // bisa null
                'customer_name'    => $request->customer_name,
                'customer_phone'   => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'total_price'      => $total,
                'status'           => 'pending',
                'payment_method'   => 'whatsapp',
            ]);

            foreach ($cart->guestCartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id'  => $cartItem->item_id,
                    'quantity' => $cartItem->quantity,
                ]);
            }

            $cart->guestCartItems()->delete();
            $cart->update(['is_locked' => true]);

            DB::commit();

            return response()->json([
                'success'    => true,
                'order_code' => $order->order_code,
                'wa_url'     => "https://wa.me/6282128366815?text=Order%20{$order->order_code}"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Checkout gagal'
            ], 500);
        }
    }
}