<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Barang Masuk</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .divider { border-top: 5px solid #000; width: 90%; margin: 5px auto 20px auto; }
        .table-container { width: 90%; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background: #f2f2f2; }
        .title { text-align: center; margin-top: 10px; }
        .footer { margin-top: 30px; font-size: 11px; text-align: right; }
        .page-number:after { content: counter(page); }
        @page { margin-top: 80px; margin-bottom: 40px; }
    </style>
</head>
<body>

    <h2 class="title">LAPORAN BARANG MASUK</h2>
    <p class="title">Periode: {{ $periodeText ?? ($startDate.' s/d '.$endDate) }}</p>

    {{-- INISIALISASI SUPAYA TIDAK ERROR --}}
    @php
        $grandTotal = $grandTotal ?? 0;
        $totalJumlah = $totalJumlah ?? 0;
    @endphp

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Supplier</th>
                    <th>Tanggal Masuk</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Harga Satuan</th>
                    <th>Total Harga</th>
                </tr>
            </thead>

            <tbody>
                @forelse($items as $i => $row)
                    @php
                        $grandTotal += $row->total_price ?? 0;
                        $totalJumlah += $row->quantity ?? 0;
                    @endphp
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $row->item->name ?? '-' }}</td>
                        <td>{{ $row->supplier->name ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y') }}</td>
                        <td>{{ $row->quantity ?? 0 }}</td>
                        <td>{{ $row->item->unit->name ?? '-' }}</td>
                        <td>Rp {{ number_format($row->item->price ?? 0, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($row->total_price ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr>
                    <th colspan="4" style="text-align:right;">Total Jumlah</th>
                    <th colspan="4">{{ number_format($totalJumlah, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <th colspan="4" style="text-align:right;">Grand Total Harga</th>
                    <th colspan="4">Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
                </tr>
            </tfoot>

        </table>
    </div>

    {{-- PAGE NUMBER & PRINT DATE --}}
    <script type="text/php">
    if (isset($pdf)) {
        $pdf->page_script('
            $font = $fontMetrics->get_font("Helvetica", "normal");
            $size = 9;
            $date = date("d-m-Y H:i");
            $pageText = "Dicetak pada: " . $date . " | Halaman " . $PAGE_NUM . " dari " . $PAGE_COUNT;
            $width = $fontMetrics->get_text_width($pageText, $font, $size);
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 25;
            $pdf->text($x, $y, $pageText, $font, $size);
        ');
    }
    </script>

</body>
</html>