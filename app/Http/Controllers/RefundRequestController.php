<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\RefundRequest;
use Illuminate\Support\Facades\Storage;

class RefundRequestController extends Controller
{
    // =========================
    // FORM REFUND (GUEST)
    // =========================
    public function form()
    {
        return view('refund.form');
    }

    // =========================
    // SUBMIT REFUND (GUEST)
    // =========================
    public function submit(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string',
            'reason'     => 'nullable|string',
            'proof'      => 'required|file|max:7168|mimes:jpg,jpeg,png,mp4,mov,avi',
        ]);

        // 🔍 CARI ORDER BERDASARKAN CODE
        $order = Order::where('order_code', $request->order_code)->first();

        if (!$order) {
            return back()->withErrors([
                'order_code' => 'Kode order tidak ditemukan atau tidak valid.'
            ]);
        }

        // ❌ CEK APAKAH SUDAH ADA REFUND
        if ($order->refund) {
            return back()->withErrors([
                'order_code' => 'Order ini sudah pernah mengajukan refund.'
            ]);
        }

        // 📁 SIMPAN FILE
        $file = $request->file('proof');
        $path = $file->store('refund_proofs', 'public');

        $type = str_starts_with($file->getMimeType(), 'video')
            ? 'video'
            : 'image';

        // 💾 SIMPAN KE DATABASE
        RefundRequest::create([
            'order_id'   => $order->id,
            'reason'     => $request->reason,
            'proof_file' => $path,
            'proof_type' => $type,
            'status'     => 'pending',
        ]);

        return redirect()->back()->with('success', 'Refund berhasil diajukan. Menunggu proses admin.');
    }

    // =========================
    // ADMIN: LIST REFUND
    // =========================
    public function index()
    {
        $refunds = RefundRequest::with('order')
            ->latest()
            ->get();

        return view('role.admin.refund.index', compact('refunds'));
    }

    // =========================
    // ADMIN: APPROVE
    // =========================
    public function approve($id)
    {
        $refund = RefundRequest::findOrFail($id);
        $refund->update(['status' => 'approved']);

        return back()->with('success', 'Refund disetujui.');
    }

    // =========================
    // ADMIN: REJECT
    // =========================
    public function reject($id)
    {
        $refund = RefundRequest::findOrFail($id);
        $refund->update(['status' => 'rejected']);

        return back()->with('success', 'Refund ditolak.');
    }

    // =========================
    // SUPER ADMIN: VIEW ONLY
    // =========================
    public function superAdminView()
    {
        $refunds = RefundRequest::with('order')
            ->latest()
            ->get();

        return view('role.super_admin.refund.index', compact('refunds'));
    }
}
