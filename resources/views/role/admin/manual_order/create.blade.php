@extends('layouts.index') 
@section('title', 'Manual Order - Admin')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">

  <div class="bg-white shadow-sm rounded-4 px-4 py-3 mb-4">
    <h4 class="fw-bold mb-0 d-flex align-items-center gap-2">
      <i class="ri-shopping-cart-line" style="color:#0B2447;"></i>
      Manual Order
    </h4>
  </div>

  @if(session('success'))
    <div class="alert alert-success shadow-sm">
      {{ session('success') }}
    </div>
  @endif

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-3">

      <form action="{{ route('admin.manual-order.store') }}" method="POST">
        @csrf

        <div class="mb-3">
          <label class="form-label fw-semibold">Nama Customer</label>
          <input type="text" name="customer_name" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Nomor HP</label>
          <input type="text" name="customer_phone" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Alamat</label>
          <textarea name="customer_address" class="form-control" required></textarea>
        </div>

        <h5 class="fw-semibold mt-4 mb-3">Barang</h5>

        <div id="items-container">
          <div class="item-row mb-2 d-flex gap-2 align-items-start">
            <select name="items[0][id]" class="form-select flex-grow-1 select2-item" required>
              <option value="">-- Pilih Barang --</option>
              @foreach($items as $item)
                <option value="{{ $item->id }}">{{ $item->name }} (Stock: {{ $item->stock }})</option>
              @endforeach
            </select>
            <input type="number" name="items[0][quantity]" class="form-control" min="1" value="1" style="width:100px;" required>
            <button type="button" class="btn btn-danger btn-sm remove-item">
              <i class="ri-delete-bin-6-line"></i>
            </button>
          </div>
        </div>

        <div class="mt-3 d-flex gap-2">
          <button type="button" id="add-item" class="btn" style="background:#D4A017;color:#0B2447;">
            <i class="ri-add-line me-1"></i> Tambah Item
          </button>
          <button type="submit" class="btn" style="background:#0B2447;color:#D4A017;">
            <i class="ri-check-line me-1"></i> Buat Order
          </button>
        </div>

      </form>

    </div>
  </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single {
    height: 40px;
    padding: 6px 12px;
    border-radius: .375rem;
    border: 1px solid #ced4da;
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let index = 1;

// Inisialisasi Select2
function initSelect2() {
    $('.select2-item').select2({
        placeholder: "-- Pilih Item --",
        width: '100%'
    });
}

initSelect2();

// Tambah item baru
document.getElementById('add-item').addEventListener('click', function() {
    let container = document.getElementById('items-container');
    let newRow = document.createElement('div');
    newRow.classList.add('item-row', 'mb-2', 'd-flex', 'gap-2', 'align-items-start');
    newRow.innerHTML = `
        <select name="items[${index}][id]" class="form-select flex-grow-1 select2-item" required>
            <option value="">-- Pilih Item --</option>
            @foreach($items as $item)
                <option value="{{ $item->id }}">{{ $item->name }} (Stock: {{ $item->stock }})</option>
            @endforeach
        </select>
        <input type="number" name="items[${index}][quantity]" class="form-control" min="1" value="1" style="width:100px;" required>
        <button type="button" class="btn btn-danger btn-sm remove-item">
            <i class="ri-delete-bin-6-line"></i>
        </button>
    `;
    container.appendChild(newRow);
    initSelect2();
    index++;
});

// Hapus item
document.getElementById('items-container').addEventListener('click', function(e){
    if(e.target.closest('.remove-item')){
        e.target.closest('.item-row').remove();
    }
});
</script>
@endsection
