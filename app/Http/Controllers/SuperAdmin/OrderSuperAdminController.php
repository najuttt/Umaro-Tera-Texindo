<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderSuperAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        /** 🔥 WAJIB – hanya tampilkan order approved */
        $query->where('status', 'approved');

        // Searching
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($qq) use ($q) {
                $qq->where('customer_name', 'like', "%{$q}%")
                   ->orWhere('customer_phone', 'like', "%{$q}%");
            });
        }

        // History 5 hari terakhir (kalau tetap mau)
        $query->where('created_at', '>=', now()->subDays(5));

        // Filter tanggal (opsional)
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59'
            ]);
        }

        $orders = $query->latest()->paginate(25)->withQueryString();

        return view('role.super_admin.orders.index', compact('orders'));
    }
}
