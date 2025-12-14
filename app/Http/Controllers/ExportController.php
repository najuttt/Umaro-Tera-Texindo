<?php

namespace App\Http\Controllers;

use App\Models\Item_in;
use App\Models\Item_out;
use App\Models\ExportLog;
use App\Models\Guest_carts_item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExportController extends Controller
{
    private function calculateEndDate($startDate, $period)
    {
        if (!$startDate) return null;
        $start = Carbon::parse($startDate);

        return match ($period) {
            'weekly'  => $start->copy()->addWeek()->format('Y-m-d'),
            'monthly' => $start->copy()->addMonth()->format('Y-m-d'),
            'yearly'  => $start->copy()->addYear()->format('Y-m-d'),
            default   => $start->copy()->format('Y-m-d'),
        };
    }

    private function filterByDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59'
            ]);
        }
        return $query;
    }

    public function index(Request $request)
    {
        $items = collect();
        $logs  = ExportLog::orderBy('created_at', 'desc')->get();

        $startDate = $request->query('start_date');
        $period    = $request->query('period', 'weekly');
        $type      = $request->query('type', 'masuk');
        $format    = $request->query('format', 'excel');

        $endDate = $this->calculateEndDate($startDate, $period);

        if ($startDate && $endDate) {
            if ($type === 'masuk') {
                $items = Item_in::with('item.unit', 'supplier')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($row) {
                        $row->total_price = ($row->item->price ?? 0) * ($row->quantity ?? 0);
                        return $row;
                    });
            }

            elseif ($type === 'keluar') {
                $pegawaiItems = Item_out::with(['item.unit', 'cart.user'])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($row) {
                        $row->role        = 'Pegawai';
                        $row->dikeluarkan = 'Petugas Gudang';
                        $row->penerima    = $row->cart->user->name ?? '-';
                        $row->total_price = ($row->item->price ?? 0) * ($row->quantity ?? 0);
                        return $row;
                    });

                $guestItems = Guest_carts_item::with(['item.unit', 'guestCart.guest'])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($row) {
                        $row->role        = 'Tamu';
                        $row->dikeluarkan = 'Petugas Gudang';
                        $row->penerima = $row->guestCart->guest->name ?? ($row->guest_name ?? 'Tamu');
                        $row->total_price = ($row->item->price ?? 0) * ($row->quantity ?? 0);
                        return $row;
                    });

                $items = $pegawaiItems->concat($guestItems)
                    ->sortByDesc('created_at')
                    ->values();
            }

            // NOTE: Reject branch DIHAPUS sesuai permintaan.

            elseif ($type === 'order') {
                // Ambil model Order (sesuaikan relasi & fields di project lo)
                $orders = Order::with('orderItems.item')
                    ->where('status', 'approved')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($row) {
                        $row->total_qty = $row->orderItems->sum('quantity') ?? 0;
                        $row->total_sale = $row->orderItems->sum(function($it){ return ($it->price ?? 0) * ($it->quantity ?? 0);});
                        return $row;
                    });

                $items = $orders;
            }

            // all: combine masuk + keluar + order (jika butuh)
            elseif ($type === 'all') {
                $barangMasuk = Item_in::with('item.unit', 'supplier')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($row) {
                        $row->role        = 'Supplier';
                        $row->dikeluarkan = $row->supplier->name ?? '-';
                        $row->penerima    = '-';
                        $row->total_price = ($row->item->price ?? 0) * ($row->quantity ?? 0);
                        return $row;
                    });

                $pegawaiItems = Item_out::with(['item.unit', 'approver'])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($row) {
                        $row->role        = 'Pegawai';
                        $row->dikeluarkan = $row->approver->name ?? 'Petugas Gudang';
                        $row->penerima    = $row->approver->name ?? '-';
                        $row->total_price = ($row->item->price ?? 0) * ($row->quantity ?? 0);
                        return $row;
                    });

                $items = $barangMasuk->concat($pegawaiItems)->sortByDesc('created_at')->values();
            }
        }

        // NOTE: KopSurat dihapus — tidak dikirim ke view
        return view('role.super_admin.exports.index', compact(
            'items', 'logs', 'period', 'startDate', 'endDate', 'format', 'type'
        ));
    }


    public function download(Request $request)
    {
        // Kop surat tidak lagi diperlukan — langsung proses
        $startDate = $request->query('start_date');
        $period    = $request->query('period', 'weekly');
        $type      = $request->query('type', 'masuk');
        $format    = $request->query('format', 'excel');
        $endDate   = $this->calculateEndDate($startDate, $period);
        $periodeText = "{$startDate} s/d {$endDate}";

        // Re-use index logic to build $items
        $controllerData = $this->index($request)->getData();
        $items = $controllerData['items'] ?? collect();

        if ($type === 'order') {
            $totalJumlah = $items->sum('total_qty');
            $grandTotal  = $items->sum('total_sale');
        } else {
            $totalJumlah = $items->sum('quantity');
            $grandTotal  = $items->sum('total_price');
        }

        $fileName    = "barang_{$type}_{$startDate}_to_{$endDate}_" . now()->format('Ymd_His');

        ExportLog::create([
            'super_admin_id' => Auth::id(),
            'type'           => $period,
            'data_type'      => $type,
            'format'         => $format,
            'file_path'      => "role/super_admin/exports/{$fileName}.{$format}",
            'period'         => $periodeText,
        ]);

        // ===== EXCEL (CSV fallback) =====
        if ($format === 'excel') {
            // Buat CSV dari collection supaya user bisa buka di Excel
            $headers = [];
            $rows = [];

            if ($type === 'masuk') {
                $headers = ['No','Nama Barang','Supplier','Tanggal Masuk','Jumlah','Satuan','Harga Satuan','Total Harga'];
                foreach ($items as $i => $row) {
                    $rows[] = [
                        $i+1,
                        $row->item->name ?? '-',
                        $row->supplier->name ?? '-',
                        optional($row->created_at)->format('d-m-Y H:i'),
                        $row->quantity,
                        $row->item->unit->name ?? '-',
                        $row->item->price ?? 0,
                        $row->total_price ?? 0
                    ];
                }
            } elseif ($type === 'order') {
                $headers = ['No','Nama Barang','Tanggal Order','Pemesan','Total Qty','Total Harga'];
                foreach ($items as $i => $row) {
                    $rows[] = [
                        $i+1,
                        $row->orderItems->pluck('item.name')->join(', '),
                        optional($row->created_at)->format('d-m-Y H:i'),
                        $row->customer_name ?? '-',
                        $row->total_qty ?? 0,
                        $row->total_sale ?? 0
                    ];
                }
            } elseif ($type === 'pembukuan') {
                // Placeholder headers; nanti update sesuai style pembukuan
                $headers = ['No','Keterangan','Tanggal','Debit','Kredit','Saldo'];
                // rows kosong untuk saat ini
            } else {
                $headers = ['No','Keterangan']; // fallback
            }

            // create CSV in memory
            $fp = fopen('php://temp', 'r+');
            fputcsv($fp, $headers);
            foreach ($rows as $r) {
                fputcsv($fp, $r);
            }
            rewind($fp);
            $csv = stream_get_contents($fp);
            fclose($fp);

            $responseHeaders = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$fileName}.csv\"",
            ];

            return response($csv, 200, $responseHeaders);
        }

        // ===== PDF =====
        $options = [
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true
        ];

        if ($type === 'masuk') {
            $pdf = Pdf::loadView('role.super_admin.exports.barang_masuk_pdf', compact(
                'items','startDate','endDate','periodeText','totalJumlah','grandTotal'
            ))->setOptions($options);
        } elseif ($type === 'keluar') {
            $pdf = Pdf::loadView('role.super_admin.exports.barang_keluar_pdf', compact(
                'items','startDate','endDate','periodeText','totalJumlah','grandTotal'
            ))->setOptions($options);
        } elseif ($type === 'order') {
            $pdf = Pdf::loadView('role.super_admin.exports.order_pdf', compact(
                'items','startDate','endDate','periodeText','totalJumlah','grandTotal'
            ))->setOptions($options);
        } else { // pembukuan (atau fallback)
            $pdf = Pdf::loadView('role.super_admin.exports.pembukuan_pdf', compact(
                'items','startDate','endDate','periodeText','totalJumlah','grandTotal'
            ))->setOptions($options);
        }

        return $pdf->setPaper('a4', 'landscape')->download("{$fileName}.pdf");
    }

    public function clearLogs()
    {
        ExportLog::truncate();
        return redirect()->route('super_admin.export.index')
            ->with('success', 'Riwayat export berhasil dibersihkan.');
    }

    // ========================= ADMIN / EXTRA EXPORTS =========================


    // --- Stubs untuk order & pembukuan exports (route references)
    public function exportOrderExcel(Request $request)
    {
        // Simple wrapper: arahkan ke download dengan format=excel,type=order
        $query = array_merge($request->query(), ['format' => 'excel', 'type' => 'order']);
        return redirect()->route('super_admin.export.download', $query);
    }

    public function exportOrderPdf(Request $request)
    {
        $query = array_merge($request->query(), ['format' => 'pdf', 'type' => 'order']);
        return redirect()->route('super_admin.export.download', $query);
    }

    public function exportBarangMasukExcel(Request $request)
    {
        $query = array_merge($request->query(), ['format' => 'excel', 'type' => 'masuk']);
        return redirect()->route('super_admin.export.download', $query);
    }

    public function exportBarangMasukPdf(Request $request)
    {
        $query = array_merge($request->query(), ['format' => 'pdf', 'type' => 'masuk']);
        return redirect()->route('super_admin.export.download', $query);
    }

    // Pembukuan routes (stub) — nanti isi sesuai style pembukuan yang lo kirim
    public function exportPembukuanExcel(Request $request)
    {
        $query = array_merge($request->query(), ['format' => 'excel', 'type' => 'pembukuan']);
        return redirect()->route('super_admin.export.download', $query);
    }

    public function exportPembukuanPdf(Request $request)
    {
        $query = array_merge($request->query(), ['format' => 'pdf', 'type' => 'pembukuan']);
        return redirect()->route('super_admin.export.download', $query);
    }
}
