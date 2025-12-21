<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class ManualOrderController extends Controller
{
    public function create()
    {
        $items = Item::where('stock', '>', 0)->get();
        return view('role.admin.manual_order.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function() use ($request) {
            // buat order pending dulu
            $order = Order::create([
                'order_code'       => Order::generateOrderCode(),
                'customer_name'    => $request->customer_name,
                'customer_phone'   => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'status'           => 'pending',
            ]);

            // catat order_items tapi stock belum dikurangi
            foreach ($request->items as $orderItem) {
                $item = Item::findOrFail($orderItem['id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id'  => $item->id,
                    'quantity' => $orderItem['quantity'],
                ]);
            }
        });

        return redirect()->route('admin.manual-order.create')
            ->with('success', 'Order berhasil dibuat dan menunggu approval.');
    }

    // APPROVE order → baru kurangi stock
    public function approve($orderId)
    {
        $order = Order::with('orderItems.item')->findOrFail($orderId);

        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Order sudah diproses.']);
        }

        DB::transaction(function() use ($order) {
            // cek stock dan kurangi
            foreach ($order->orderItems as $orderItem) {
                $item = Item::lockForUpdate()->find($orderItem->item_id);

                if ($orderItem->quantity > $item->stock) {
                    throw new \Exception("Stok {$item->name} tidak cukup untuk approve order.");
                }

                $item->decrement('stock', $orderItem->quantity);
            }

            $order->update(['status' => 'approved']);
        });

        return response()->json(['success' => true]);
    }

    // REJECT order → status rejected, stock tidak berubah
    public function reject($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Order sudah diproses.']);
        }

        $order->update(['status' => 'rejected']);

        return response()->json(['success' => true]);
    }
}