<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembukuan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2,h3 { text-align: center; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; font-size: 11px; }
        th { background: #f0f0f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .mt-20 { margin-top: 20px; }
    </style>
</head>
<body>

<h2>LAPORAN PEMBUKUAN</h2>
<p style="text-align:center;">Periode: {{ $periodeText }}</p>

{{-- ======================================================= --}}
{{-- 1. DATA PRODUK (DARI ORDER + ITEM) --}}
{{-- ======================================================= --}}
<h3 class="mt-20">1. Data Produk</h3>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Produk</th>
            <th>Harga Jual / Item</th>
        </tr>
    </thead>
    <tbody>
        @forelse($salesData as $i => $row)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td class="text-center">{{ $row['tanggal'] }}</td>
            <td>{{ $row['produk'] }}</td>
            <td class="text-right">Rp {{ number_format($row['harga_jual'],0,',','.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- ======================================================= --}}
{{-- 2. CATATAN PENJUALAN --}}
{{-- ======================================================= --}}
<h3 class="mt-20">2. Catatan Penjualan Harian</h3>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Produk</th>
            <th>Qty</th>
            <th>Harga Jual</th>
            <th>Total Jual</th>
        </tr>
    </thead>
    <tbody>
        @forelse($salesData as $i => $row)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td class="text-center">{{ $row['tanggal'] }}</td>
            <td>{{ $row['produk'] }}</td>
            <td class="text-center">{{ $row['qty'] }}</td>
            <td class="text-right">Rp {{ number_format($row['harga_jual'],0,',','.') }}</td>
            <td class="text-right">Rp {{ number_format($row['total_jual'],0,',','.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">Tidak ada data penjualan</td>
        </tr>
        @endforelse
    </tbody>
</table>

<p><strong>Total Penjualan:</strong> Rp {{ number_format($totalPenjualan,0,',','.') }}</p>
<p><strong>Total HPP:</strong> Rp {{ number_format($totalHpp,0,',','.') }}</p>
<p><strong>Total Laba Kotor:</strong> Rp {{ number_format($totalLabaKotor,0,',','.') }}</p>

{{-- ======================================================= --}}
{{-- 3. PENGELUARAN OPERASIONAL --}}
{{-- ======================================================= --}}
<h3 class="mt-20">3. Pengeluaran Operasional</h3>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Nominal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expensesData as $i => $exp)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td class="text-center">{{ $exp->date }}</td>
            <td>{{ $exp->description }}</td>
            <td class="text-right">Rp {{ number_format($exp->amount,0,',','.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">Tidak ada pengeluaran</td>
        </tr>
        @endforelse
    </tbody>
</table>

<p><strong>Total Pengeluaran:</strong> Rp {{ number_format($totalPengeluaran,0,',','.') }}</p>

{{-- ======================================================= --}}
{{-- 4. LABA BERSIH --}}
{{-- ======================================================= --}}
<h3 class="mt-20">4. Laba Bersih</h3>

<p>
    Laba Kotor - Pengeluaran = <br>
    Rp {{ number_format($totalLabaKotor,0,',','.') }}
    -
    Rp {{ number_format($totalPengeluaran,0,',','.') }}
    =
    <strong>Rp {{ number_format($labaBersih,0,',','.') }}</strong>
</p>

{{-- ======================================================= --}}
{{-- 5. REKAP AKHIR --}}
{{-- ======================================================= --}}
<h3 class="mt-20">5. Rekap Akhir</h3>

<table>
    <thead>
        <tr>
            <th>Keterangan</th>
            <th>Nominal</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Penjualan</td>
            <td class="text-right">Rp {{ number_format($totalPenjualan,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Total HPP</td>
            <td class="text-right">Rp {{ number_format($totalHpp,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Total Laba Kotor</td>
            <td class="text-right">Rp {{ number_format($totalLabaKotor,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Total Pengeluaran</td>
            <td class="text-right">Rp {{ number_format($totalPengeluaran,0,',','.') }}</td>
        </tr>
        <tr>
            <td><strong>Laba Bersih</strong></td>
            <td class="text-right"><strong>Rp {{ number_format($labaBersih,0,',','.') }}</strong></td>
        </tr>
    </tbody>
</table>

</body>
</html>
