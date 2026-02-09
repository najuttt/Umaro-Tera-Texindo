@extends('layouts.product')
@if(session('error'))
<script>
    alert('❌ Keranjang Terhapus: {{ session('error') }}');
</script>
@endif

@if(session('success'))
<script>
    alert('✅ Keranjang Tersimpan: {{ session('success') }}');
</script>
@endif


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

/* ✨ FLOATING CART - SIMPLE & ELEGANT ===== */
#floating-cart{
    width: 340px;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(11,36,71,.12);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#floating-cart.minimized {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    box-shadow: 0 4px 20px rgba(212,160,23,.3);
}

/* Cart Header - Simplified */
.cart-header {
    background: var(--navy);
    padding: 16px 20px;
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
}

.cart-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cart-title {
    color: #fff;
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.cart-title i {
    color: var(--gold);
    font-size: 1.1rem;
}

.cart-header-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

.cart-badge {
    background: var(--gold);
    color: #000;
    font-weight: 700;
    font-size: 0.75rem;
    padding: 4px 10px;
    border-radius: 12px;
    min-width: 28px;
    text-align: center;
}

.cart-toggle-btn {
    background: rgba(255,255,255,0.15);
    border: none;
    color: var(--gold);
    width: 24px;
    height: 24px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.cart-toggle-btn:hover {
    background: rgba(255,255,255,0.25);
}

.cart-toggle-btn i {
    font-size: 0.85rem;
}

/* Minimized Icon */
.cart-icon-minimized {
    display: none;
    position: absolute;
    font-size: 1.6rem;
    color: #fff;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

/* Cart Content */
.cart-content {
    padding: 16px;
}

/* Minimized State */
#floating-cart.minimized .cart-content,
#floating-cart.minimized .cart-header-content {
    display: none;
}

#floating-cart.minimized .cart-header {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    padding: 0;
    background: linear-gradient(135deg, var(--navy), var(--navy-soft));
}

#floating-cart.minimized .cart-icon-minimized {
    display: block;
}

#floating-cart.minimized .cart-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    min-width: 26px;
    height: 26px;
    padding: 0;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 3px 12px rgba(212,160,23,.5);
    border: 2px solid #fff;
}

/* Cart Items Container */
#cart-items {
    max-height: 280px;
    overflow-y: auto;
    margin-bottom: 12px;
}

/* Minimal Scrollbar */
#cart-items::-webkit-scrollbar {
    width: 4px;
}

#cart-items::-webkit-scrollbar-track {
    background: transparent;
}

#cart-items::-webkit-scrollbar-thumb {
    background: rgba(212,160,23,.3);
    border-radius: 10px;
}

#cart-items::-webkit-scrollbar-thumb:hover {
    background: var(--gold);
}

/* Cart Item - Clean Design */
.cart-item {
    background: #fff;
    border: 1px solid rgba(11,36,71,.08);
    border-radius: 12px;
    padding: 12px;
    margin-bottom: 8px;
    transition: all 0.2s ease;
}

.cart-item:hover {
    border-color: var(--gold);
    box-shadow: 0 2px 12px rgba(212,160,23,.15);
}

.cart-item-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 8px;
}

.cart-name {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--navy);
    line-height: 1.3;
    flex: 1;
    margin-right: 8px;
}

.cart-item-price {
    color: var(--gold);
    font-weight: 700;
    font-size: 0.85rem;
    white-space: nowrap;
}

.cart-item-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cart-qty-controls {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(11,36,71,.04);
    padding: 4px 8px;
    border-radius: 8px;
}

.btn-cart-qty {
    background: #fff;
    border: 1px solid rgba(11,36,71,.15);
    color: var(--navy);
    width: 24px;
    height: 24px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    font-weight: 700;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-cart-qty:hover {
    background: var(--gold);
    border-color: var(--gold);
    color: #000;
    transform: scale(1.05);
}

.cart-qty-display {
    font-weight: 700;
    color: var(--navy);
    min-width: 24px;
    text-align: center;
    font-size: 0.85rem;
}

.btn-cart-delete {
    background: transparent;
    border: 1px solid rgba(220,53,69,.2);
    color: #dc3545;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-cart-delete:hover {
    background: #dc3545;
    border-color: #dc3545;
    color: #fff;
    transform: scale(1.05);
}

/* Cart Total - Minimalist */
.cart-total {
    border-top: 1px solid rgba(11,36,71,.1);
    padding-top: 12px;
    margin-bottom: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cart-total-label {
    color: var(--navy);
    font-weight: 600;
    font-size: 0.9rem;
}

.cart-total-value {
    color: var(--gold);
    font-size: 1.2rem;
    font-weight: 800;
}

/* Checkout Button - Clean */
#btn-checkout {
    background: linear-gradient(135deg, var(--gold), var(--gold-soft));
    color: #000;
    border: none;
    border-radius: 12px;
    padding: 12px;
    font-weight: 700;
    font-size: 0.9rem;
    width: 100%;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(212,160,23,.25);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

#btn-checkout:not(:disabled):hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(212,160,23,.4);
}

#btn-checkout:disabled {
    background: #e9ecef;
    color: #adb5bd;
    cursor: not-allowed;
    box-shadow: none;
}

/* Empty Cart */
.cart-empty {
    text-align: center;
    padding: 32px 16px;
    color: var(--muted);
}

.cart-empty i {
    font-size: 2.5rem;
    color: var(--gold);
    opacity: 0.25;
    margin-bottom: 8px;
    display: block;
}

.cart-empty small {
    font-size: 0.85rem;
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

{{-- ✨ FLOATING CART - SIMPLE & ELEGANT ================= --}}
<div id="floating-cart"
     class="position-fixed end-0 top-50 translate-middle-y me-3"
     style="z-index:1050">

    {{-- Cart Header --}}
    <div class="cart-header" id="cart-header">
        {{-- Icon untuk minimized --}}
        <i class="bi bi-cart3 cart-icon-minimized"></i>
        <span id="cart-badge-mini" class="cart-badge">0</span>
        
        {{-- Normal header --}}
        <div class="cart-header-content">
            <div class="cart-title">
                <i class="bi bi-cart3"></i>
                <span>Keranjang</span>
            </div>
            <div class="cart-header-right">
                <span id="cart-count" class="cart-badge">0</span>
                <button class="cart-toggle-btn" id="toggle-cart" type="button">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Cart Content --}}
    <div class="cart-content">
        {{-- Cart Items --}}
        <ul id="cart-items" class="list-unstyled">
            <li class="cart-empty">
                <i class="bi bi-cart-x"></i>
                <small>Keranjang kosong</small>
            </li>
        </ul>

        {{-- Cart Total --}}
        <div class="cart-total">
            <span class="cart-total-label">Total</span>
            <span id="cart-total" class="cart-total-value">Rp 0</span>
        </div>

        {{-- Checkout Button --}}
        <button 
            id="btn-checkout"
            type="button"
            disabled>
            <i class="bi bi-bag-check"></i>
            <span>Checkout</span>
        </button>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded',()=>{
    const grid = document.getElementById('product-grid');
    const search = document.getElementById('search');
    const kategori = document.getElementById('kategori');
    const sort = document.getElementById('sort');
    const checkoutBtn = document.getElementById('btn-checkout');
    const floatingCart = document.getElementById('floating-cart');
    const toggleBtn = document.getElementById('toggle-cart');
    const cartHeader = document.getElementById('cart-header');

    /* ===== TOGGLE CART ===== */
    function toggleCart() {
        floatingCart.classList.toggle('minimized');
        const icon = toggleBtn.querySelector('i');
        icon.classList.toggle('bi-chevron-right');
        icon.classList.toggle('bi-chevron-left');
        localStorage.setItem('cartMinimized', floatingCart.classList.contains('minimized'));
    }

    toggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleCart();
    });

    cartHeader.addEventListener('click', () => {
        if (floatingCart.classList.contains('minimized')) toggleCart();
    });

    if (localStorage.getItem('cartMinimized') === 'true') {
        floatingCart.classList.add('minimized');
        toggleBtn.querySelector('i').classList.replace('bi-chevron-right','bi-chevron-left');
    }

    /* ===== CHECKOUT ===== */
    checkoutBtn.addEventListener('click', function() {
        if (!this.disabled) window.location.href = "{{ route('checkout.page') }}";
    });

    /* ===== FILTER ===== */
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

    [search,kategori,sort].forEach(el=>el.addEventListener('input',fetchProducts));

    /* ===== LOAD CART ===== */
    function loadCart(){
        fetch("{{ route('cart.get') }}",{
            credentials:'same-origin',
            headers:{'X-Requested-With':'XMLHttpRequest'}
        })
        .then(r=>r.json())
        .then(d=>refreshCart(d.cart_items))
        .catch(()=>refreshCart([]));
    }

    /* ===== ADD TO CART ===== */
    function bindAddToCart(){
        document.querySelectorAll('.add-to-cart').forEach(btn=>{
            btn.onclick = ()=>{
                const id = btn.dataset.id;
                const qty = document.getElementById('qty-'+id)?.value || 1;

                fetch("{{ route('produk.add_to_guest_cart') }}",{
                    method:'POST',
                    credentials:'same-origin',
                    headers:{
                        'X-CSRF-TOKEN':"{{ csrf_token() }}",
                        'Content-Type':'application/json'
                    },
                    body:JSON.stringify({item_id:id,quantity:qty})
                })
                .then(res=>res.json())
                .then(data=>{
                    if(data.message) alert(data.message);
                    if(floatingCart.classList.contains('minimized')) {
                        setTimeout(toggleCart, 300);
                    }
                    loadCart();
                });
            };
        });
    }

    /* ===== UPDATE QTY ===== */
    window.updateQty = (id,qty)=>{
        if(qty < 1) return;

        fetch("{{ route('cart.update') }}",{
            method:'POST',
            credentials:'same-origin',
            headers:{
                'X-CSRF-TOKEN':"{{ csrf_token() }}",
                'Content-Type':'application/json'
            },
            body:JSON.stringify({item_id:id,quantity:qty})
        })
        .then(res=>res.json())
        .then(data=>{
            if(!data.success) alert(data.message || 'Jumlah melebihi stok');
            else loadCart();
        });
    };

    /* ===== DELETE ITEM ===== */
    window.deleteItem = id =>{
        fetch("{{ route('cart.delete') }}",{
            method:'POST',
            credentials:'same-origin',
            headers:{
                'X-CSRF-TOKEN':"{{ csrf_token() }}",
                'Content-Type':'application/json'
            },
            body:JSON.stringify({item_id:id})
        }).then(()=>loadCart());
    };

    /* ===== RENDER CART ===== */
    function refreshCart(items){
        const list = document.getElementById('cart-items');
        const count = document.getElementById('cart-count');
        const countMini = document.getElementById('cart-badge-mini');
        const totalEl = document.getElementById('cart-total');

        list.innerHTML = '';
        let totalQty = 0;
        let totalHarga = 0;

        if(!items || items.length === 0){
            list.innerHTML = `
                <li class="cart-empty">
                    <i class="bi bi-cart-x"></i>
                    <small>Keranjang kosong</small>
                </li>
            `;
            count.textContent = 0;
            countMini.textContent = 0;
            totalEl.textContent = 'Rp 0';
            checkoutBtn.disabled = true;
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
                <div class="cart-item-header">
                    <div class="cart-name">${i.name ?? '-'}</div>
                    <div class="cart-item-price">Rp ${sub.toLocaleString('id-ID')}</div>
                </div>
                <div class="cart-item-controls">
                    <div class="cart-qty-controls">
                        <button class="btn-cart-qty" onclick="updateQty(${i.item_id},${qty-1})">−</button>
                        <span class="cart-qty-display">${qty}</span>
                        <button class="btn-cart-qty" onclick="updateQty(${i.item_id},${qty+1})">+</button>
                    </div>
                    <button class="btn-cart-delete" onclick="deleteItem(${i.item_id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </li>`;
        });

        count.textContent = totalQty;
        countMini.textContent = totalQty;
        totalEl.textContent = 'Rp ' + totalHarga.toLocaleString('id-ID');
        checkoutBtn.disabled = false;
    }

    /* ===== INIT ===== */
    bindAddToCart();
    loadCart();
});
</script>
@endsection