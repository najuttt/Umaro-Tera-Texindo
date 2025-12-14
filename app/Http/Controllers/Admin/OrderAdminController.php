<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderAdminController extends Controller
{
    public function index()
    {
        $orders = Order::where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('role.admin.orders.index', compact('orders'));
    }

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
        $order->status = 'approved';
        $order->save();

        return response()->json([
            'success' => true,
            'status' => 'approved'
        ]);
    }

    // ===============================
    //      REJECT ORDER
    // ===============================
    public function reject(Order $order)
    {
        $order->status = 'rejected';
        $order->save();

        return response()->json([
            'success' => true,
            'status' => 'rejected'
        ]);
    }
}
