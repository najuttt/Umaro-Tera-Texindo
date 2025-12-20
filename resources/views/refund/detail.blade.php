@extends('layouts.refund')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="card shadow rounded-4">
                <div class="card-body p-4">

                    <h4 class="fw-bold mb-2">Ajukan Refund</h4>
                    <p class="text-muted small">
                        Pilih item dan jumlah yang ingin direfund
                    </p>

                    <form action="{{ route('refund.submit') }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        {{-- ORDER ID --}}
                        <input type="hidden" name="order_id" value="{{ $order->id }}">

                        {{-- LIST ITEM --}}
                        <div class="mb-3">
                            <label class="fw-semibold mb-2">Item Pesanan</label>

                            @foreach($order->orderItems as $oi)
                                <div class="border rounded p-3 mb-2">
                                    <div class="fw-semibold">
                                        {{ $oi->item->name }}
                                    </div>
                                    <small class="text-muted">
                                        Dibeli: {{ $oi->quantity }}
                                    </small>

                                    <input type="number"
                                           name="items[{{ $oi->item_id }}]"
                                           class="form-control mt-2"
                                           min="0"
                                           max="{{ $oi->quantity }}"
                                           placeholder="Jumlah refund">
                                </div>
                            @endforeach
                        </div>

                        {{-- ALASAN --}}
                        <div class="mb-3">
                            <label class="fw-semibold">Alasan</label>
                            <textarea name="reason"
                                      class="form-control"
                                      rows="3"></textarea>
                        </div>

                        {{-- BUKTI --}}
                        <div class="mb-3">
                            <label class="fw-semibold">Bukti (Foto / Video)</label>
                            <input type="file"
                                   name="proof"
                                   class="form-control"
                                   required>
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
