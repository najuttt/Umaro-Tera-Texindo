<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem; 
use App\Models\Guest_carts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function pay(Request $request)
    {
        // ✅ VALIDASI DATA CUSTOMER DARI FORM
        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Login terlebih dahulu'], 401);
        }

        // Ambil cart
        $cart = Guest_carts::with('guestCartItems.item')
            ->where('user_id', $user->id)
            ->where('is_locked', false)
            ->first();

        if (!$cart || $cart->guestCartItems->isEmpty()) {
            return response()->json(['message' => 'Cart kosong'], 400);
        }

        // Hitung total dari DB (ANTI MANIPULASI)
        $total = $cart->guestCartItems->sum(function ($i) {
            return $i->item->price * $i->quantity;
        });

        if ($total <= 0) {
            return response()->json(['message' => 'Total order tidak valid'], 400);
        }

        // Midtrans Config
        Config::$serverKey     = config('services.midtrans.server_key');
        Config::$isProduction  = filter_var(config('services.midtrans.is_production'), FILTER_VALIDATE_BOOLEAN);
        Config::$isSanitized   = true;
        Config::$is3ds         = true;

        DB::beginTransaction();

        try {
            // ORDER CODE AMAN (ANTI DUPLIKAT)
            $orderCode = 'ORD-' . now()->format('YmdHis') . '-' . rand(1000, 9999);

            // ✅ SIMPAN DATA CUSTOMER DARI FORM (BUKAN DARI USER PROFILE)
            $order = Order::create([
                'order_code'       => $orderCode,
                'user_id'          => $user->id,
                'customer_name'    => $request->customer_name,
                'customer_phone'   => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'total_price'      => $total,
                'status'           => 'pending',
                'payment_method'   => 'midtrans',
            ]);

            // ✅ SIMPAN ORDER ITEMS
            foreach ($cart->guestCartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id'  => $cartItem->item_id,
                    'quantity' => $cartItem->quantity,
                ]);
            }

            // SNAP TOKEN
            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id'      => $order->order_code,
                    'gross_amount'  => (int) $order->total_price,
                ],
                'customer_details' => [
                    'first_name' => $request->customer_name,
                    'email'      => $user->email,
                    'phone'      => $request->customer_phone,
                ],
            ]);

            // ✅ LOCK CART & HAPUS ITEMS
            $cart->guestCartItems()->delete();
            $cart->update(['is_locked' => true]);

            // ✅ SIMPAN ORDER ID KE SESSION (UNTUK TRACKING)
            session([
                'last_order_id'   => $order->id,
                'last_order_code' => $order->order_code,
            ]);

            DB::commit();

            // ✅ RETURN SNAP TOKEN + REDIRECT URL
            return response()->json([
                'snap_token'   => $snapToken,
                'redirect_url' => route('order.history') 
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal memproses pembayaran',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}