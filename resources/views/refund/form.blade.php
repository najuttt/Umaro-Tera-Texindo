@extends('layouts.refund')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow rounded-4">
                <div class="card-body p-4">

                    <h4 class="fw-bold mb-3">Cek Order Refund</h4>
                    <p class="text-muted small">
                        Masukkan kode order dengan benar
                    </p>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('refund.check') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="fw-semibold">Kode Order</label>
                            <input type="text"
                                   name="order_code"
                                   class="form-control"
                                   required>
                        </div>

                        <button class="btn btn-danger w-100">
                            Lanjutkan
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
