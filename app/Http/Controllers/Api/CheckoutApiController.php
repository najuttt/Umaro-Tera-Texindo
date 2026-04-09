<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Guest_carts;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutApiController extends Controller
{
    // 🔥 DEVICE ID
    private function getDeviceId(Request $request)
    {
        return $request->header('device_id') ?? $request->ip();
    }

    // 🔥 GET CART (LOGIN / GUEST)
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

    // =========================================================
    // 💳 MIDTRANS (LOGIN WAJIB)
    // =========================================================
    public function midtrans(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Harus login dulu'
            ], 401);
        }

        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
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

            // 🔥 HITUNG TOTAL + VALIDASI STOCK
            $total = 0;

            foreach ($cart->guestCartItems as $cartItem) {

                $item = Item::lockForUpdate()->find($cartItem->item_id);

                if (!$item || $cartItem->quantity > $item->stock) {
                    throw new \Exception("Stok {$item->name} tidak cukup");
                }

                $total += $item->price * $cartItem->quantity;
            }

            // 🔥 MIDTRANS CONFIG
            Config::$serverKey    = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized  = true;
            Config::$is3ds        = true;

            // 🔥 CREATE ORDER
            $order = Order::create([
                'order_code'       => Order::generateOrderCode(),
                'user_id'          => Auth::id(),
                'customer_name'    => $request->customer_name,
                'customer_phone'   => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'total_price'      => $total,
                'status'           => 'pending',
                'payment_method'   => 'midtrans',
            ]);

            $itemDetails = [];

            // 🔥 SAVE ITEM + KURANGI STOCK
            foreach ($cart->guestCartItems as $cartItem) {

                $item = Item::lockForUpdate()->find($cartItem->item_id);

                $item->decrement('stock', $cartItem->quantity);

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id'  => $cartItem->item_id,
                    'quantity' => $cartItem->quantity,
                ]);

                $itemDetails[] = [
                    'id'       => $item->id,
                    'price'    => (int) $item->price,
                    'quantity' => $cartItem->quantity,
                    'name'     => $item->name,
                ];
            }

            // 🔥 SNAP TOKEN
            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id'     => $order->order_code,
                    'gross_amount' => (int) $total,
                ],
                'customer_details' => [
                    'first_name' => $request->customer_name,
                    'phone'      => $request->customer_phone,
                ],
                'item_details' => $itemDetails,
            ]);

            // 🔥 SIMPAN SNAP TOKEN
            $order->update([
                'payment_reference' => $snapToken
            ]);

            // 🔥 CLEAR CART
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

    // =========================================================
    // 📱 WHATSAPP (GUEST & LOGIN)
    // =========================================================
    public function whatsapp(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
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

            $total = 0;
            $messageItems = "";

            // 🔥 VALIDASI + HITUNG TOTAL
            foreach ($cart->guestCartItems as $cartItem) {

                $item = Item::lockForUpdate()->find($cartItem->item_id);

                if (!$item || $cartItem->quantity > $item->stock) {
                    throw new \Exception("Stok {$item->name} tidak cukup");
                }

                $total += $item->price * $cartItem->quantity;
            }

            // 🔥 CREATE ORDER
            $order = Order::create([
                'order_code'       => Order::generateOrderCode(),
                'user_id'          => Auth::id(),
                'customer_name'    => $request->customer_name,
                'customer_phone'   => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'total_price'      => $total,
                'status'           => 'pending',
                'payment_method'   => 'whatsapp',
            ]);

            // 🔥 SAVE ITEM + KURANGI STOCK
            foreach ($cart->guestCartItems as $cartItem) {

                $item = Item::lockForUpdate()->find($cartItem->item_id);

                $item->decrement('stock', $cartItem->quantity);

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id'  => $cartItem->item_id,
                    'quantity' => $cartItem->quantity,
                ]);

                $messageItems .= "- {$item->name} x{$cartItem->quantity}\n";
            }

            // 🔥 FORMAT PESAN WA
            $message = "Halo, saya mau order:\n\n";
            $message .= $messageItems;
            $message .= "\nTotal: Rp " . number_format($total, 0, ',', '.');
            $message .= "\nKode Order: {$order->order_code}";

            $adminNumber = env('WA_ADMIN', '6282128366815');
            $waUrl = "https://wa.me/{$adminNumber}?text=" . urlencode($message);

            // 🔥 CLEAR CART
            $cart->guestCartItems()->delete();
            $cart->update(['is_locked' => true]);

            DB::commit();

            return response()->json([
                'success'    => true,
                'url'        => $waUrl,
                'order_code' => $order->order_code
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
}