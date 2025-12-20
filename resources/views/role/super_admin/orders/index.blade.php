@extends('layouts.index')
@section('title','Semua Orders')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">

  {{-- 🧭 Breadcrumb --}}
  <div class="bg-white shadow-sm rounded-4 px-4 py-3 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3 smooth-fade">
    <div class="d-flex align-items-center flex-wrap gap-2">
      <i class="bi bi-receipt fs-5" style="color:#FF9800;"></i>
      <a href="{{ route('dashboard') }}" class="breadcrumb-link fw-semibold text-decoration-none" style="color:#FF9800;">Dashboard</a>
      <span class="text-muted">/</span>
      <span class="fw-semibold text-dark">Semua Orders</span>
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
              <th>Nama</th>
              <th>HP</th>
              <th>Status</th>
              <th>Tanggal</th>
            </tr>
          </thead>
          <tbody>

            @forelse($orders as $o)
              <tr class="text-center table-row-hover">
                <td>{{ $o->id }}</td>
                <td class="text-start fw-semibold text-dark">{{ $o->customer_name }}</td>
                <td>{{ $o->customer_phone }}</td>
                <td>
                  @php
                      $statusClass =
                        $o->status=='pending'  ? 'bg-warning-subtle text-warning' :
                        ($o->status=='approved' ? 'bg-success-subtle text-success' :
                        'bg-danger-subtle text-danger');
                  @endphp

                  <span class="badge rounded-pill px-3 py-2 {{ $statusClass }}">
                    {{ ucfirst($o->status) }}
                  </span>
                </td>
                <td>{{ $o->created_at->format('d M Y H:i') }}</td>
              </tr>

            @empty
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                  <i class="ri-information-line me-1"></i> Tidak ada data.
                </td>
              </tr>
            @endforelse

          </tbody>
        </table>
      </div>
    </div>

    {{-- ⭐ PAGINATION --}}
    <div class="p-3">
      {{ $orders->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>

{{-- Pagination Orange Style --}}
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
@endsection
