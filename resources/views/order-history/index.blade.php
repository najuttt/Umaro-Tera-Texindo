@extends('layouts.checkout_detail')

@section('content')
<style>
:root {
    --navy: #0b1d3a;
    --navy-soft: #13294b;
    --gold: #d4af37;
    --gold-soft: #f1e5ac;
    --bg-light: #f8f9fa;
}

/* ==================== GENERAL ==================== */
.bg-navy-gradient {
    background: linear-gradient(135deg, var(--navy), var(--navy-soft));
}

.text-gold { color: var(--gold); }
.text-navy { color: var(--navy); }

/* ==================== PAGE HEADER ==================== */
.page-header {
    background: linear-gradient(135deg, var(--navy), var(--navy-soft));
    padding: 50px 30px;
    border-radius: 20px;
    margin-bottom: 40px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(11, 29, 58, 0.3);
}

.page-header h2 {
    color: var(--gold);
    font-weight: 800;
    font-size: 2.2rem;
    margin-bottom: 12px;
    letter-spacing: 1px;
}

.page-header p {
    color: rgba(255,255,255,.85);
    font-size: 1.05rem;
    margin: 0;
}

/* ==================== INFO BOX (PENJELASAN) ==================== */
.info-box {
    background: linear-gradient(135deg, rgba(212,175,55,.08), rgba(212,175,55,.15));
    border: 2px solid var(--gold);
    border-radius: 16px;
    padding: 25px 30px;
    margin-bottom: 35px;
    box-shadow: 0 4px 15px rgba(212,175,55,.2);
}

.info-box h5 {
    color: var(--navy);
    font-weight: 700;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-box h5 i {
    color: var(--gold);
    font-size: 1.4rem;
}

.info-item {
    background: white;
    border-left: 4px solid var(--gold);
    padding: 15px 20px;
    margin-bottom: 12px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-item strong {
    color: var(--navy);
    font-weight: 700;
    display: block;
    margin-bottom: 6px;
}

.info-item p {
    color: #555;
    margin: 0;
    line-height: 1.6;
}

/* ==================== CARD ELEGANT ==================== */
.card-elegant {
    border-radius: 20px;
    border: 2px solid rgba(212,175,55,.25);
    box-shadow: 0 8px 25px rgba(0,0,0,.1);
    overflow: hidden;
    background: white;
}

/* ==================== ORDER CARD (GANTI TABLE JADI CARD) ==================== */
.order-card {
    background: white;
    border: 2px solid rgba(212,175,55,.2);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,.08);
}

.order-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(212,175,55,.3);
    border-color: var(--gold);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid rgba(212,175,55,.15);
}

.order-code {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--navy);
    letter-spacing: 0.5px;
}

.order-date {
    color: #666;
    font-size: 0.9rem;
}

.order-date i {
    color: var(--gold);
    margin-right: 6px;
}

.order-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.order-info-item {
    background: var(--bg-light);
    padding: 15px 18px;
    border-radius: 12px;
    border-left: 4px solid var(--gold);
}

.order-info-item label {
    display: block;
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 6px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.order-info-item .value {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--navy);
}

.order-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    padding-top: 15px;
    border-top: 2px solid rgba(212,175,55,.15);
}

/* ==================== STATUS BADGES ==================== */
.badge-status {
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 700;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 3px 10px rgba(0,0,0,.15);
}

.badge-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.badge-danger {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.badge-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
}

.badge-warning {
    background: linear-gradient(135deg, #ffc107, #ff9800);
    color: #000;
}

.badge-secondary {
    background: linear-gradient(135deg, #6c757d, #5a6268);
    color: white;
}

/* ==================== TAB FILTER ==================== */
.filter-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    overflow-x: auto;
    padding-bottom: 10px;
}

.filter-tab {
    padding: 12px 24px;
    border-radius: 12px;
    background: white;
    border: 2px solid rgba(212,175,55,.2);
    color: var(--navy);
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.3s ease;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
}

.filter-tab:hover {
    background: rgba(212,175,55,.1);
    border-color: var(--gold);
    transform: translateY(-2px);
    color: var(--navy);
}

.filter-tab.active {
    background: linear-gradient(135deg, var(--navy), var(--navy-soft));
    border-color: var(--navy);
    color: var(--gold);
    box-shadow: 0 4px 12px rgba(11,29,58,.3);
}

.filter-tab .badge-count {
    background: var(--gold);
    color: #000;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 800;
}

.filter-tab.active .badge-count {
    background: white;
    color: var(--navy);
}

@media (max-width: 768px) {
    .filter-tabs {
        flex-wrap: nowrap;
    }
    
    .filter-tab {
        font-size: 0.85rem;
        padding: 10px 18px;
    }
}

/* ==================== BUTTONS ==================== */
.btn-action {
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 700;
    transition: all 0.3s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    box-shadow: 0 3px 10px rgba(0,0,0,.12);
}

.btn-detail {
    background: linear-gradient(135deg, #17a2b8, #138496);
    color: white;
}

.btn-detail:hover {
    background: linear-gradient(135deg, #138496, #117a8b);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(23,162,184,.4);
    color: white;
}

.btn-refund {
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    color: var(--navy);
    border: 2px solid var(--gold);
    box-shadow: 0 3px 10px rgba(212,175,55,.15);
}

.btn-refund:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-soft));
    color: #000;
    border-color: var(--gold);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(212,175,55,.35);
}

.btn-cancel {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
    color: #c62828;
    border: 2px solid #ef5350;
    box-shadow: 0 3px 10px rgba(239,83,80,.15);
}

.btn-cancel:hover {
    background: linear-gradient(135deg, #ef5350, #e53935);
    color: white;
    border-color: #c62828;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(198,40,40,.35);
}

.btn-cancel i {
    color: inherit;
}

.btn-refund i {
    color: #25D366;
}

.btn-report {
    background: linear-gradient(135deg, #fff3cd, #ffeaa7);
    color: var(--navy);
    border: 2px solid #ffc107;
    box-shadow: 0 3px 10px rgba(255,193,7,.15);
}

.btn-report:hover {
    background: linear-gradient(135deg, #ffc107, #ff9800);
    color: #000;
    border-color: #ff9800;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255,193,7,.35);
}

.btn-report i {
    color: #ff6b6b;
}

.btn-shop {
    background: linear-gradient(135deg, var(--navy), var(--navy-soft));
    color: var(--gold);
    padding: 12px 30px;
    font-size: 1rem;
}

.btn-shop:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(11,29,58,.4);
    color: var(--gold);
}

/* ==================== EMPTY STATE ==================== */
.empty-state {
    text-align: center;
    padding: 80px 30px;
}

.empty-state i {
    font-size: 5rem;
    color: var(--gold);
    margin-bottom: 25px;
    opacity: 0.7;
}

.empty-state h4 {
    color: var(--navy);
    font-weight: 700;
    margin-bottom: 15px;
}

.empty-state p {
    color: #666;
    margin-bottom: 30px;
    font-size: 1.05rem;
}

/* ==================== ALERTS ==================== */
.alert-elegant {
    border-radius: 14px;
    border: 2px solid;
    padding: 18px 25px;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(0,0,0,.1);
}

.alert-success {
    background: rgba(40,167,69,.1);
    border-color: #28a745;
    color: #155724;
}

.alert-danger {
    background: rgba(220,53,69,.1);
    border-color: #dc3545;
    color: #721c24;
}

.alert-warning {
    background: rgba(255,193,7,.15);
    border-color: #ffc107;
    color: #856404;
}

/* ==================== RESPONSIVE ==================== */
@media (max-width: 768px) {
    .order-body {
        grid-template-columns: 1fr;
    }
    
    .order-footer {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn-action {
        justify-content: center;
        width: 100%;
    }
    
    .page-header h2 {
        font-size: 1.8rem;
    }
}
</style>

<div class="container py-5">
    
    {{-- PAGE HEADER --}}
    <div class="page-header">
        <h2>
            <i class="bi bi-clock-history me-2"></i>
            Riwayat Pembayaran
        </h2>
        <p>Pantau dan kelola transaksi pembayaran Midtrans Anda</p>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success alert-elegant">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-elegant">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- INFO BOX: PENJELASAN REFUND --}}
    <div class="info-box">
        <h5>
            <i class="bi bi-info-circle-fill"></i>
            Informasi Penting tentang Refund & Garansi
        </h5>
        
        <div class="info-item">
            <strong>💰 Refund Pembayaran (Order Ditolak)</strong>
            <p>
                Jika pesanan Anda ditolak oleh admin, dana yang telah dibayarkan melalui Midtrans <b>tidak dapat dikembalikan secara otomatis</b>. 
                Silakan hubungi admin melalui tombol <b>"Hubungi Admin"</b> dengan menyertakan <b>Kode Order</b> Anda untuk proses refund manual.
            </p>
        </div>

        <div class="info-item">
            <strong>📦 Garansi Barang Rusak/Cacat</strong>
            <p>
                Jika barang yang Anda terima dalam kondisi rusak atau cacat, Anda berhak mendapatkan penggantian atau refund. 
                Segera hubungi admin maksimal <b>3 hari setelah barang diterima</b> dengan melampirkan foto kondisi barang dan <b>Kode Order</b>.
            </p>
        </div>

        <div class="info-item">
            <strong>🔄 Proses Refund</strong>
            <p>
                Proses refund akan diproses dalam <b>7-14 hari kerja</b> setelah verifikasi admin. 
                Dana akan dikembalikan ke metode pembayaran yang sama atau ke rekening yang Anda daftarkan.
            </p>
        </div>

        <div class="info-item">
            <strong>💬 Cara Mengajukan Refund</strong>
            <p>
                Klik tombol <b>"Hubungi Admin"</b> pada pesanan yang ditolak atau bermasalah. 
                Sistem akan otomatis membuka WhatsApp dengan format pesan yang sudah terisi. 
                <b>Jangan lupa simpan Kode Order Anda!</b>
            </p>
        </div>
    </div>
    
    {{-- ✅ TAB FILTER --}}
    <div class="filter-tabs">
        <a href="{{ route('order.history', ['status' => 'all']) }}" 
        class="filter-tab {{ $statusFilter === 'all' ? 'active' : '' }}">
            <i class="bi bi-list-ul"></i>
            Semua
            <span class="badge-count">{{ $counts['all'] }}</span>
        </a>
        
        <a href="{{ route('order.history', ['status' => 'pending']) }}" 
        class="filter-tab {{ $statusFilter === 'pending' ? 'active' : '' }}">
            <i class="bi bi-hourglass-split"></i>
            Pending
            <span class="badge-count">{{ $counts['pending'] }}</span>
        </a>
        
        <a href="{{ route('order.history', ['status' => 'approved']) }}" 
        class="filter-tab {{ $statusFilter === 'approved' ? 'active' : '' }}">
            <i class="bi bi-check-circle"></i>
            Disetujui
            <span class="badge-count">{{ $counts['approved'] }}</span>
        </a>
        
        <a href="{{ route('order.history', ['status' => 'completed']) }}" 
        class="filter-tab {{ $statusFilter === 'completed' ? 'active' : '' }}">
            <i class="bi bi-check2-all"></i>
            Selesai
            <span class="badge-count">{{ $counts['completed'] }}</span>
        </a>
        
        <a href="{{ route('order.history', ['status' => 'rejected']) }}" 
        class="filter-tab {{ $statusFilter === 'rejected' ? 'active' : '' }}">
            <i class="bi bi-x-circle"></i>
            Ditolak
            <span class="badge-count">{{ $counts['rejected'] }}</span>
        </a>
    </div>

    {{-- EMPTY STATE --}}
    @if($orders->isEmpty())
        <div class="card card-elegant">
            <div class="card-body">
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h4>Belum Ada Riwayat Pembayaran</h4>
                    <p>Anda belum memiliki transaksi pembayaran Midtrans</p>
                    <a href="{{ route('produk') }}" class="btn btn-action btn-shop">
                        <i class="bi bi-cart-plus"></i>
                        Mulai Belanja Sekarang
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- ORDER CARDS --}}
        @foreach($orders as $order)
        <div class="order-card">
            
            {{-- HEADER --}}
            <div class="order-header">
                <div>
                    <div class="order-code">
                        <i class="bi bi-receipt-cutoff me-2 text-gold"></i>
                        {{ $order->order_code }}
                    </div>
                    <div class="order-date">
                        <i class="bi bi-calendar3"></i>
                        {{ $order->created_at->format('d M Y, H:i') }} WIB
                    </div>
                </div>
                
                <div>
                    @if($order->status === 'approved')
                        <span class="badge-status badge-success">
                            <i class="bi bi-check-circle-fill"></i>
                            Disetujui
                        </span>
                    @elseif($order->status === 'rejected')
                        <span class="badge-status badge-danger">
                            <i class="bi bi-x-circle-fill"></i>
                            Ditolak
                        </span>
                    @elseif($order->status === 'completed')
                        <span class="badge-status badge-primary">
                            <i class="bi bi-check2-all"></i>
                            Selesai
                        </span>
                    @elseif($order->status === 'pending')
                        <span class="badge-status badge-warning">
                            <i class="bi bi-hourglass-split"></i>
                            Pending
                        </span>
                    @else
                        <span class="badge-status badge-secondary">
                            {{ ucfirst($order->status) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- BODY --}}
            <div class="order-body">
                <div class="order-info-item">
                    <label><i class="bi bi-person me-1"></i> Nama Pembeli</label>
                    <div class="value">{{ $order->customer_name }}</div>
                </div>
                
                <div class="order-info-item">
                    <label><i class="bi bi-telephone me-1"></i> No. Telepon</label>
                    <div class="value">{{ $order->customer_phone }}</div>
                </div>
                
                <div class="order-info-item">
                    <label><i class="bi bi-cash-stack me-1"></i> Total Pembayaran</label>
                    <div class="value text-gold">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </div>
                </div>
                
                <div class="order-info-item">
                    <label><i class="bi bi-credit-card me-1"></i> Metode Bayar</label>
                    <div class="value">Midtrans</div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="order-footer">

                {{-- ✅ TOMBOL BATALKAN PESANAN (PENDING ONLY) --}}
                @if($order->status === 'pending')
                    <form action="{{ route('order.cancel', $order->id) }}" method="POST" 
                        onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')"
                        style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-action btn-cancel">
                            <i class="bi bi-x-circle"></i>
                            Batalkan Pesanan
                        </button>
                    </form>
                @endif

                {{-- ✅ TOMBOL REFUND (REJECTED ONLY) --}}
                @if($order->status === 'rejected')
                    <a href="{{ route('order.refund.wa', $order->id) }}" 
                    class="btn btn-action btn-refund">
                        <i class="bi bi-whatsapp"></i>
                        Ajukan Refund
                    </a>
                @endif

                {{-- ✅ TOMBOL LAPORAN KERUSAKAN (APPROVED/COMPLETED ONLY) --}}
                @if($order->status === 'approved' || $order->status === 'completed')
                    <a href="{{ route('order.refund.wa', $order->id) }}" 
                    class="btn btn-action btn-report">
                        <i class="bi bi-exclamation-triangle"></i>
                        Laporkan Masalah
                    </a>
                @endif
            </div>

        </div>

        {{-- MODAL DETAIL --}}
        <div class="modal fade modal-elegant" id="detailModal{{ $order->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-receipt"></i>
                            Detail Pesanan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                        
                        {{-- KODE ORDER --}}
                        <div class="text-center mb-4">
                            <h4 class="text-navy mb-2">{{ $order->order_code }}</h4>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $order->created_at->format('d M Y, H:i') }} WIB
                            </p>
                        </div>

                        <div class="divider-gold"></div>

                        {{-- DATA PEMBELI --}}
                        <div class="modal-section">
                            <h6>
                                <i class="bi bi-person-circle"></i>
                                Data Pembeli
                            </h6>
                            <ul class="modal-info-list">
                                <li>
                                    <strong>Nama:</strong> {{ $order->customer_name }}
                                </li>
                                <li>
                                    <strong>No HP:</strong> {{ $order->customer_phone }}
                                </li>
                                <li>
                                    <strong>Alamat:</strong> {{ $order->customer_address }}
                                </li>
                            </ul>
                        </div>

                        {{-- DAFTAR BARANG --}}
                        <div class="modal-section">
                            <h6>
                                <i class="bi bi-bag-check"></i>
                                Daftar Barang
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-center" style="width: 80px;">Qty</th>
                                            <th class="text-end" style="width: 120px;">Harga</th>
                                            <th class="text-end" style="width: 140px;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->orderItems as $item)
                                        <tr>
                                            <td>
                                                <strong class="text-navy">{{ $item->item->name }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                            </td>
                                            <td class="text-end">
                                                Rp {{ number_format($item->item->price, 0, ',', '.') }}
                                            </td>
                                            <td class="text-end">
                                                <strong>
                                                    Rp {{ number_format($item->item->price * $item->quantity, 0, ',', '.') }}
                                                </strong>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <td colspan="3" class="text-end">
                                                <strong class="text-navy fs-5">TOTAL:</strong>
                                            </td>
                                            <td class="text-end">
                                                <h5 class="mb-0 text-gold fw-bold">
                                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                                </h5>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                    </div>
                    
                </div>
            </div>
        </div>
        @endforeach

        {{-- PAGINATION --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @endif

</div>
@endsection