<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Guest_carts;
use App\Models\Guest_carts_item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Item;
use App\Models\Category;

class ProductController extends Controller
{
    // ==========================
    // PRODUK PAGE
    // ==========================
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Item::with('category');

        // SEARCH
        if ($request->q) {
            $search = $request->q;
            $query->where(function($q) use ($search){
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhereHas('category', fn($c) => 
                        $c->where('name', 'like', "%$search%")
                  );
            });
        }

        // KATEGORI FILTER
        if ($request->kategori && $request->kategori != 'none') {
            $query->whereHas('category', fn($q) => 
                $q->where('name', $request->kategori)
            );
        }

        // SORTING
        match ($request->sort) {
            'stok_terbanyak' => $query->orderBy('stock', 'desc'),
            'stok_sedikit'   => $query->orderBy('stock', 'asc'),
            'terbaru'        => $query->latest('created_at'),
            'terlama'        => $query->oldest('created_at'),
            'nama_az'        => $query->orderBy('name', 'asc'),
            'nama_za'        => $query->orderBy('name', 'desc'),
            default          => $query->orderBy('stock', 'desc')
        };

        $items = $query->paginate(12)->appends($request->all());

        // SESSION HANDLING
        $sessionId = $request->session()->get('guest_session', Str::uuid());
        $request->session()->put('guest_session', $sessionId);

        $cart = Guest_carts::with('guestCartItems.item')
            ->firstOrCreate(['session_id' => $sessionId]);

        return view('product.index', [
            'categories' => $categories,
            'items'      => $items,
            'cartCount'  => $cart->guestCartItems->sum('quantity'),
            'cartItems'  => $cart->guestCartItems
        ]);
    }

    // ==========================
    // ADD TO CART
    // ==========================
    public function addToGuestCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $qty = intval($request->quantity ?? 1);

        $sessionId = $request->session()->get('guest_session', Str::uuid());
        $request->session()->put('guest_session', $sessionId);

        $cart = Guest_carts::firstOrCreate(['session_id' => $sessionId]);

        $ci = $cart->guestCartItems()->where('item_id', $request->item_id)->first();

        if ($ci) {
            $ci->increment('quantity', $qty);
        } else {
            $cart->guestCartItems()->create([
                'item_id' => $request->item_id,
                'quantity' => $qty
            ]);
        }

        $cartItems = $cart->guestCartItems()->with('item')->get();

        return response()->json([
            'success' => true,
            'message' => 'Item ditambahkan',
            'cart_count' => $cartItems->sum('quantity'),
            'cart_items' => $cartItems->map(fn($i) => [
                'id'       => $i->item_id,
                'name'     => $i->item->name,
                'quantity' => $i->quantity
            ])
        ]);
    }

    // ==========================
    // UPDATE CART
    // ==========================
    public function updateGuestCart(Request $request)
    {
        $request->validate([
            'item_id'  => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $sessionId = $request->session()->get('guest_session');

        $cart = Guest_carts::where('session_id', $sessionId)->first();
        if (!$cart) return response()->json(['error' => 'Cart tidak ditemukan'], 404);

        $cartItem = $cart->guestCartItems()->where('item_id', $request->item_id)->first();
        if (!$cartItem) return response()->json(['error' => 'Item tidak ditemukan'], 404);

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'cart_count' => $cart->guestCartItems->sum('quantity'),
            'cart_items' => $cart->guestCartItems()->with('item')->get()
        ]);
    }

    // ==========================
    // DELETE CART ITEM
    // ==========================
    public function deleteGuestCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id'
        ]);

        $sessionId = $request->session()->get('guest_session');

        $cart = Guest_carts::where('session_id', $sessionId)->first();
        if (!$cart) return response()->json(['error' => 'Cart tidak ditemukan'], 404);

        $cart->guestCartItems()->where('item_id', $request->item_id)->delete();

        return response()->json([
            'success' => true,
            'cart_count' => $cart->guestCartItems->sum('quantity'),
            'cart_items' => $cart->guestCartItems()->with('item')->get()
        ]);
    }

    // ==========================
    // **CHECKOUT PAGE (MENAMPILKAN HALAMAN CHECKOUT)**
    // ==========================
    public function checkoutPage(Request $request)
    {
        $sessionId = $request->session()->get('guest_session');

        $cart = Guest_carts::with('guestCartItems.item')
            ->where('session_id', $sessionId)
            ->first();

        if (!$cart || $cart->guestCartItems->isEmpty()) {
            return redirect()->route('produk')->with('error', 'Keranjang kosong');
        }

        $totalHarga = $cart->guestCartItems->sum(function ($i) {
        return $i->item->price * $i->quantity;
        });

        return view('checkout.index', [
            'cart' => $cart,
            'totalHarga' => $totalHarga
        ]);
    }

   public function sendWhatsApp(Request $request)
    {
        $orderId   = session('last_order_id');
        $orderCode = session('last_order_code');

        if (!$orderId || !$orderCode) {
            return response()->json([
                'success' => false,
                'message' => 'Order belum ditemukan. Silakan checkout ulang.'
            ], 400);
        }

        $order = Order::with('orderItems.item')->find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak valid'
            ], 400);
        }

        // FORMAT PESAN
        $text  = "*Pesanan Baru dari Website*\n\n";
        $text .= "*Kode Order:* {$order->order_code}\n\n";
        $text .= "*Nama:* {$order->customer_name}\n";
        $text .= "*No HP:* {$order->customer_phone}\n";
        $text .= "*Alamat:* {$order->customer_address}\n\n";
        $text .= "*Daftar Barang:*\n";

        foreach ($order->orderItems as $oi) {
            $text .= "• {$oi->item->name} ({$oi->quantity}x)\n";
        }

        $total = $order->orderItems->sum(
            fn($i) => $i->item->price * $i->quantity
        );

        $text .= "\n*Total:* Rp " . number_format($total, 0, ',', '.');
        $text .= "\n\nSimpan *Kode Order* ini.\n";
        $text .= "Gunakan untuk refund jika ada kendala.";

        $adminNumber = "6282128366815";

        return response()->json([
            'success' => true,
            'wa_url'  => "https://wa.me/{$adminNumber}?text=" . urlencode($text)
        ]);
    }

    // ==========================
    // **CHECKOUT PROCESS**
    // ==========================
    public function checkoutGuestCart(Request $request)
    {
        $sessionId = $request->session()->get('guest_session');

        $guestCart = Guest_carts::with('guestCartItems.item')
            ->where('session_id', $sessionId)
            ->first();

        if (!$guestCart || $guestCart->guestCartItems->isEmpty()) {
            return redirect()->route('produk')->with('error', 'Keranjang kosong');
        }

        // VALIDASI
        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($guestCart, $request) {

        $order = Order::create([
            'order_code'       => Order::generateOrderCode(),
            'customer_name'    => $request->customer_name,
            'customer_phone'   => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'status'           => 'pending'
        ]);

        foreach ($guestCart->guestCartItems as $cartItem) {
            $item = Item::lockForUpdate()->find($cartItem->item_id);

            if ($cartItem->quantity > $item->stock) {
                throw new \Exception("Stok {$item->name} tidak cukup.");
            }

            $item->decrement('stock', $cartItem->quantity);

            OrderItem::create([
                'order_id' => $order->id,
                'item_id'  => $item->id,
                'quantity' => $cartItem->quantity
            ]);
        }

        // 🔥 SIMPAN KE SESSION
        session([
            'last_order_id'   => $order->id,
            'last_order_code' => $order->order_code,
        ]);

        // HAPUS CART
        $guestCart->guestCartItems()->delete();
        });

        return redirect()->route('produk')->with('success', 'Pesanan baru menunggu approval');
    }

}