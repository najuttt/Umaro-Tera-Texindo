<!doctype html>
<html lang="id" data-assets-path="{{ asset('assets') }}">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Produk – UMARO</title>

<!-- 🧩 Favicon -->
<link rel="icon" type="image/x-icon" href="{{ asset('assets/img/icons/logo1.jpeg') }}" />

<!-- Fonts & Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<!-- Bootstrap & AOS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<style>
/* =====================
   🎨 CORE COLOR (SAMA DENGAN LANDING)
===================== */
:root{
  --navy:#0B1C2D;
  --navy-soft:#13293D;
  --gold:#D4AF37;
  --gold-soft:#E6C15A;

  --text-dark:#0F172A;
  --text-light:#64748B;
  --white-soft:#F8FAFC;
}

/* =====================
   GLOBAL
===================== */
body{
  font-family:'Inter',sans-serif;
  background:var(--white-soft);
  color:var(--text-dark);
}

/* =====================
   NAVBAR
===================== */
.navbar{
  background:rgba(244,244,245,.85);
  backdrop-filter:blur(8px);
  box-shadow:0 6px 24px rgba(0,0,0,.35);
  transition:.4s;
}

.navbar.scrolled{
  background:rgba(246,247,248,.95);
}

.navbar-brand .navbar-logo{
  height:48px;
  filter:drop-shadow(0 3px 6px rgba(0,0,0,.4));
}

.brand-text{
  font-weight:900;
  font-size:1.7rem;
  letter-spacing:1px;
  color:var(--gold);
  transition:.3s;
}

.navbar:hover .brand-text{
  color:var(--gold-soft);
}

/* =====================
   BUTTON – KEMBALI
===================== */
.btn-back{
  background:linear-gradient(135deg,var(--gold),var(--gold-soft));
  color:#000 !important;
  border-radius:30px;
  padding:9px 24px;
  font-weight:700;
  box-shadow:0 6px 18px rgba(212,175,55,.45);
  transition:.3s;
}

.btn-back:hover{
  transform:translateY(-2px) scale(1.05);
  box-shadow:0 10px 30px rgba(212,175,55,.7);
}

/* =====================
   BUTTON – REFUND
===================== */
.btn-refund{
  background:transparent;
  border:2px solid var(--gold);
  color:var(--gold) !important;
  border-radius:30px;
  padding:8px 22px;
  font-weight:700;
  transition:.3s;
}

.btn-refund:hover{
  background:linear-gradient(135deg,var(--gold),var(--gold-soft));
  color:#000 !important;
  transform:translateY(-2px);
  box-shadow:0 8px 22px rgba(212,175,55,.55);
}

/* =====================
   RESPONSIVE
===================== */
@media(max-width:992px){
  .brand-text{font-size:1.4rem}
  .navbar-logo{height:38px}
}
</style>
</head>

<body>

<!-- 🧭 NAVBAR PRODUK -->
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container d-flex align-items-center justify-content-between">

    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="#">
      <img src="{{ asset('assets/img/icons/logo4.png') }}" class="navbar-logo">
      <span class="brand-text">UMARO</span>
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarProduct">
      <i class="bi bi-list fs-2 text-dark"></i>
    </button>

    <!-- Menu -->
    <div class="collapse navbar-collapse justify-content-end gap-3" id="navbarProduct">
      <a href="{{ route('refund.form') }}" class="btn-refund mt-3 mt-lg-0">
        <i class="bi bi-arrow-counterclockwise me-1"></i> Refund
      </a>

      <a href="{{ route('welcome') }}" class="btn-back mt-3 mt-lg-0">
        <i class="bi bi-arrow-left-circle me-1"></i> Kembali
      </a>
    </div>

  </div>
</nav>

<!-- MAIN -->
<main class="pt-5 mt-4">
  @yield('content')
</main>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
window.addEventListener('scroll',()=>{
  document.querySelector('.navbar')
    .classList.toggle('scrolled',window.scrollY>20);
});
AOS.init({duration:900,once:true,easing:'ease-in-out'});
</script>

@yield('scripts')
</body>
</html>
