<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // ✅ TAMBAHKAN
use App\Models\Guest_carts;
use App\Models\Guest_carts_item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
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

        return view('product.index', [
            'categories' => $categories,
            'items'      => $items,
        ]);
    }

    // ==========================
    // ADD TO CART (SUPPORT USER & GUEST)
    // ==========================
    public function addToGuestCart(Request $request)
    {
        $request->validate([
            'item_id'  => 'required|exists:items,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $qty  = intval($request->quantity ?? 1);
        $item = Item::findOrFail($request->item_id);

        // ✅ CARI/BUAT CART: USER DULU, BARU SESSION
        $user = Auth::user();
        
        if ($user) {
            // ✅ JANGAN AMBIL CART YANG UDAH LOCKED!
            $cart = Guest_carts::where('user_id', $user->id)
                ->where('is_locked', false)
                ->first();
                
            // ✅ KALAU GA ADA CART AKTIF, BIKIN BARU
            if (!$cart) {
                $cart = Guest_carts::create([
                    'user_id' => $user->id,
                    'is_locked' => false
                ]);
            }
        } else {
            $sessionId = session('guest_session_id') ?? session()->getId();
            
            // ✅ JANGAN AMBIL CART YANG UDAH LOCKED!
            $cart = Guest_carts::where('session_id', $sessionId)
                ->where('is_locked', false)
                ->first();
                
            // ✅ KALAU GA ADA CART AKTIF, BIKIN BARU
            if (!$cart) {
                $cart = Guest_carts::create([
                    'session_id' => $sessionId,
                    'is_locked' => false
                ]);
            }
        }

        $cartItem = $cart->guestCartItems()
            ->where('item_id', $item->id)
            ->first();

        $newQty = ($cartItem->quantity ?? 0) + $qty;

        if ($newQty > $item->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi'
            ], 422);
        }

        if ($cartItem) {
            $cartItem->update(['quantity' => $newQty]);
        } else {
            $cart->guestCartItems()->create([
                'item_id'  => $item->id,
                'quantity' => $qty
            ]);
        }

        return response()->json([
            'success'     => true,
            'cart_items' => $cart->guestCartItems()->with('item')->get(),
            'cart_count' => $cart->guestCartItems()->sum('quantity')
        ]);
    }

    // ==========================
    // UPDATE CART (SUPPORT USER & GUEST)
    // ==========================
    public function updateGuestCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id'  => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $item = Item::findOrFail($request->item_id);

        if ($request->quantity > $item->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah melebihi stok'
            ], 422);
        }

        // ✅ CARI CART: USER ATAU SESSION (JANGAN YANG LOCKED!)
        $user = Auth::user();
        
        if ($user) {
            $cart = Guest_carts::where('user_id', $user->id)
                ->where('is_locked', false)
                ->first();
        } else {
            $sessionId = session('guest_session_id') ?? session()->getId();
            $cart = Guest_carts::where('session_id', $sessionId)
                ->where('is_locked', false)
                ->first();
        }

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang tidak ditemukan'
            ], 404);
        }

        $cartItem = $cart->guestCartItems()
            ->where('item_id', $item->id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ada di keranjang'
            ], 404);
        }

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'success'     => true,
            'cart_items' => $cart->guestCartItems()->with('item')->get(),
            'cart_count' => $cart->guestCartItems()->sum('quantity')
        ]);
    }

    // ✅ HAPUS METHOD getGuestSession() KARENA UDAH GA DIPAKE

    // ==========================
    // GET CART (SUPPORT USER & GUEST)
    // ==========================
    public function getGuestCart(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            // ✅ JANGAN AMBIL CART YANG UDAH LOCKED!
            $cart = Guest_carts::with('guestCartItems.item')
                ->where('user_id', $user->id)
                ->where('is_locked', false)
                ->first();
                
            // ✅ KALAU GA ADA CART AKTIF, BIKIN BARU
            if (!$cart) {
                $cart = Guest_carts::create([
                    'user_id' => $user->id,
                    'is_locked' => false
                ]);
            }
        } else {
            $sessionId = session('guest_session_id') ?? session()->getId();
            
            // ✅ JANGAN AMBIL CART YANG UDAH LOCKED!
            $cart = Guest_carts::with('guestCartItems.item')
                ->where('session_id', $sessionId)
                ->where('is_locked', false)
                ->first();
                
            // ✅ KALAU GA ADA CART AKTIF, BIKIN BARU
            if (!$cart) {
                $cart = Guest_carts::create([
                    'session_id' => $sessionId,
                    'is_locked' => false
                ]);
            }
        }

        return response()->json([
            'cart_items' => $cart->guestCartItems->map(function ($ci) {
                return [
                    'item_id'  => $ci->item_id,
                    'name'     => $ci->item->name,
                    'price'    => $ci->item->price,
                    'quantity' => $ci->quantity,
                ];
            })
        ]);
    }

    // ==========================
    // DELETE CART ITEM (SUPPORT USER & GUEST)
    // ==========================
    public function deleteGuestCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id'
        ]);

        $user = Auth::user();
        
        if ($user) {
            $cart = Guest_carts::where('user_id', $user->id)
                ->where('is_locked', false)
                ->first();
        } else {
            $sessionId = session('guest_session_id') ?? session()->getId();
            $cart = Guest_carts::where('session_id', $sessionId)
                ->where('is_locked', false)
                ->first();
        }

        if (!$cart) {
            return response()->json([
                'success' => true,
                'cart_items' => []
            ]);
        }

        $cart->guestCartItems()
            ->where('item_id', $request->item_id)
            ->delete();

        return response()->json([
            'success'     => true,
            'cart_items' => $cart->guestCartItems()->with('item')->get(),
            'cart_count' => $cart->guestCartItems()->sum('quantity')
        ]);
    }

    // ==========================
    // CHECKOUT PAGE (SUPPORT USER & GUEST)
    // ==========================
public function checkoutPage(Request $request)
{
    $user = Auth::user();
    
    logger()->info('🔍 Checkout Page - User:', ['user_id' => $user?->id, 'name' => $user?->name]);
    logger()->info('🔍 Session ID:', ['guest_session_id' => session('guest_session_id'), 'session_id' => session()->getId()]);
    
    if ($user) {
        // ✅ JANGAN AMBIL CART YANG UDAH LOCKED!
        $cart = Guest_carts::with('guestCartItems.item')
            ->where('user_id', $user->id)
            ->where('is_locked', false)
            ->first();
    } else {
        $sessionId = session('guest_session_id') ?? session()->getId();
        
        // ✅ JANGAN AMBIL CART YANG UDAH LOCKED!
        $cart = Guest_carts::with('guestCartItems.item')
            ->where('session_id', $sessionId)
            ->where('is_locked', false)
            ->first();
    }

    logger()->info('🔍 Cart Found:', [
        'cart_id' => $cart?->id,
        'items_count' => $cart?->guestCartItems->count() ?? 0,
        'is_locked' => $cart?->is_locked ?? 'null'
    ]);

    if (!$cart || $cart->guestCartItems->isEmpty()) {
        logger()->warning('❌ Cart kosong atau tidak ditemukan! Redirect ke produk');
        return redirect()->route('produk')->with('error', 'Keranjang kosong');
    }

    if ($cart->is_locked) {
        logger()->warning('❌ Cart sudah di-lock! Redirect ke produk');
        return redirect()->route('produk')->with('error', 'Cart sudah diproses, silakan buat order baru');
    }

    $totalHarga = $cart->guestCartItems->sum(function ($i) {
        return $i->item->price * $i->quantity;
    });

    logger()->info('✅ Checkout Page Success', ['total' => $totalHarga]);

    session()->flash('success', 'Cart ditemukan! Total items: ' . $cart->guestCartItems->count());

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
    // CHECKOUT PROCESS (SUPPORT USER & GUEST)
    // ==========================
    public function checkoutGuestCart(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            $guestCart = Guest_carts::with('guestCartItems.item')
                ->where('user_id', $user->id)
                ->where('is_locked', false)
                ->first();
        } else {
            $sessionId = session('guest_session_id') ?? session()->getId();
            $guestCart = Guest_carts::with('guestCartItems.item')
                ->where('session_id', $sessionId)
                ->where('is_locked', false)
                ->first();
        }

        if (!$guestCart || $guestCart->guestCartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong'
            ], 400);
        }

        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($guestCart, $request, $user) {

            $totalHarga = $guestCart->guestCartItems->sum(function ($i) {
                return $i->item->price * $i->quantity;
            });

            $order = Order::create([
                'order_code'       => Order::generateOrderCode(),
                'user_id'          => $user ? $user->id : null,
                'customer_name'    => $request->customer_name,
                'customer_phone'   => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'total_price'      => $totalHarga,
                'status'           => 'pending',
                'payment_method'   => 'whatsapp',
                'payment_reference'=> null
            ]);

            foreach ($guestCart->guestCartItems as $cartItem) {
                $item = Item::lockForUpdate()->find($cartItem->item_id);

                if ($cartItem->quantity > $item->stock) {
                    throw new \Exception("Stok {$item->name} tidak cukup");
                }

                $item->decrement('stock', $cartItem->quantity);

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id'  => $item->id,
                    'quantity' => $cartItem->quantity
                ]);
            }

            session([
                'last_order_id'   => $order->id,
                'last_order_code' => $order->order_code,
            ]);

            $guestCart->guestCartItems()->delete();
            $guestCart->update(['is_locked' => true]);
            
            // ✅ GA USAH REGENERATE SESSION!
            // Cart baru otomatis dibuat pas add to cart lagi
        });

        return response()->json(['success' => true]);
    }
}