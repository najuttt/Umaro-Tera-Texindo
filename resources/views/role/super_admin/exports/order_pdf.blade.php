<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Order (Approved)</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background: #f2f2f2; }
        .title { text-align: center; margin-top: 10px; }
        .footer { margin-top: 20px; font-size: 11px; }
        @page { margin-top: 80px; margin-bottom: 40px; }
    </style>
</head>
<body>

<h2 class="title">LAPORAN ORDER (APPROVED)</h2>
<p class="title">Periode: {{ $periodeText }}</p>

@php
    $grandTotal = $grandTotal ?? 0;
    $totalJumlah = $totalJumlah ?? 0;
@endphp

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Order</th>
            <th>Pemesan</th>
            <th>Barang</th>
            <th>Total Qty</th>
            <th>Total Harga</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($items as $i => $row)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y') }}</td>
                <td>{{ $row->customer_name ?? '-' }}</td>
                <td>
                    @foreach($row->orderItems as $oi)
                        {{ $oi->item->name ?? '-' }} ({{ $oi->quantity }})<br>
                    @endforeach
                </td>
                <td>{{ $row->total_qty }}</td>
                <td>Rp {{ number_format($row->total_sale, 0, ',', '.') }}</td>
            </tr>
        @empty
        <tr>
            <td colspan="6">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>

    <tfoot>
        <tr>
            <th colspan="4" style="text-align:right;">Total Qty</th>
            <th colspan="2">{{ number_format($totalJumlah, 0, ',', '.') }}</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align:right;">Grand Total</th>
            <th colspan="2">Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
        </tr>
    </tfoot>
</table>


<script type="text/php">
if (isset($pdf)) {
    $pdf->page_script('
        $font = $fontMetrics->get_font("Helvetica", "normal");
        $size = 9;
        $date = date("d-m-Y H:i");
        $text = "Dicetak: " . $date . " | Halaman " . $PAGE_NUM . " / " . $PAGE_COUNT;
        $pdf->text(270, 570, $text, $font, $size);
    ');
}
</script>

</body>
</html>
