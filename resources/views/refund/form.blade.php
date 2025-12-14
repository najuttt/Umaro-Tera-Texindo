@extends('layouts.refund')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow rounded-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-3">Ajukan Refund</h4>
                    <p class="text-muted small">
                        Masukkan kode order dengan benar. Salah satu digit aja = auto ditolak
                    </p>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('refund.submit') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Order Code -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kode Order</label>
                            <input type="text"
                                   name="order_code"
                                   class="form-control"
                                   placeholder="Kode Harus Sesuai Seperti Yang di WhatsApp"
                                   required>
                            @error('order_code')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Reason -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat Pengiriman</label>
                            <textarea name="reason"
                                      class="form-control"
                                      rows="3"></textarea>
                        </div>

                        <!-- Proof -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bukti (Foto / Video)</label>
                            <input type="file"
                                   name="proof"
                                   class="form-control"
                                   accept="image/*,video/*"
                                   required>
                            @error('proof')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <button class="btn btn-danger w-100">
                            Kirim Pengajuan Refund
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
