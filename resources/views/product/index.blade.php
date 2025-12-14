@extends('layouts.product')

@section('content')
<style>
:root {
    --navy: #0B2447;
    --navy-soft: #19376D;
    --silver: #A9B4C2;
    --gold: #D4A017;
    --cream: #F2EDE4;
}

/* 🎨 CARD PRODUK */
.product-card {
    border: 0;
    border-radius: 18px;
    overflow: hidden;
    background: var(--cream);
    transition: all 0.3s ease;
    position: relative;
    box-shadow: 0 6px 12px rgba(11, 36, 71, 0.2);
}

.product-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 14px 28px rgba(11, 36, 71, 0.28);
}

/* 🖼️ Gambar produk */
.product-card img {
    height: 200px;
    object-fit: cover;
    border-bottom: 3px solid var(--navy);
}

/* 🔖 Badge status */
.badge-status {
    position: absolute;
    top: 12px;
    left: 12px;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 700;
}

/* 🛒 Floating Cart */
#floating-cart {
    width: 280px !important;
    border-radius: 24px;
    background: var(--cream);
    border: 2px solid var(--navy);
    box-shadow: 0 12px 24px rgba(0, 0, 0, .2);
    animation: fadeInCart .4s ease;
}

#floating-cart h5,
#floating-cart span {
    color: var(--navy);
}

/* Item list */
#floating-cart ul li {
    background: var(--silver);
    padding: 8px 10px;
    border-radius: 10px;
}

/* Scrollbar */
#floating-cart ul::-webkit-scrollbar-thumb {
    background: var(--navy-soft);
}

/* Button qty */
#floating-cart button {
    border-radius: 8px !important;
    background: var(--cream);
    border: 1px solid var(--navy-soft);
}

/* Input qty */
.product-card input[type="number"] {
    border-radius: 12px;
    border: 2px solid var(--navy);
}

/* Tombol tambah */
.product-card .btn-success {
    background: var(--navy);
    border: none;
    font-weight: 600;
    padding: 6px 12px;
}

.product-card .btn-success:hover {
    background: var(--navy-soft);
    transform: scale(1.03);
}

/* Checkout button */
#floating-cart a.btn-warning {
    background: var(--gold) !important;
    border: none !important;
    color: #fff !important;
    font-weight: 600;
}
</style>

<div class="container py-5">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h2 class="fw-bold text-dark mb-0">Daftar Produk</h2>
        <small class="text-muted"><i class="bi bi-calendar-check me-1"></i>{{ now()->format('d M Y, H:i') }}</small>
    </div>

    <!-- Filter -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari produk...">
        </div>
        <div class="col-md-3">
            <select name="kategori" class="form-select">
                <option value="none">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->name }}" {{ request('kategori')==$cat->name?'selected':'' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select" onchange="applySortFilter(this.value)">
                <option value="stok_terbanyak" {{ request('sort')=='stok_terbanyak'?'selected':'' }}>Stok Terbanyak</option>
                <option value="stok_sedikit" {{ request('sort')=='stok_sedikit'?'selected':'' }}>Stok Sedikit</option>
                <option value="terbaru" {{ request('sort')=='terbaru'?'selected':'' }}>Terbaru</option>
                <option value="terlama" {{ request('sort')=='terlama'?'selected':'' }}>Terlama</option>
                <option value="nama_az" {{ request('sort')=='nama_az'?'selected':'' }}>Nama A-Z</option>
                <option value="nama_za" {{ request('sort')=='nama_za'?'selected':'' }}>Nama Z-A</option>
            </select>
        </div>
    </form>

    <!-- Grid Produk -->
    <div class="row gy-4">
        @forelse ($items as $item)
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
            <div class="card product-card position-relative shadow-sm">

                @php
                    $imgPath = $item->image && file_exists(public_path('storage/'.$item->image))
                        ? asset('storage/'.$item->image)
                        : asset('default.png');
                @endphp

                <img src="{{ $imgPath }}" alt="{{ $item->name }}" class="card-img-top">

                @if($item->stock == 0)
                    <span class="badge-status bg-danger text-white">Habis</span>
                @elseif($item->stock < 5)
                    <span class="badge-status bg-warning text-dark">Menipis</span>
                @endif

                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="fw-semibold mb-2 text-truncate">{{ $item->name }}</h5>
                    <p class="small mb-1"><i class="bi bi-tag me-1"></i> Kategori: <span class="fw-semibold text-dark">{{ $item->category->name ?? '-' }}</span></p>
                    <p class="small mb-3"><i class="bi bi-box me-1"></i> Stok: <span class="fw-semibold {{ $item->stock==0?'text-danger':'text-success' }}">{{ $item->stock }}</span></p>

                    <div class="d-flex align-items-center gap-2">
                        <input type="number" id="qty-{{ $item->id }}" class="form-control text-center border-warning" value="1" min="1" max="{{ $item->stock }}" {{ $item->stock==0?'disabled':'' }}>
                        <button class="btn btn-success btn-sm add-to-cart" data-item="{{ $item->id }}" {{ $item->stock==0?'disabled':'' }}>
                            <i class="bi bi-cart-plus me-1"></i> Tambah
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
            <p class="text-muted mb-0">Tidak ada produk ditemukan.</p>
        </div>
        @endforelse
    </div>

    @if ($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-4 d-flex justify-content-center">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>


<!-- FLOATING CART -->
<div id="floating-cart" class="position-fixed end-0 top-50 translate-middle-y me-3 shadow-lg p-3 bg-white rounded-4 border border-warning" style="width: 260px; z-index: 1050;">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0"><i class="bi bi-cart3 me-1"></i> Keranjang</h5>
        <span id="cart-count" class="badge rounded-pill bg-danger">{{ $cartCount ?? 0 }}</span>
    </div>

    <ul id="cart-items" class="list-unstyled mb-2" style="max-height: 300px; overflow-y: auto;">
        @if($cartItems && $cartItems->count() > 0)
            @foreach($cartItems as $c)
                <li class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ $c->item->name }}</span>
                    <div class="d-flex gap-2 align-items-center">
                        <button class="btn btn-sm btn-light px-2" onclick="updateQty({{ $c->item_id }}, {{ $c->quantity - 1 }})">-</button>
                        <span class="fw-bold">{{ $c->quantity }}</span>
                        <button class="btn btn-sm btn-light px-2" onclick="updateQty({{ $c->item_id }}, {{ $c->quantity + 1 }})">+</button>
                        <button class="btn btn-danger btn-sm px-2" onclick="deleteItem({{ $c->item_id }})">x</button>
                    </div>
                </li>
            @endforeach
        @else
            <li class="text-muted">Belum ada item</li>
        @endif
    </ul>

    <a href="{{ route('checkout.page') }}" class="btn btn-warning w-100">
        Checkout
    </a>
</div>
@endsection


@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){

    const search = document.querySelector('input[name="q"]');
    if(search){
        search.addEventListener('input', function(){
            const url = new URL(window.location.href);
            url.searchParams.set('q', this.value);
            window.location.href = url.toString();
        });
    }

    const kategori = document.querySelector('select[name="kategori"]');
    if(kategori){
        kategori.addEventListener('change', function(){
            const url = new URL(window.location.href);
            url.searchParams.set('kategori', this.value);
            window.location.href = url.toString();
        });
    }

    window.applySortFilter = function(sortValue) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sortValue);
        window.location.href = url.toString();
    }

    document.querySelectorAll('.add-to-cart').forEach(btn=>{
        btn.addEventListener('click', function(e){
            e.preventDefault();
            const itemId = this.dataset.item;
            const quantity = document.getElementById('qty-'+itemId).value;

            fetch("{{ route('produk.add_to_guest_cart') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({item_id:itemId, quantity:quantity})
            })
            .then(res=>res.json())
            .then(data=>{
                refreshCartList(data.cart_items);
            })
        });
    });

    function refreshCartList(items){
        const list = document.getElementById('cart-items');
        const count = document.getElementById('cart-count');
        list.innerHTML = '';

        if(!items || items.length === 0){
            list.innerHTML = `<li class="text-muted">Belum ada item</li>`;
            count.textContent = 0;
            return;
        }

        items.forEach(i=>{
            list.innerHTML += `
                <li class="d-flex justify-content-between align-items-center mb-2">
                    <span>${i.name}</span>
                    <div class="d-flex gap-2 align-items-center">
                        <button class="btn btn-sm btn-light px-2" onclick="updateQty(${i.id}, ${i.quantity - 1})">-</button>
                        <span class="fw-bold">${i.quantity}</span>
                        <button class="btn btn-sm btn-light px-2" onclick="updateQty(${i.id}, ${i.quantity + 1})">+</button>
                        <button class="btn btn-danger btn-sm px-2" onclick="deleteItem(${i.id})">x</button>
                    </div>
                </li>
            `;
        });

        count.textContent = items.reduce((acc, cur)=> acc + cur.quantity, 0);
    }

    window.updateQty = function(item_id, quantity){
        if(quantity < 1) return;

        fetch("{{ route('cart.update') }}", {
            method: "POST",
            headers: {
                "Content-Type":"application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ item_id, quantity })
        })
        .then(res => res.json())
        .then(data => refreshCartList(data.cart_items));
    }

    window.deleteItem = function(item_id){
        fetch("{{ route('cart.delete') }}", {
            method: "POST",
            headers: {
                "Content-Type":"application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ item_id })
        })
        .then(res => res.json())
        .then(data => refreshCartList(data.cart_items));
    }

});
</script>
@endsection