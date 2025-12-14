@extends('layouts.index')
@section('title', 'Export Data Barang')
@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">

  {{-- 🧭 BREADCRUMB --}}
  <div class="bg-white shadow-sm rounded-4 px-4 py-3 mb-4 d-flex flex-wrap align-items-center justify-content-between smooth-fade">
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <i class="bi bi-file-earmark-arrow-down fs-5" style="color:#FF9800;"></i>
      <a href="{{ route('dashboard') }}" class="breadcrumb-link fw-semibold text-decoration-none" style="color:#FF9800;">
        Dashboard
      </a>
      <span class="text-muted">/</span>
      <span class="fw-semibold text-dark">Export Data Barang</span>
    </div>
  </div>

  {{-- 🔶 FILTER DATA --}}
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white fw-bold" style="color:#FF9800;">
      <i class="bi bi-funnel me-2"></i> Filter Data
    </div>

    <div class="card-body bg-white rounded-bottom-4">

      {{-- FORM DIMULAI --}}
      <form action="{{ route('super_admin.export.index') }}" method="GET">

        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label for="start_date" class="form-label fw-semibold text-dark">Tanggal Mulai</label>
            <input type="date" name="start_date" id="start_date"
                   value="{{ request('start_date') }}" class="form-control border-0 shadow-sm"
                   style="border-left:4px solid #FF9800;" required>
          </div>

          <div class="col-md-3">
            <label for="period" class="form-label fw-semibold text-dark">Periode</label>
            <select name="period" id="period" class="form-select border-0 shadow-sm"
                    style="border-left:4px solid #FF9800;" required>
              <option value="">-- Pilih Periode --</option>
              <option value="weekly"  {{ request('period')=='weekly'  ? 'selected' : '' }}>1 Minggu</option>
              <option value="monthly" {{ request('period')=='monthly' ? 'selected' : '' }}>1 Bulan</option>
              <option value="yearly"  {{ request('period')=='yearly'  ? 'selected' : '' }}>1 Tahun</option>
            </select>
          </div>

          <div class="col-md-3">
            <label for="type" class="form-label fw-semibold text-dark">Jenis Data</label>
            <select name="type" id="type" class="form-select border-0 shadow-sm"
                    style="border-left:4px solid #FF9800;">
              <option value="masuk"  {{ request('type')=='masuk'  ? 'selected' : '' }}>Barang Masuk</option>
              <option value="order"  {{ request('type')=='order'  ? 'selected' : '' }}>Order</option>
            </select>
          </div>

          <div class="col-md-3 text-end mt-3 mt-md-0">
            <button type="submit" class="btn rounded-pill w-100 shadow-sm hover-glow"
                    style="background-color:#FF9800;color:white;">
              <i class="bi bi-search"></i> Tampilkan
            </button>
          </div>
        </div>

      </form>
      {{-- FORM SELESAI --}}

    </div>
  </div>

  {{-- 🔶 TABEL DATA --}}
  @if(isset($items) && count($items) > 0)
  <div class="card shadow-sm border-0 mb-4">
      <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <h6 class="mb-0 fw-semibold">
              <i class="bi bi-table"></i> Data
              <span class="text-muted">({{ count($items) }} data)</span>
          </h6>

          <div class="btn-group">
              <a href="{{ route('super_admin.export.download', array_merge(request()->query(), ['format'=>'excel'])) }}"
                  class="btn btn-success btn-sm shadow-sm">
                  <i class="bi bi-file-earmark-excel"></i> Excel
              </a>
              <a href="{{ route('super_admin.export.download', array_merge(request()->query(), ['format'=>'pdf'])) }}"
                  class="btn btn-danger btn-sm shadow-sm">
                  <i class="bi bi-file-earmark-pdf"></i> PDF
              </a>
          </div>
      </div>

      <div class="card-body table-responsive bg-white">
          <table class="table table-bordered table-hover align-middle text-center">
              <thead class="table-primary">
                  <tr>
                      @if(request('type') == 'masuk')
                          <th>No</th>
                          <th>Nama Barang</th>
                          <th>Supplier</th>
                          <th>Tanggal Masuk</th>
                          <th>Jumlah</th>
                          <th>Satuan</th>
                          <th>Harga Satuan</th>
                          <th>Total Harga</th>

                      @elseif(request('type') == 'order')
                          <th>No</th>
                          <th>Nama Barang</th>
                          <th>Tanggal Order</th>
                          <th>Pemesan</th>
                          <th>Total Qty</th>
                          <th>Total Harga</th>
                      @endif
                  </tr>
              </thead>

              <tbody>
                  @foreach($items as $i => $row)
                  <tr>
                      @if(request('type') == 'masuk')
                          <td>{{ $i+1 }}</td>
                          <td>{{ $row->item->name ?? '-' }}</td>
                          <td>{{ $row->supplier->name ?? '-' }}</td>
                          <td>{{ optional($row->created_at)->format('d-m-Y H:i') }}</td>
                          <td>{{ $row->quantity }}</td>
                          <td>{{ $row->item->unit->name ?? '-' }}</td>
                          <td>Rp {{ number_format($row->item->price,0,',','.') }}</td>
                          <td>Rp {{ number_format($row->total_price,0,',','.') }}</td>

                      @elseif(request('type') == 'order')
                          <td>{{ $i+1 }}</td>
                          <td>{{ $row->orderItems->pluck('item.name')->join(', ') }}</td>
                          <td>{{ optional($row->created_at)->format('d-m-Y H:i') }}</td>
                          <td>{{ $row->customer_name ?? '-' }}</td>
                          <td>{{ $row->total_qty }}</td>
                          <td>Rp {{ number_format($row->total_sale,0,',','.') }}</td>
                      @endif
                  </tr>
                  @endforeach
              </tbody>
          </table>
      </div>
  </div>

  @elseif(request()->has('start_date'))
  <div class="alert alert-warning shadow-sm">
      <i class="bi bi-exclamation-triangle"></i> Tidak ada data ditemukan untuk periode ini.
  </div>
  @endif

</div>

{{-- STYLE --}}
<style>
.smooth-fade { animation: fadeIn 0.6s ease-in-out; }
@keyframes fadeIn { from {opacity:0;transform:translateY(10px);} to {opacity:1;transform:translateY(0);} }
.hover-glow:hover {
  background-color: #FFC107 !important;
  color: #fff !important;
  box-shadow: 0 0 12px rgba(255,152,0,0.4);
}
.form-control:focus, .form-select:focus {
  border-color: #FF9800 !important;
  box-shadow: 0 0 0 3px rgba(255,152,0,0.25);
}
.table-hover tbody tr:hover { background-color:#FFF9E6 !important; transition:all .2s ease; }
</style>

@endsection