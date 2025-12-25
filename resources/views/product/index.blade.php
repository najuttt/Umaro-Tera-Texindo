@extends('layouts.product')

@section('content')

<style>
:root{
    --navy:#0B2447;
    --navy-soft:#132F5C;
    --gold:#D4A017;
    --gold-soft:#E6C45C;
    --light:#F7F8FA;
    --muted:#6c757d;
}

/* ===== PAGE ===== */
body{
    background:#f4f6f9;
}

/* ===== CARD ===== */
.product-card{
    border-radius:18px;
    background:#fff;
    border:1px solid rgba(11,36,71,.08);
    box-shadow:0 10px 30px rgba(11,36,71,.08);
    transition:.35s ease;
    position:relative;
}
.product-card:hover{
    transform:translateY(-6px);
    box-shadow:0 18px 45px rgba(11,36,71,.15);
}

/* ===== IMAGE ===== */
.product-card img{
    transition:.4s ease;
}
.product-card:hover img{
    transform:scale(1.06);
}

/* ===== TERLARIS ===== */
.badge-terlaris{
    position:absolute;
    top:12px;
    right:12px;
    background:linear-gradient(135deg,var(--gold),var(--gold-soft));
    color:#000;
    font-size:.7rem;
    padding:6px 10px;
    border-radius:12px;
    box-shadow:0 6px 15px rgba(212,160,23,.45);
}

/* ===== PRICE ===== */
.price{
    color:var(--navy);
    font-weight:700;
}

/* ===== STOCK BADGE ===== */
.badge-stock{
    font-size:.7rem;
    padding:6px 10px;
    border-radius:12px;
}
.stock-ok{
    background:rgba(11,36,71,.08);
    color:var(--navy);
}
.stock-low{
    background:rgba(212,160,23,.15);
    color:#8a6d00;
}
.stock-out{
    background:#e9ecef;
    color:#6c757d;
}

/* ===== INPUT ===== */
.qty-input{
    border-radius:12px;
    border:1px solid rgba(11,36,71,.2);
    font-weight:600;
}

/* ===== BUTTON ===== */
.btn-cart-main{
    border-radius:12px;
    background:linear-gradient(135deg,var(--navy),var(--navy-soft));
    color:#fff;
    border:none;
}
.btn-cart-main:hover{
    background:linear-gradient(135deg,var(--navy-soft),var(--navy));
}

.btn-cart-main:disabled{
    background:#adb5bd;
    cursor:not-allowed;
}

/* ===== FLOATING CART ===== */
#floating-cart{
    width:320px;
    border-radius:22px;
    border:1px solid rgba(11,36,71,.15);
    background:#fff;
    box-shadow:0 20px 50px rgba(11,36,71,.25);
}

/* ===== CART ITEM ===== */
.cart-item{
    background:var(--light);
    border-radius:14px;
    padding:10px;
    margin-bottom:8px;
}
.cart-name{
    font-size:.85rem;
    font-weight:600;
    color:var(--navy);
}

/* ===== CART BTN ===== */
.btn-cart{
    border-radius:10px;
    font-size:.75rem;
}

/* ===== TOTAL ===== */
.cart-total{
    border-top:1px dashed rgba(11,36,71,.25);
    padding-top:10px;
    font-weight:700;
    color:var(--navy);
}

/* ===== PAGINATION NAVY GOLD ===== */
.pagination-navy{
    gap:6px;
}

.pagination-navy .page-link{
    border-radius:12px;
    border:1px solid rgba(11,36,71,.2);
    color:var(--navy);
    padding:8px 14px;
    font-weight:600;
    background:#fff;
    transition:.3s ease;
}

.pagination-navy .page-link:hover{
    background:rgba(11,36,71,.08);
    color:var(--navy);
}

.pagination-navy .page-item.active .page-link{
    background:linear-gradient(135deg,var(--gold),var(--gold-soft));
    color:#000;
    border:none;
    box-shadow:0 6px 18px rgba(212,160,23,.45);
}

.pagination-navy .page-item.disabled .page-link{
    opacity:.5;
    cursor:not-allowed;
}

</style>

<div class="container py-5">

    <h3 class="fw-bold mb-4">Daftar Produk</h3>

    {{-- ================= FILTER ================= --}}
    <div class="row mb-4 g-2">
        <div class="col-md-4">
            <input type="text" id="search"
                   class="form-control"
                   placeholder="Cari produk atau kategori...">
        </div>

        <div class="col-md-3">
            <select id="kategori" class="form-select">
                <option value="none">Semua Kategori</option>
                @foreach($categories as $c)
                    <option value="{{ $c->name }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select id="sort" class="form-select">
                <option value="nama_az">Nama A-Z</option>
                <option value="nama_za">Nama Z-A</option>
                <option value="terbaru">Terbaru</option>
                <option value="stok_terbanyak">Stok Terbanyak</option>
            </select>
        </div>
    </div>

<div class="row g-4" id="product-grid">
@foreach($items as $item)

@php
    $stok = $item->stock ?? 0;
    $habis = $stok <= 0;
    $menipis = $stok > 0 && $stok <= 5;
@endphp

<div class="col-md-3 col-sm-6">
    <div class="product-card p-3 h-100 d-flex flex-column">

        @if(isset($item->total_ordered) && $item->total_ordered > 5)
            <span class="badge-terlaris">TERLARIS</span>
        @endif

        <div class="ratio ratio-1x1 mb-3 rounded overflow-hidden">
            <img src="{{ asset('storage/'.$item->image) }}"
                 class="img-fluid object-fit-cover">
        </div>

        <h6 class="fw-semibold text-truncate mb-1">
            {{ $item->name }}
        </h6>

        <div class="price mb-2">
            Rp {{ number_format($item->price,0,',','.') }}
        </div>

        @if($habis)
            <span class="badge-stock stock-out">
                Stok Habis
            </span>
        @elseif($menipis)
            <span class="badge-stock stock-low">
                Sisa {{ $stok }} {{ $item->unit->name ?? '' }}
            </span>
        @else
            <span class="badge-stock stock-ok">
                Stok {{ $stok }} {{ $item->unit->name ?? '' }}
            </span>
        @endif

        <div class="mt-auto d-flex gap-2">
            <input type="number"
                id="qty-{{ $item->id }}"
                class="form-control qty-input text-center"
                value="1"
                min="1"
                max="{{ $stok }}"
                {{ $habis ? 'disabled' : '' }}>

            <button
                class="btn btn-cart-main add-to-cart"
                data-id="{{ $item->id }}"
                {{ $habis ? 'disabled' : '' }}>
                <i class="bi bi-cart-plus"></i>
            </button>
        </div>

    </div>
</div>

@endforeach
</div>

{{-- ================= PAGINATION ================= --}}
@if ($items->hasPages())
<div class="d-flex justify-content-center mt-5">
    <nav>
        <ul class="pagination pagination-navy">

            {{-- PREV --}}
            <li class="page-item {{ $items->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link"
                   href="{{ $items->previousPageUrl() ?? '#' }}">
                   &laquo;
                </a>
            </li>

            {{-- PAGE NUMBER --}}
            @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $items->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">
                        {{ $page }}
                    </a>
                </li>
            @endforeach

            {{-- NEXT --}}
            <li class="page-item {{ $items->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link"
                   href="{{ $items->nextPageUrl() ?? '#' }}">
                   &raquo;
                </a>
            </li>

        </ul>
    </nav>
</div>
@endif

{{-- ================= FLOATING CART ================= --}}
<div id="floating-cart"
     class="position-fixed end-0 top-50 translate-middle-y me-3 p-3 bg-white"
     style="z-index:1050">

    <div class="d-flex justify-content-between mb-2">
        <h6 class="fw-bold">
            <i class="bi bi-cart3"></i> Keranjang
        </h6>
        <span id="cart-count" class="badge bg-danger">0</span>
    </div>

    <ul id="cart-items" class="list-unstyled mb-2"
        style="max-height:260px;overflow:auto">
        <li class="text-muted small">Keranjang kosong</li>
    </ul>

    <div class="cart-total d-flex justify-content-between mb-2">
        <span>Total</span>
        <span id="cart-total">Rp 0</span>
    </div>

    <a href="{{ route('checkout.page') }}"
       id="btn-checkout"
       class="btn btn-warning w-100 fw-semibold disabled">
        Checkout
    </a>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded',()=>{

    const grid = document.getElementById('product-grid');
    const search = document.getElementById('search');
    const kategori = document.getElementById('kategori');
    const sort = document.getElementById('sort');

    /* ================= FILTER AJAX ================= */
    function fetchProducts(){
        const params = new URLSearchParams({
            q: search.value,
            kategori: kategori.value,
            sort: sort.value
        });

        fetch("{{ route('produk') }}?"+params,{
            headers:{'X-Requested-With':'XMLHttpRequest'}
        })
        .then(res=>res.text())
        .then(html=>{
            const dom = new DOMParser().parseFromString(html,'text/html');
            grid.innerHTML = dom.querySelector('#product-grid').innerHTML;
            bindAddToCart();
        });
    }

    [search,kategori,sort].forEach(el=>{
        el.addEventListener('input',fetchProducts);
    });

    /* ================= LOAD CART (SINGLE SOURCE OF TRUTH) ================= */
    function loadCart(){
        fetch("{{ route('cart.get') }}",{
            headers:{'X-Requested-With':'XMLHttpRequest'}
        })
        .then(r=>r.json())
        .then(d=>refreshCart(d.cart_items));
    }

    /* ================= ADD TO CART ================= */
    function bindAddToCart(){
        document.querySelectorAll('.add-to-cart').forEach(btn=>{
            btn.onclick = ()=>{
                const id = btn.dataset.id;
                const qty = document.getElementById('qty-'+id)?.value || 1;

                fetch("{{ route('produk.add_to_guest_cart') }}",{
                    method:'POST',
                    headers:{
                        'X-CSRF-TOKEN':"{{ csrf_token() }}",
                        'Content-Type':'application/json'
                    },
                    body:JSON.stringify({item_id:id,quantity:qty})
                })
                .then(()=>loadCart()); // ⬅️ WAJIB
            };
        });
    }

    /* ================= UPDATE QTY ================= */
    window.updateQty = (id,qty)=>{
        if(qty < 1) return;

        fetch("{{ route('cart.update') }}",{
            method:'POST',
            headers:{
                'X-CSRF-TOKEN':"{{ csrf_token() }}",
                'Content-Type':'application/json'
            },
            body:JSON.stringify({item_id:id,quantity:qty})
        })
        .then(()=>loadCart()); // ⬅️ WAJIB
    };

    /* ================= DELETE ITEM ================= */
    window.deleteItem = id =>{
        fetch("{{ route('cart.delete') }}",{
            method:'POST',
            headers:{
                'X-CSRF-TOKEN':"{{ csrf_token() }}",
                'Content-Type':'application/json'
            },
            body:JSON.stringify({item_id:id})
        })
        .then(()=>loadCart()); // ⬅️ WAJIB
    };

    /* ================= RENDER CART ================= */
    function refreshCart(items){
        const list = document.getElementById('cart-items');
        const count = document.getElementById('cart-count');
        const totalEl = document.getElementById('cart-total');
        const checkout = document.getElementById('btn-checkout');

        list.innerHTML = '';
        let totalQty = 0;
        let totalHarga = 0;

        if(!items || items.length === 0){
            list.innerHTML = '<li class="text-muted small">Keranjang kosong</li>';
            count.textContent = 0;
            totalEl.textContent = 'Rp 0';
            checkout.classList.add('disabled');
            return;
        }

        items.forEach(i=>{
            const qty = Number(i.quantity) || 0;
            const price = Number(i.price) || 0;
            const sub = qty * price;

            totalQty += qty;
            totalHarga += sub;

            list.innerHTML += `
            <li class="cart-item">
                <div class="cart-name">${i.name ?? '-'}</div>
                <small class="text-muted">
                    Subtotal: Rp ${sub.toLocaleString('id-ID')}
                </small>
                <div class="d-flex justify-content-end gap-1 mt-1">
                    <button class="btn btn-outline-secondary btn-cart"
                        onclick="updateQty(${i.item_id},${qty-1})">−</button>
                    <span class="fw-bold">${qty}</span>
                    <button class="btn btn-outline-secondary btn-cart"
                        onclick="updateQty(${i.item_id},${qty+1})">+</button>
                    <button class="btn btn-outline-danger btn-cart"
                        onclick="deleteItem(${i.item_id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </li>`;
        });

        count.textContent = totalQty;
        totalEl.textContent = 'Rp ' + totalHarga.toLocaleString('id-ID');
        checkout.classList.remove('disabled');
    }

    /* ================= INIT ================= */
    bindAddToCart();
    loadCart(); // ⬅️ INIT PERTAMA
});
</script>

@endsection