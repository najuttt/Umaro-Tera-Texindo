@extends('layouts.index')
@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">

    {{-- ======================== BREADCRUMB ======================== --}}
    <div class="bg-white shadow-sm rounded-4 px-4 py-3 mb-4 d-flex justify-content-between align-items-center smooth-fade">
        <div class="d-flex align-items-center gap-3">
            <div class="breadcrumb-icon d-flex align-items-center justify-content-center rounded-circle"
                style="width:40px;height:40px;background:#001F3F20;color:#001F3F;">
                <i class="bi bi-bag-check-fill fs-5"></i>
            </div>

            <div>
                <h5 class="fw-bold mb-0" style="color:#001F3F;">Dashboard Order</h5>
                <small class="text-muted">Statistika Pemesanan Barang</small>
            </div>
        </div>

        <div>
            <small class="text-muted"><i class="bi bi-calendar-check me-1"></i>{{ now()->format('d M Y, H:i') }}</small>
        </div>
    </div>

    {{-- ======================== SUMMARY CARDS ======================== --}}
    <div class="row g-4 mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 summary-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-round bg-warning text-white">
                        <i class="bi bi-cart-check fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Order</h6>
                        <h3 class="fw-bold">{{ $totalOrder }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 summary-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-round bg-primary text-white">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Order Pending</h6>
                        <h3 class="fw-bold">{{ $pendingOrder }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 summary-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-round bg-success text-white">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Order Selesai</h6>
                        <h3 class="fw-bold">{{ $completedOrder }}</h3>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ======================== LATEST ORDER ======================== --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3 px-4 border-0">
            <h5 class="fw-bold m-0" style="color:#001F3F;">
                <i class="bi bi-clock-history me-2"></i>Order Terbaru
            </h5>
        </div>

        <div class="card-body px-4 pb-4">
            @if(count($latestOrders) > 0)
            <ul class="list-group list-group-flush">
                @foreach($latestOrders as $ord)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>#{{ $ord->id }}</strong> — {{ $ord->customer_name }}
                        <br>
                        <small class="text-muted">{{ $ord->created_at->format('d M Y H:i') }}</small>
                    </div>

                    <span class="badge rounded-pill 
                        {{ $ord->status == 'completed' ? 'bg-success' : ($ord->status == 'pending' ? 'bg-warning' : 'bg-secondary') }}">
                        {{ ucfirst($ord->status) }}
                    </span>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-muted fst-italic">Belum ada order terbaru</p>
            @endif
        </div>
    </div>

</div>
@endsection


{{-- ======================== STYLE ======================== --}}
@section('styles')
<style>
.summary-card { transition: .3s ease; }
.summary-card:hover { transform: translateY(-4px); }

.icon-round {
    width:50px;
    height:50px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
}

.chart-btn.active {
    background:#001F3F !important;
    color:white !important;
}
</style>
@endsection

{{-- ======================== SCRIPT ======================== --}}
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let ctx = document.getElementById('orderChart').getContext('2d');
let chart;

function loadChart(range = 'week') {
    fetch(`/admin/chart/order?range=${range}`)
        .then(res => res.json())
        .then(data => {
            if (chart) chart.destroy();

            chart = new Chart(ctx, {
                type:'line',
                data:{
                    labels:data.labels,
                    datasets:[{
                        label:'Order',
                        data:data.values,
                        borderColor:'#001F3F',
                        backgroundColor:'rgba(0,31,63,0.2)',
                        tension:0.4,
                        borderWidth:2,
                        fill:true
                    }]
                },
                options:{ responsive:true, maintainAspectRatio:false }
            });
        });
}

document.querySelectorAll('.chart-btn').forEach(btn=>{
    btn.addEventListener('click',()=>{
        document.querySelectorAll('.chart-btn').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        loadChart(btn.dataset.range);
    });
});

loadChart();
</script>
@endsection
