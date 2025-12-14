@extends('layouts.index') 
@section('title', 'Orders - Admin')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">

  <div class="bg-white shadow-sm rounded-4 px-4 py-3 mb-4">
    <h4 class="fw-bold mb-0 d-flex align-items-center gap-2">
      <i class="ri-shopping-bag-3-line" style="color:#FF9800;"></i>
      Orders Pending
    </h4>
  </div>

  @if($orders->count() == 0)
    <div class="alert alert-light border-start border-4 shadow-sm"
         style="border-color:#FF9800;">
      <i class="ri-information-line me-1 text-warning"></i>
      Tidak ada order pending.
    </div>
  @endif

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-3">
      <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">
          <thead class="text-center" style="background:#FFF3E0;">
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>HP</th>
              <th>Alamat</th>
              <th>Item</th>
              <th>Dibuat</th>
              <th>Aksi</th>
            </tr>
          </thead>

          <tbody id="orders-table-body">
            @foreach($orders as $order)
            <tr id="order-row-{{ $order->id }}" class="text-center">
              <td class="fw-semibold">{{ $order->id }}</td>
              <td class="text-start fw-semibold">{{ $order->customer_name }}</td>
              <td>{{ $order->customer_phone }}</td>
              <td class="text-start text-muted" style="max-width:220px">
                {{ Str::limit($order->customer_address, 60) }}
              </td>
              <td>
                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                  {{ $order->orderItems()->count() }}
                </span>
              </td>
              <td>{{ $order->created_at->format('d M Y H:i') }}</td>
              <td>
                    <button
                        class="btn btn-sm rounded-pill shadow-sm me-1"
                        style="border:1px solid #FFB74D;color:#FF9800;background:#FFF3E0;"
                        onclick="showDetail({{ $order->id }})">
                        <i class="ri-eye-line me-1"></i> Detail
                    </button>

                    <button
                        class="btn btn-sm rounded-pill shadow-sm me-1"
                        style="background:#4CAF50;color:#fff;"
                        onclick="updateStatus({{ $order->id }}, 'approved')">
                        <i class="ri-check-line me-1"></i> Selesai
                    </button>

                    <button
                        class="btn btn-sm rounded-pill shadow-sm"
                        style="background:#F44336;color:#fff;"
                        onclick="updateStatus({{ $order->id }}, 'rejected')">
                        <i class="ri-close-line me-1"></i> Tolak
                    </button>
                </td>
            </tr>
            @endforeach
          </tbody>
        </table>

      </div>
    </div>

    <div class="p-3">
      {{ $orders->links('pagination::bootstrap-5') }}
    </div>
  </div>

</div>

<!-- Modal detail (TIDAK DIUBAH) -->
<div class="modal fade" id="orderDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="orderDetailContent">
      {{-- content di-load via AJAX --}}
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
function showDetail(id){
    fetch("{{ url('/admin/orders') }}/" + id)
        .then(r => r.text())
        .then(html => {
            document.getElementById('orderDetailContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('orderDetailModal')).show();
        });
}

async function updateStatus(id, status){
    if(!confirm('Yakin ubah status ke '+status+'?')) return;

    let endpoint = "";

    if(status === 'approved'){
        endpoint = "{{ url('/admin/orders') }}/" + id + "/approve";
    } else if(status === 'rejected'){
        endpoint = "{{ url('/admin/orders') }}/" + id + "/reject";
    } else {
        alert("Status tidak valid!");
        return;
    }

    try {
        let res = await fetch(endpoint, {
            method: "POST",
            headers: {
                "Content-Type":"application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        });

        let json = await res.json();

        if(json.success){
            document.getElementById('order-row-' + id)?.remove();
            alert("Order #" + id + " diubah ke: " + status);
        } else {
            alert("Gagal update status");
        }
    } catch (e) {
        alert("Error jaringan");
    }
}

</script>
@endsection
