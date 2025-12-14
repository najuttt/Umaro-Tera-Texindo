@extends('layouts.index')
@section('title', 'Data Refund')
@section('content')

<div class="container-fluid py-4 animate__animated animate__fadeIn">

  {{-- 🧭 BREADCRUMB --}}
  <div class="bg-white shadow-sm rounded-4 px-4 py-3 mb-4 d-flex align-items-center gap-2 smooth-fade">
    <i class="ri-refund-2-line fs-5" style="color:#FF9800;"></i>
    <a href="{{ route('dashboard') }}" class="fw-semibold text-decoration-none" style="color:#FF9800;">
      Dashboard
    </a>
    <span class="text-muted">/</span>
    <span class="fw-semibold text-dark">Data Refund</span>
  </div>

  {{-- 📋 CARD TABLE --}}
  <div class="card border-0 shadow-sm rounded-4 smooth-fade">
    <div class="card-body p-3">
      <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">
          <thead class="text-center" style="background:#FFF3E0;">
            <tr>
              <th>Order Code</th>
              <th>Alamat</th>
              <th>Status</th>
              <th>Diajukan</th>
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

              <tr class="text-center table-row-hover">
                <td class="fw-semibold text-dark">
                  {{ $refund->order->order_code }}
                </td>

                <td class="text-start text-muted">
                  {{ $refund->reason }}
                </td>

                <td>
                  <span class="badge px-3 py-2 rounded-pill {{ $statusClass }}">
                    {{ strtoupper($refund->status) }}
                  </span>
                </td>

                <td>
                  {{ $refund->created_at->format('d M Y') }}
                </td>
              </tr>

            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted">
                  <i class="ri-information-line me-1"></i>
                  Belum ada data refund.
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
