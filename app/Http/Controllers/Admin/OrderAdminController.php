<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Midtrans\Config;
use Midtrans\Transaction;

class OrderAdminController extends Controller
{
    public function __construct()
    {
        // 🔐 Midtrans config global
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    // ===============================
    //      LIST ORDER PENDING
    // ===============================
    public function index()
    {
        $orders = Order::where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('role.admin.orders.index', compact('orders'));
    }

    // ===============================
    //      DETAIL ORDER
    // ===============================
    public function show(Order $order)
    {
        $order->load('orderItems.item');
        return view('role.admin.orders.show', compact('order'));
    }

    // ===============================
    //      APPROVE ORDER
    // ===============================
    public function approve(Order $order)
    {
        // ✅ ubah status
        $order->update(['status' => 'approved']);

        // optional: kirim notif ke user / email / WA
        // Notification::send($order->user, new OrderApproved($order));

        return response()->json([
            'success' => true,
            'status' => 'approved'
        ]);
    }

    // ===============================
    //      REJECT ORDER + REFUND
    // ===============================
    public function reject(Order $order)
    {
        // hanya refund kalau ada transaksi midtrans
        if ($order->midtrans_transaction_id) {
            try {
                Transaction::refund($order->midtrans_transaction_id, $order->total_amount);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal refund: ' . $e->getMessage()
                ], 500);
            }
        }

        $order->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'status' => 'rejected'
        ]);
    }

}