<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guest_carts;
use App\Models\Guest_carts_item;
use Illuminate\Support\Facades\Auth;

class CartApiController extends Controller
{
    // 🔥 ambil device_id
    private function getDeviceId(Request $request)
    {
        return $request->header('device_id') ?? $request->ip();
    }

    // 🔥 ambil cart (SMART)
    private function getCart(Request $request)
    {
        if (Auth::check()) {
            return Guest_carts::firstOrCreate([
                'user_id' => Auth::id(),
                'is_locked' => false
            ]);
        } else {
            return Guest_carts::firstOrCreate([
                'device_id' => $this->getDeviceId($request),
                'is_locked' => false
            ]);
        }
    }

    /**
     * GET CART
     */
    public function index(Request $request)
    {
        $cart = $this->getCart($request)->load('guestCartItems.item');

        $items = $cart->guestCartItems->map(function ($cartItem) {
            return [
                'cart_item_id' => $cartItem->id,
                'product_id'   => $cartItem->item->id,
                'name'         => $cartItem->item->name,
                'price'        => (int) $cartItem->item->price,
                'quantity'     => $cartItem->quantity,
                'subtotal'     => (int) $cartItem->item->price * $cartItem->quantity,
                'image'        => $cartItem->item->image
                    ? asset('storage/' . $cartItem->item->image)
                    : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $items,
            'total_price' => $items->sum('subtotal')
        ]);
    }

    /**
     * ADD CART
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:items,id',
            'quantity'   => 'required|integer|min:1'
        ]);

        $cart = $this->getCart($request);

        $existing = Guest_carts_item::where('guest_cart_id', $cart->id)
            ->where('item_id', $request->product_id)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $request->quantity);
        } else {
            Guest_carts_item::create([
                'guest_cart_id' => $cart->id,
                'item_id'       => $request->product_id,
                'quantity'      => $request->quantity,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Produk ditambahkan ke cart'
        ]);
    }

    /**
     * UPDATE CART
     */
    public function update(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:guest_carts_items,id',
            'quantity'     => 'required|integer|min:1'
        ]);

        $cart = $this->getCart($request);

        $cartItem = Guest_carts_item::where('id', $request->cart_item_id)
            ->where('guest_cart_id', $cart->id)
            ->firstOrFail();

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cart diperbarui'
        ]);
    }

    /**
     * DELETE CART
     */
    public function delete(Request $request, $id)
    {
        $cart = $this->getCart($request);

        $cartItem = Guest_carts_item::where('id', $id)
            ->where('guest_cart_id', $cart->id)
            ->firstOrFail();

        $cartItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item dihapus dari cart'
        ]);
    }
}