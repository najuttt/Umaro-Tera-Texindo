<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\HppLog;
use App\Models\ExpenseLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PembukuanController extends Controller
{
    // =============================
    // HALAMAN INPUT PEMBUKUAN
    // =============================
    public function index()
    {
        $hpp      = HppLog::orderBy('date', 'desc')->get();
        $expenses = ExpenseLog::orderBy('date', 'desc')->get();

        return view('role.super_admin.pembukuan.index', compact('hpp', 'expenses'));
    }

    // =============================
    // SIMPAN HPP
    // =============================
    public function storeHpp(Request $request)
    {
        $request->validate([
            'date'      => 'required|date',
            'hpp_total' => 'required|numeric',
            'note'      => 'nullable|string'
        ]);

        // Simpan data HPP dari form
        HppLog::create($request->only(['date','hpp_total','note']));

        return back()->with('success', 'HPP berhasil disimpan');
    }

    // =============================
    // SIMPAN PENGELUARAN
    // =============================
    public function storeExpense(Request $request)
    {
        $request->validate([
            'date'        => 'required|date',
            'description' => 'required|string',
            'amount'      => 'required|numeric'
        ]);

        // Simpan data pengeluaran dari form
        ExpenseLog::create($request->only(['date','description','amount']));

        return back()->with('success', 'Pengeluaran berhasil disimpan');
    }

    // =============================
    // EXPORT PDF PEMBUKUAN
    // =============================
    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date'
        ]);

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end   = Carbon::parse($request->end_date)->endOfDay();

        // Ambil order approved beserta item
        $orders = Order::where('status', 'approved')
            ->whereBetween('created_at', [$start, $end])
            ->with('orderItems.item')
            ->get();

        $salesData = [];
        $totalPenjualan = 0;

        foreach ($orders as $order) {
            foreach ($order->orderItems as $oi) {
                $price = $oi->item?->price ?? 0;
                $name  = $oi->item?->name ?? '-';
                $total = $price * $oi->quantity;

                $salesData[] = [
                    'tanggal'    => $order->created_at->format('Y-m-d'),
                    'produk'     => $name,
                    'qty'        => $oi->quantity,
                    'harga_jual' => $price,
                    'total_jual' => $total
                ];

                $totalPenjualan += $total;
            }
        }

        // Ambil HPP dan pengeluaran sesuai periode
        $totalHpp        = HppLog::whereBetween('date', [$start, $end])->sum('hpp_total');
        $expensesData    = ExpenseLog::whereBetween('date', [$start, $end])->get();
        $totalPengeluaran = $expensesData->sum('amount');

        // Hitung laba
        $totalLabaKotor = $totalPenjualan - $totalHpp;
        $labaBersih     = $totalLabaKotor - $totalPengeluaran;

        $periodeText = $start->format('d M Y').' - '.$end->format('d M Y');

        // Generate PDF
        $pdf = Pdf::loadView('role.super_admin.exports.pembukuan_pdf', [
            'salesData'        => $salesData,
            'expensesData'     => $expensesData,
            'totalPenjualan'   => $totalPenjualan,
            'totalHpp'         => $totalHpp,
            'totalPengeluaran' => $totalPengeluaran,
            'totalLabaKotor'   => $totalLabaKotor,
            'labaBersih'       => $labaBersih,
            'periodeText'      => $periodeText
        ]);

        return $pdf->download('laporan_pembukuan.pdf');
    }
}