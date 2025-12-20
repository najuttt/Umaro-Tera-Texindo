@extends('layouts.index')
@section('title', 'Data Refund')
@section('content')

<div class="container-fluid py-4 animate__animated animate__fadeIn">

  {{-- 🧭 BREADCRUMB --}}
  <div class="bg-white shadow-sm rounded-4 px-4 py-3 mb-4 d-flex align-items-center gap-2">
    <i class="ri-refund-2-line fs-5 text-warning"></i>
    <span class="fw-semibold text-dark">Data Refund</span>
  </div>

  {{-- 📋 TABLE --}}
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-3">
      <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">
          <thead class="text-center bg-warning-subtle">
            <tr>
              <th style="width:160px">Order Code</th>
              <th>Item Refund</th>
              <th style="width:120px">Status</th>
              <th style="width:140px">Diajukan</th>
            </tr>
          </thead>

          <tbody>
          @forelse($refunds as $refund)

            @php
              $statusMap = [
                'pending'  => 'bg-warning-subtle text-warning',
                'approved' => 'bg-success-subtle text-success',
                'rejected' => 'bg-danger-subtle text-danger',
              ];
              $statusClass = $statusMap[$refund->status] ?? 'bg-secondary-subtle text-secondary';

              $totalQty = $refund->items->sum('quantity');
            @endphp

            <tr>
              {{-- ORDER CODE --}}
              <td class="text-center fw-semibold">
                {{ $refund->order?->order_code ?? '-' }}
              </td>

              {{-- ITEM REFUND --}}
              <td>
                <div class="border rounded-3 p-2 bg-light"
                     style="max-height:120px; overflow-y:auto">

                  @forelse($refund->items as $ri)
                    <div class="d-flex justify-content-between align-items-center
                                small bg-white rounded-2 px-2 py-1 mb-1 shadow-sm">

                      <span>
                        {{ $ri->item?->name ?? 'Item dihapus' }}
                      </span>

                      <span class="badge bg-dark-subtle text-dark">
                        {{ $ri->qty }}
                      </span>
                    </div>
                  @empty
                    <span class="text-muted fst-italic small">
                      Tidak ada item refund
                    </span>
                  @endforelse

                </div>

              {{-- STATUS --}}
              <td class="text-center">
                <span class="badge px-3 py-2 rounded-pill {{ $statusClass }}">
                  {{ strtoupper($refund->status) }}
                </span>
              </td>

              {{-- TANGGAL --}}
              <td class="text-center">
                {{ $refund->created_at->format('d M Y') }}
              </td>
            </tr>

          @empty
            <tr>
              <td colspan="4" class="text-center text-muted py-4">
                Belum ada data refund
              </td>
            </tr>
          @endforelse
          </tbody>

        </table>

      </div>
    </div>
  </div>

</div>
@endsection