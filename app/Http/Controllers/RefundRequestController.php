<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\RefundRequest;
use App\Models\RefundItem;
use App\Models\Item;
use App\Models\ExpenseLog;
use Illuminate\Support\Facades\DB;
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

    public function checkOrder(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string'
        ]);

        $order = Order::where('order_code', $request->order_code)
            ->with(['orderItems.item'])
            ->first();

        if (!$order) {
            return back()->with('error', 'Order tidak ditemukan');
        }

        if ($order->status !== 'approved') {
        return back()->with('error', 
            'Refund hanya dapat diajukan untuk pesanan yang sudah disetujui.'
        );
        }

        // cegah double refund untuk order yang sama
        if ($order->refund) {
            return back()->with('error', 'Order ini sudah pernah diajukan refund');
        }

        return view('refund.detail', compact('order'));
    }

    // =========================
    // SUBMIT REFUND (GUEST)
    // =========================
    public function submit(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason'   => 'nullable|string',
            'proof'    => 'required|file|max:7168|mimes:jpg,jpeg,png,mp4,mov,avi',
            'items'    => 'required|array'
        ]);

        $order = Order::with('orderItems')->findOrFail($request->order_id);

        if ($order->refund) {
            return back()->withErrors([
                'order_code' => 'Order ini sudah pernah mengajukan refund.'
            ]);
        }

        DB::transaction(function () use ($request, $order) {

            // upload bukti
            $file = $request->file('proof');
            $path = $file->store('refund_proofs', 'public');

            $type = str_starts_with($file->getMimeType(), 'video')
                ? 'video'
                : 'image';

            // create refund request
            $refund = RefundRequest::create([
                'order_id'   => $order->id,
                'reason'     => $request->reason,
                'proof_file' => $path,
                'proof_type' => $type,
                'status'     => 'pending',
            ]);

            // simpan item refund
            foreach ($request->items as $itemId => $qty) {
                $qty = (int) $qty;

                $orderItem = $order->orderItems
                    ->where('item_id', $itemId)
                    ->first();

                if (!$orderItem) {
                    continue;
                }

                if ($qty > 0 && $qty <= $orderItem->quantity) {
                    RefundItem::create([
                        'refund_request_id' => $refund->id,
                        'item_id'           => $itemId,
                        'qty'               => $qty,
                    ]);
                }
            }
        });

        return redirect()->route('refund.form')
            ->with('success', 'Refund berhasil diajukan. Menunggu proses admin.');
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
    // ADMIN: APPROVE REFUND
    // =========================
    public function approve($id)
    {
        DB::transaction(function () use ($id) {

            $refund = RefundRequest::with(['items', 'order'])
                ->findOrFail($id);

            if ($refund->status !== 'pending') {
                abort(400, 'Refund sudah diproses');
            }

            $totalRefund = 0;

            foreach ($refund->items as $refundItem) {
                $qty = (int) $refundItem->qty;

                if ($qty <= 0) {
                    continue;
                }

                $item = Item::find($refundItem->item_id);

                if ($item) {
                    // balikin stok
                    $item->increment('stock', $qty);

                    // hitung nominal refund
                    $totalRefund += ((int) $item->price * $qty);
                }
            }

            // catat ke pembukuan (uang keluar)
            if ($totalRefund > 0) {
                ExpenseLog::create([
                    'date'        => now(),
                    'description' => 'Refund Order ' . $refund->order->order_code,
                    'amount'      => $totalRefund
                ]);
            }

            // update status refund SAJA
            $refund->update(['status' => 'approved']);

            // ❌ JANGAN ubah status order
        });

        return back()->with('success', 'Refund disetujui, stok & pembukuan aman.');
    }

    // =========================
    // ADMIN: REJECT
    // =========================
    public function reject($id)
    {
        $refund = RefundRequest::findOrFail($id);

        if ($refund->status !== 'pending') {
            abort(400, 'Refund sudah diproses');
        }

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