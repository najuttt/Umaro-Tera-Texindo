@extends('layouts.index')
@section('title', 'Manajemen Refund')
@section('content')

<div class="container-fluid py-4 animate__animated animate__fadeIn">

  {{-- 🧭 BREADCRUMB --}}
  <div class="bg-white shadow-sm rounded-4 px-4 py-3 mb-4 d-flex align-items-center gap-2">
    <i class="ri-refund-2-line fs-5" style="color:#FF9800;"></i>
    <span class="fw-semibold text-dark">Manajemen Refund</span>
  </div>

  {{-- 📋 TABLE --}}
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-3">
      <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">
          <thead class="text-center" style="background:#FFF3E0;">
            <tr>
              <th>Order Code</th>
              <th>Item Refund</th>
              <th>Alasan</th>
              <th>Bukti</th>
              <th>Status</th>
              <th width="180">Aksi</th>
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
            @endphp

            <tr>
              {{-- ORDER CODE --}}
              <td class="text-center fw-semibold">
                {{ $refund->order?->order_code ?? '-' }}
              </td>

              {{-- ITEM REFUND --}}
              <td>
                @forelse($refund->items as $ri)
                  <div class="small">
                    {{ $ri->item?->name ?? 'Item dihapus' }}
                    <span class="text-muted">(x{{ $ri->qty }})</span>
                  </div>
                @empty
                  <span class="text-muted fst-italic small">Tidak ada item</span>
                @endforelse
              </td>

              {{-- ALASAN --}}
              <td class="text-muted small">
                {{ $refund->reason ?? '-' }}
              </td>

              {{-- BUKTI --}}
              <td class="text-center">
                @if($refund->proof_file)
                  <a href="{{ asset('storage/'.$refund->proof_file) }}"
                     target="_blank"
                     class="btn btn-sm rounded-pill"
                     style="border:1px solid #FF9800;color:#FF9800;">
                    Lihat
                  </a>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>

              {{-- STATUS --}}
              <td class="text-center">
                <span class="badge px-3 py-2 rounded-pill {{ $statusClass }}">
                  {{ strtoupper($refund->status) }}
                </span>
              </td>

              {{-- AKSI --}}
              <td class="text-center">
                @if($refund->status === 'pending')
                  <div class="d-flex justify-content-center gap-2">

                    <form method="POST"
                          action="{{ route('admin.refunds.approve', $refund->id) }}">
                      @csrf
                      <button class="btn btn-sm rounded-pill"
                              style="background:#4CAF50;color:#fff;">
                        Approve
                      </button>
                    </form>

                    <form method="POST"
                          action="{{ route('admin.refunds.reject', $refund->id) }}">
                      @csrf
                      <button class="btn btn-sm rounded-pill"
                              style="background:#F44336;color:#fff;">
                        Reject
                      </button>
                    </form>

                  </div>
                @else
                  <span class="text-muted fst-italic small">Selesai</span>
                @endif
              </td>
            </tr>

          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                Belum ada pengajuan refund
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
