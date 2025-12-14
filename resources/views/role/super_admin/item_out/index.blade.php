@extends('layouts.index')
@section('title', 'Daftar Barang Keluar')
@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">

  {{-- 🧭 BREADCRUMB --}}
  <div class="bg-white shadow-sm rounded-4 px-4 py-3 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3 smooth-fade">
    <div class="d-flex align-items-center flex-wrap gap-2">
      <i class="bi bi-box-arrow-up fs-5" style="color:#FF9800;"></i>
      <a href="{{ route('dashboard') }}" class="breadcrumb-link fw-semibold text-decoration-none" style="color:#FF9800;">Dashboard</a>
      <span class="text-muted">/</span>
      <span class="fw-semibold text-dark">Daftar Barang Keluar</span>
    </div>
  </div>

  {{-- 📋 FILTER DAN PENCARIAN --}}
  <div class="card border-0 shadow-sm rounded-4 smooth-fade mb-4">
    <div class="card-body">
    <form method="GET" action="{{ route('super_admin.item_out.index') }}" id="filterForm">
        <div class="row g-3 align-items-center">
          <div class="col-md-3 col-sm-6">
            <label class="form-label small text-muted mb-1">Dari Tanggal</label>
            <input type="date" name="start_date" id="startDate"
              class="form-control form-control-sm border-0 shadow-sm"
              value="{{ request('start_date') }}"
              style="border-left:4px solid #FF9800 !important;">
          </div>

          <div class="col-md-3 col-sm-6">
            <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
            <input type="date" name="end_date" id="endDate"
              class="form-control form-control-sm border-0 shadow-sm"
              value="{{ request('end_date') }}"
              style="border-left:4px solid #FF9800 !important;">
          </div>

          <div class="col-md-3 col-sm-6">
            <label class="form-label small text-muted mb-1">Urutkan Qty</label>
            <select name="sort_qty" id="sortQty"
              class="form-select form-select-sm border-0 shadow-sm"
              style="border-left:4px solid #FF9800 !important;">
              <option value="">Semua</option>
              <option value="desc" {{ request('sort_qty') == 'desc' ? 'selected' : '' }}>Paling Banyak</option>
              <option value="asc" {{ request('sort_qty') == 'asc' ? 'selected' : '' }}>Paling Sedikit</option>
            </select>
          </div>

          <div class="col-md-2 col-sm-8">
            <label class="form-label small text-muted mb-1">Cari Barang/User</label>
            <input type="text" name="search" id="autoSearchInput"
              class="form-control form-control-sm border-0 shadow-sm"
              placeholder="Cari..."
              value="{{ request('search') }}"
              style="border-left:4px solid #FF9800 !important;">
          </div>

          <div class="col-md-1 col-sm-4 d-flex align-items-end">
            @if(request('start_date') || request('end_date') || request('sort_qty') || request('search'))
            <a href="{{ route('super_admin.item_out.index') }}"
               class="btn btn-sm rounded-pill w-100 shadow-sm"
               style="border:1px solid #FFB74D;color:#FF9800;background:#FFF3E0;">
              <i class="ri-refresh-line me-1"></i> Reset
            </a>
            @endif
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- 📦 TABEL DATA --}}
  <div class="card border-0 shadow-sm rounded-4 smooth-fade">
    <div class="card-body p-3">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="text-center" style="background:#FFF3E0;">
            <tr>
              <th>No</th>
              <th>Nama Pengguna</th>
              <th>Barang</th>
              <th>Kode Barang</th>
              <th>Qty</th>
              <th>Disetujui Oleh</th>
              <th>Tanggal Keluar</th>
            </tr>
          </thead>
          <tbody>
            @forelse($itemOuts as $i => $row)
              <tr class="text-center table-row-hover">
                <td>{{ $itemOuts->firstItem() + $i }}</td>
                <td class="text-start">{{ $row->cart->user->name ?? '-' }}</td>
                <td class="text-start">{{ $row->item->name ?? '-' }}</td>
                <td>{{ $row->item->code ?? '-' }}</td>
                <td>{{ $row->quantity }}</td>
                <td>{{ $row->approver->name ?? '-' }}</td>
                <td>{{ $row->released_at->format('d M Y H:i') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                  <i class="ri-information-line me-1"></i> Belum ada data barang keluar.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- ⭐ PAGINATION --}}
    <div class="p-3">
      {{ $itemOuts->withQueryString()->links('pagination::bootstrap-5') }}
    </div>

  </div>
</div>

{{-- ⚡ AUTO FILTER --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('filterForm');
  const searchInput = document.getElementById('autoSearchInput');
  const startDate = document.getElementById('startDate');
  const endDate = document.getElementById('endDate');
  const sortQty = document.getElementById('sortQty');
  let timer = null;

  function autoSubmit() {
    const start = startDate.value;
    const end = endDate.value;
    if ((start && end) || (!start && !end)) {
      clearTimeout(timer);
      timer = setTimeout(() => form.submit(), 600);
    }
  }

  searchInput.addEventListener('input', autoSubmit);
  startDate.addEventListener('change', autoSubmit);
  endDate.addEventListener('change', autoSubmit);
  sortQty.addEventListener('change', autoSubmit);
});
</script>

{{-- ⭐ PAGINATION ORANGE --}}
<style>
.pagination .page-link {
    color: #FF9800;
    border: 1px solid #FFCC80;
    border-radius: 50px !important;
}
.pagination .page-link:hover {
    background-color: #FFE0B2;
    color: #E68900;
}
.pagination .active .page-link {
    background-color: #FF9800 !important;
    border-color: #FF9800 !important;
    color: white !important;
}
.pagination .page-item.disabled .page-link {
    color: #FFCC80;
}
</style>

@if(request('search'))
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const searchTerm = @json(request('search'));
    const searchInput = document.getElementById('autoSearchInput');
    const table = document.querySelector('.table');
    if (searchInput && searchTerm) {
      searchInput.focus();
      searchInput.classList.add('glow-focus');
      table.scrollIntoView({ behavior: 'smooth', block: 'center' });
      setTimeout(() => searchInput.classList.remove('glow-focus'), 1800);
    }
  });
</script>
@endif

<style>
.glow-focus {
  box-shadow: 0 0 12px rgba(255,152,0,0.6);
  transition: box-shadow 0.4s ease-in-out;
}
</style>

@endsection
