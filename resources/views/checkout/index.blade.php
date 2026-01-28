@extends('layouts.checkout')

@section('content')
<style>
:root {
    --navy: #0b1d3a;
    --navy-soft: #13294b;
    --gold: #d4af37;
    --gold-soft: #f1e5ac;
}

.bg-navy { background: linear-gradient(135deg, var(--navy), var(--navy-soft)); }
.text-gold { color: var(--gold); }

.btn-gold {
    background: linear-gradient(135deg, var(--gold), var(--gold-soft));
    color: #000;
    border: none;
    font-weight: 600;
    transition: all 0.25s ease;
}
.btn-gold:hover { filter: brightness(0.95); transform: scale(1.02); }

.card-elegant {
    border-radius: 16px;
    border: 1px solid rgba(212,175,55,.3);
    box-shadow: 0 10px 25px rgba(0,0,0,.15);
}

.divider {
    height: 1px;
    background: linear-gradient(to right, transparent, var(--gold), transparent);
    margin: 20px 0;
}
</style>

<div class="container py-5">
<div class="row justify-content-center">
<div class="col-lg-8">

{{-- HEADER --}}
<div class="text-center mb-4">
    <h3 class="fw-bold text-gold">Checkout</h3>
    <p class="text-muted">Pastikan data & pesanan kamu sudah benar</p>
</div>

{{-- LIST ITEM --}}
<div class="card card-elegant mb-4">
<div class="card-body">
<h5 class="fw-semibold mb-3">Daftar Barang</h5>

@foreach($cart->guestCartItems as $item)
<div class="d-flex align-items-center mb-3 pb-3 border-bottom">
    <img src="{{ $item->item->image ? asset('storage/'.$item->item->image) : asset('images/default.png') }}"
         width="70" height="70" class="rounded me-3" style="object-fit:cover">

    <div class="flex-grow-1">
        <div class="fw-bold">{{ $item->item->name }}</div>
        <div class="text-muted small">Jumlah: {{ $item->quantity }}</div>
    </div>

    <div class="fw-semibold">
        Rp {{ number_format($item->item->price * $item->quantity, 0, ',', '.') }}
    </div>
</div>
@endforeach

<div class="d-flex justify-content-between mt-3">
    <h5>Total</h5>
    <h4 class="fw-bold text-gold">
        Rp {{ number_format($totalHarga, 0, ',', '.') }}
    </h4>
</div>
</div>
</div>

{{-- METODE CHECKOUT --}}
<div class="card card-elegant mb-4">
<div class="card-body">
<h5 class="fw-semibold mb-3">Pilih Metode Checkout</h5>

{{-- ✅ CHECKOUT WA (GUEST ONLY) - ADA FORM --}}
@guest
<div class="mb-4">
    <h6 class="text-muted mb-3">Checkout via WhatsApp</h6>
    <p class="small text-muted">Isi data diri untuk order via WhatsApp</p>
    
    <form id="checkoutWaForm">
        @csrf
        <input type="text" name="customer_name" class="form-control mb-2"
               placeholder="Nama Lengkap" required>
        <input type="text" name="customer_phone" class="form-control mb-2"
               placeholder="Nomor HP (08...)" required>
        <textarea name="customer_address" class="form-control mb-3"
                  placeholder="Alamat Lengkap" rows="3" required></textarea>
        
        <button type="button" class="btn btn-success w-100" id="checkoutWaBtn">
            <i class="bi bi-whatsapp"></i> Checkout via WhatsApp
        </button>
    </form>
</div>

<div class="divider"></div>

<p class="text-center text-muted small mb-2">atau</p>
@endguest

{{-- ✅ PAYMENT GATEWAY - LANGSUNG BAYAR (GA ADA FORM) --}}
@guest
<a href="{{ route('google.login') }}" class="btn btn-gold w-100">
    <i class="bi bi-google"></i> Login untuk Bayar dengan Midtrans
</a>
@else
<div>
    <h6 class="text-muted mb-3">Pembayaran Online</h6>
    <p class="small text-muted mb-3">Bayar langsung dengan kartu kredit/debit, transfer bank, atau e-wallet</p>
    <button class="btn btn-gold w-100" id="payMidtrans">
        <i class="bi bi-credit-card"></i> Bayar Sekarang
    </button>
</div>
@endguest

</div>
</div>

</div>
</div>
</div>

{{-- MODAL MIDTRANS --}}
<div class="modal fade" id="midtransModal" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content p-3">
    <div id="snap-container"></div>
</div>
</div>
</div>

{{-- MIDTRANS SCRIPT --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script>
// ✅ CHECKOUT VIA WHATSAPP (ADA FORM DATA)
document.getElementById('checkoutWaBtn')?.addEventListener('click', async () => {
    const form = document.getElementById('checkoutWaForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);

    try {
        // 1. CHECKOUT DULU (SIMPAN ORDER)
        const checkoutRes = await fetch("{{ route('checkout.guest.process') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: formData
        });

        if (!checkoutRes.ok) {
            const err = await checkoutRes.json();
            alert(err.message ?? 'Checkout gagal');
            return;
        }

        // 2. AMBIL LINK WA
        const waRes = await fetch("{{ route('send.whatsapp') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        });

        if (!waRes.ok) {
            const err = await waRes.json();
            alert(err.message ?? 'Gagal membuat link WhatsApp');
            return;
        }

        const waData = await waRes.json();

        if (waData.wa_url) {
            window.open(waData.wa_url, '_blank');
            setTimeout(() => {
                window.location.href = "{{ route('produk') }}";
            }, 1500);
        }

    } catch (e) {
        console.error(e);
        alert('Terjadi kesalahan sistem');
    }
});

// ✅ PAYMENT MIDTRANS (GA PERLU FORM, LANGSUNG BAYAR)
document.getElementById('payMidtrans')?.addEventListener('click', async () => {
    try {
        const res = await fetch("{{ route('checkout.pay') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        });

        if (!res.ok) {
            let err;
            try {
                err = await res.json();
            } catch {
                err = { message: await res.text() };
            }
            console.error('SERVER ERROR:', err);
            alert(err.message ?? 'Server error');
            return;
        }

        const data = await res.json();

        if (!data.snap_token) {
            console.error(data);
            alert('Snap token tidak ditemukan');
            return;
        }

        const modalEl = document.getElementById('midtransModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        modalEl.addEventListener('shown.bs.modal', () => {
            window.snap.embed(data.snap_token, {
                embedId: 'snap-container',
                onSuccess: function(result) {
                    alert('Pembayaran berhasil!');
                    window.location.href = "{{ route('produk') }}";
                },
                onPending: function(result) {
                    alert('Menunggu pembayaran');
                    window.location.href = "{{ route('produk') }}";
                },
                onError: function(result) {
                    alert('Pembayaran gagal');
                },
                onClose: function() {
                    modal.hide();
                }
            });
        }, { once: true });

    } catch (e) {
        console.error(e);
        alert('Terjadi kesalahan sistem');
    }
});
</script>
@endsection