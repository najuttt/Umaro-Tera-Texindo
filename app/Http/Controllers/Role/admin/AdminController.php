<?php

namespace App\Http\Controllers\Role\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('role.admin.dashboard', [
            'totalOrder'      => Order::count(),
            'pendingOrder'    => Order::where('status', 'pending')->count(),
            'completedOrder'  => Order::where('status', 'approved')->count(),
            'latestOrders' => Order::where('created_at', '>=', now()->subDay())
                       ->latest()
                       ->limit(6)
                       ->get(),
        ]);
    }

}
