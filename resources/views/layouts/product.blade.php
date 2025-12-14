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
:root {
    --primary: #ff9900;
    --primary-dark: #ff7a00;
    --text-dark: #1f1f1f;
    --text-light: #6c757d;
}

body {
    font-family: 'Inter', sans-serif;
    background-color: #fff;
    color: var(--text-dark);
}

/* 🌐 NAVBAR */
.navbar {
    transition: all 0.4s ease-in-out;
    padding: 0.8rem 2rem;
    z-index: 1050;
    backdrop-filter: blur(6px);
    background-color: rgba(255, 255, 255, 0.8);
}

.navbar.scrolled {
    background-color: rgba(255, 255, 255, 0.95);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

/* Logo */
.navbar-brand .navbar-logo {
    height: 48px;
    width: auto;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    transition: transform 0.3s ease;
}

.navbar-brand:hover .navbar-logo {
    transform: scale(1.05);
}

/* Brand text */
.brand-text {
    font-weight: 900;
    font-size: 1.7rem;
    letter-spacing: 1px;
    color: var(--primary-dark);
    transition: color 0.3s ease;
}

.navbar:hover .brand-text {
    color: var(--primary);
}

/* 🔙 Tombol Kembali */
.btn-back {
    background: linear-gradient(90deg, var(--primary-dark), var(--primary));
    color: #fff !important;
    border-radius: 25px;
    padding: 8px 22px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 3px 8px rgba(255, 153, 0, 0.4);
}

.btn-back:hover {
    opacity: 0.95;
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 6px 14px rgba(255, 153, 0, 0.6);
}

/* ♻️ Tombol Refund */
.btn-refund {
    background: transparent;
    color: var(--primary-dark) !important;
    border: 2px solid var(--primary-dark);
    border-radius: 25px;
    padding: 7px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-refund:hover {
    background: linear-gradient(90deg, var(--primary-dark), var(--primary));
    color: #fff !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(255, 153, 0, 0.4);
}


/* Responsive */
@media (max-width: 992px) {
    .navbar {
        padding: 1rem;
    }
    .brand-text {
        font-size: 1.4rem;
    }
    .navbar-logo {
        height: 36px;
    }
}
</style>
</head>

<body>

<!-- 🧭 NAVBAR PRODUK -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container d-flex align-items-center justify-content-between">

        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="#">
            <img src="{{ asset('assets/img/icons/logo1.jpeg') }}" alt="Logo UMARO" class="navbar-logo">
            <span class="brand-text">UMARO</span>
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarProduct">
            <i class="bi bi-list fs-2 text-dark"></i>
        </button>

        <!-- MENU PRODUK -->
        <div class="collapse navbar-collapse justify-content-end gap-3" id="navbarProduct">

            <!-- ♻️ Tombol Refund -->
            <a href="{{ route('refund.form') }}" class="btn-refund mt-3 mt-lg-0">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Refund
            </a>

            <!-- 🔙 Tombol Kembali -->
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
    // Scroll Blur Navbar
    window.addEventListener('scroll', function () {
        const navbar = document.querySelector('.navbar');
        navbar.classList.toggle('scrolled', window.scrollY > 20);
    });

    AOS.init({ duration: 900, once: true, easing: 'ease-in-out' });
</script>

@yield('scripts')
</body>
</html>
