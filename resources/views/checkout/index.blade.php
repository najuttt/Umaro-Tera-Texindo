@extends('layouts.checkout')

@section('content')
<div class="container py-4">

    <h5 class="fw-semibold mb-3">Daftar Barang</h5>

    @foreach($cart->guestCartItems as $item)
    <div class="d-flex align-items-center mb-3 border-bottom pb-2">
        <img src="{{ $item->item->image ? asset($item->item->image) : asset('images/default.png') }}"
             width="70" height="70" class="rounded me-3" style="object-fit:cover;">
        <div>
            <div class="fw-bold">{{ $item->item->name }}</div>
            <div class="text-muted small">Jumlah: {{ $item->quantity }}</div>
            <div class="fw-semibold">
                Rp {{ number_format($item->item->price * $item->quantity, 0, ',', '.') }}
            </div>
        </div>
    </div>
    @endforeach

    <div class="d-flex justify-content-between mt-3">
        <h5>Total</h5>
        <h5 class="fw-bold text-success">
            Rp {{ number_format($totalHarga, 0, ',', '.') }}
        </h5>
    </div>

    <div class="card p-3 shadow-sm mt-4">
        <h5 class="mb-3 fw-semibold">Data Pembeli</h5>

        <form id="checkoutForm">
            @csrf
            <input type="text" name="customer_name" class="form-control mb-2" placeholder="Nama" required>
            <input type="text" name="customer_phone" class="form-control mb-2" placeholder="Nomor HP (08...)" required>
            <textarea name="customer_address" class="form-control" placeholder="Alamat Lengkap" rows="3" required></textarea>
        </form>

        <button type="button"
                class="btn btn-success w-100 mt-3"
                data-bs-toggle="modal"
                data-bs-target="#confirmModal">
            Checkout via WhatsApp
        </button>
    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Checkout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Lanjutkan order via WhatsApp?
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success" id="confirmSend">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("confirmSend").addEventListener("click", async () => {

    const nama   = document.querySelector("[name='customer_name']").value.trim();
    let hp       = document.querySelector("[name='customer_phone']").value.trim();
    const alamat = document.querySelector("[name='customer_address']").value.trim();

    if (!nama || !hp || !alamat) {
        alert("Harap isi semua data");
        return;
    }

    if (hp.startsWith("08")) {
        hp = "628" + hp.substring(2);
    }

    // STEP 1: SIMPAN ORDER
    let checkout = await fetch("{{ route('produk.checkout_guest_cart') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            customer_name: nama,
            customer_phone: hp,
            customer_address: alamat
        })
    });

    if (!checkout.ok) {
        alert("Gagal menyimpan order");
        return;
    }

    // STEP 2: MINTA WA URL DARI BACKEND
    let wa = await fetch("{{ route('send.whatsapp') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            customer_name: nama,
            customer_phone: hp,
            customer_address: alamat
        })
    });

    let res = await wa.json();

    if (!res.success) {
        alert(res.message);
        return;
    }

    window.location.href = res.wa_url;
});
</script>
@endsection