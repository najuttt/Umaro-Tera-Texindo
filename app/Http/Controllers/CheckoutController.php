<?php

namespace App\Http\Controllers;

use App\Models\Order;
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
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Login terlebih dahulu'], 401);
        }

        // ✅ CARI CART BY USER ATAU SESSION
        $cart = Guest_carts::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('session_id', session('guest_session_id') ?? session()->getId());
            })
            ->where('is_locked', false)
            ->with('guestCartItems.item')
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

            $order = Order::create([
                'order_code'       => $orderCode,
                'user_id'          => $user->id,
                'customer_name'    => $user->name,
                'customer_phone'   => $user->phone ?? '-',        
                'customer_address' => $user->address ?? '-',      
                'total_price'      => $total,
                'status'           => 'pending',
                'payment_method'   => 'midtrans',
            ]);

            foreach ($cart->guestCartItems as $item) {
                $order->items()->create([
                    'item_id'  => $item->item_id,
                    'quantity' => $item->quantity,
                    'price'    => $item->item->price
                ]);
            }

            // SNAP TOKEN
            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id'      => $order->order_code,
                    'gross_amount'  => (int) $order->total_price,
                ],
                'customer_details' => [
                    'first_name' => $user->name ?? 'User',
                    'email'      => $user->email,
                ],
            ]);

            // KUNCI CART BIAR GA DOBEL ORDER
            $cart->update(['is_locked' => true]);

            DB::commit();

            return response()->json([
                'snap_token' => $snapToken
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