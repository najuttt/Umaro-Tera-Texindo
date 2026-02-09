<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class OrderHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        // ✅ FILTER BERDASARKAN STATUS (DEFAULT: ALL)
        $statusFilter = $request->get('status', 'all');

        $query = Order::with(['orderItems.item'])
            ->where('user_id', $user->id)
            ->where('payment_method', 'midtrans')
            ->whereNull('deleted_at');

        // ✅ APPLY FILTER
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        } else {
            $query->whereIn('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled']);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        // ✅ HITUNG JUMLAH PER STATUS (UNTUK BADGE)
        $counts = [
            'all' => Order::where('user_id', $user->id)
                ->where('payment_method', 'midtrans')
                ->whereNull('deleted_at')
                ->count(),
            'pending' => Order::where('user_id', $user->id)
                ->where('payment_method', 'midtrans')
                ->where('status', 'pending')
                ->whereNull('deleted_at')
                ->count(),
            'approved' => Order::where('user_id', $user->id)
                ->where('payment_method', 'midtrans')
                ->where('status', 'approved')
                ->whereNull('deleted_at')
                ->count(),
            'completed' => Order::where('user_id', $user->id)
                ->where('payment_method', 'midtrans')
                ->where('status', 'completed')
                ->whereNull('deleted_at')
                ->count(),
            'rejected' => Order::where('user_id', $user->id)
                ->where('payment_method', 'midtrans')
                ->where('status', 'rejected')
                ->whereNull('deleted_at')
                ->count(),
        ];

        // ✅ AMBIL ORDER (TERMASUK PENDING, TAPI EXCLUDE YANG SUDAH DIHAPUS)
        $orders = Order::with(['orderItems.item'])
            ->where('user_id', $user->id)
            ->where('payment_method', 'midtrans')
            ->whereIn('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])
            // ✅ HANYA YANG BELUM DI-SOFT DELETE
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('order-history.index', compact('orders', 'counts', 'statusFilter'));
    }

    // ✅ SOFT DELETE ORDER (BATALKAN PESANAN)
    public function cancelOrder($orderId)
    {
        $order = Order::findOrFail($orderId);

        // ✅ CEK OWNERSHIP
        if ($order->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke order ini');
        }

        // ✅ HANYA PENDING YANG BISA DIBATALKAN
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya order dengan status pending yang bisa dibatalkan');
        }

        // ✅ SOFT DELETE
        $order->delete();

        return redirect()->route('order.history')->with('success', 'Pesanan berhasil dibatalkan');
    }

    // ✅ FUNCTION UNTUK GENERATE PESAN WA REFUND
    public function getRefundWhatsAppUrl($orderId)
    {
        $order = Order::with('orderItems.item')->findOrFail($orderId);

        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // ✅ FORMAT PESAN BERBEDA UNTUK REJECTED VS APPROVED/COMPLETED
        if ($order->status === 'rejected') {
            // PESAN REFUND
            $text  = "*Permintaan Refund*\n\n";
            $text .= "*Kode Order:* {$order->order_code}\n";
            $text .= "*Nama:* {$order->customer_name}\n";
            $text .= "*No HP:* {$order->customer_phone}\n";
            $text .= "*Total:* Rp " . number_format($order->total_price, 0, ',', '.') . "\n\n";
            $text .= "*Alasan Penolakan:*\n";
            $text .= $order->admin_notes ?? 'Tidak ada keterangan';
            $text .= "\n\n_Mohon bantuannya untuk proses refund._";
        } else {
            // PESAN LAPORAN KERUSAKAN
            $text  = "*Laporan Barang Rusak/Cacat*\n\n";
            $text .= "*Kode Order:* {$order->order_code}\n";
            $text .= "*Nama:* {$order->customer_name}\n";
            $text .= "*No HP:* {$order->customer_phone}\n";
            $text .= "*Total:* Rp " . number_format($order->total_price, 0, ',', '.') . "\n\n";
            $text .= "*Deskripsi Masalah:*\n";
            $text .= "[Jelaskan kondisi barang yang rusak/cacat]\n\n";
            $text .= "_Lampirkan foto barang untuk proses lebih cepat._";
        }

        $adminNumber = "6282128366815";

        return redirect("https://wa.me/{$adminNumber}?text=" . urlencode($text));
    }
}