    <!doctype html>
    <html lang="id" data-assets-path="{{ asset('assets') }}">
    <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Selamat Datang di UMARO</title>

    <!-- 🧩 Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/icons/logo1.jpeg') }}" />

    <!-- 🖋️ Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- 🧱 Bootstrap & AOS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
  /* 🎯 CORE COLORS */
  --navy: #0B1C2D;
  --navy-soft: #13293D;
  --gold: #D4AF37;
  --gold-soft: #E6C15A;

  --text-dark: #0F172A;
  --text-light: #64748B;
  --white-soft: #F8FAFC;
}

/* =====================
   GLOBAL
===================== */
body {
  font-family: 'Inter', sans-serif;
  background: var(--white-soft);
  color: var(--text-dark);
  scroll-behavior: smooth;
}

/* =====================
   NAVBAR
===================== */
.navbar {
  background: rgba(244, 244, 245, 0.85);
  backdrop-filter: blur(8px);
  transition: all 0.4s ease;
  box-shadow: 0 6px 24px rgba(0,0,0,.35);
}

.navbar.scrolled {
  background: rgba(246, 247, 248, 0.95);
}

.navbar-brand .navbar-logo {
  height: 48px;
  filter: drop-shadow(0 3px 6px rgba(0,0,0,.4));
}

.brand-text {
  font-weight: 900;
  font-size: 1.7rem;
  letter-spacing: 1px;
  color: var(--gold);
  transition: all .3s ease;
}

.navbar:hover .brand-text {
  color: var(--gold-soft);
}

/* =====================
   NAV LINKS
===================== */
.nav-link {
  color: #0c0c0c !important;
  font-weight: 500;
  position: relative;
  transition: all .35s ease;
}

.nav-link::after {
  content: "";
  position: absolute;
  left: 50%;
  bottom: -6px;
  width: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--gold), var(--gold-soft));
  transform: translateX(-50%);
  transition: all .35s ease;
  opacity: 0;
}

.nav-link:hover,
.nav-link.active {
  color: var(--gold) !important;
}

.nav-link:hover::after,
.nav-link.active::after {
  width: 60%;
  opacity: 1;
}

/* =====================
   LOGIN BUTTON
===================== */
.btn-login {
  background: linear-gradient(135deg, var(--gold), var(--gold-soft));
  color: #fdfdfd !important;
  border-radius: 30px;
  padding: 9px 24px;
  font-weight: 700;
  box-shadow: 0 6px 18px rgba(212,175,55,.45);
  transition: all .3s ease;
}

.btn-login:hover {
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 10px 30px rgba(212,175,55,.7);
}

/* =====================
   SECTION
===================== */
section {
  padding: 100px 0;
}

/* =====================
   FOOTER
===================== */
footer {
  background: linear-gradient(180deg, var(--navy), #050D16);
  color: #E5E7EB;
  border-top: 2px solid rgba(212,175,55,.25);
}

.footer-logo {
  height: 200px;
  filter: brightness(1.1) drop-shadow(0 4px 10px rgba(0,0,0,.6));
  transition: transform .3s ease;
}

.footer-logo:hover {
  transform: scale(1.06);
}

footer h5 {
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--gold);
  letter-spacing: .6px;
}

footer p {
  color: #CBD5E1;
}

/* =====================
   FOOTER LINK
===================== */
footer a {
  color: #E5E7EB;
  transition: all .3s ease;
}

footer a:hover {
  color: var(--gold);
  transform: scale(1.08);
}

/* =====================
   SHOPEE BUTTON
===================== */
footer a[href*="shopee"] {
  background: linear-gradient(135deg, var(--gold), var(--gold-soft)) !important;
  color: #000 !important;
  font-weight: 700;
  box-shadow: 0 6px 20px rgba(212,175,55,.45);
}

/* =====================
   RESPONSIVE
===================== */
@media (max-width: 992px) {
  .brand-text {
    font-size: 1.4rem;
  }
  .navbar-logo {
    height: 38px;
  }
}
    </style>
    </head>

    <body>

    <!-- 🧭 NAVBAR -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container d-flex align-items-center justify-content-between">

        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="#">
            <img src="{{ asset('assets/img/icons/logo4.png') }}" alt="Logo SIMBA" class="navbar-logo">
            <span class="brand-text">UMARO</span>
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="bi bi-list fs-2 text-dark"></i>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse justify-content-end align-items-center" id="navbarNav">
            <ul class="navbar-nav mb-2 mb-lg-0 align-items-lg-center">
            <li class="nav-item"><a class="nav-link active" href="#awal">Beranda</a></li>
            <li class="nav-item"><a class="nav-link" href="#about">Tentang</a></li>
            <li class="nav-item"><a class="nav-link" href="#stats">Statistik</a></li>
            <li class="nav-item"><a class="nav-link" href="#lokasi">Lokasi</a></li>
            </ul>
            <a href="{{ route('login') }}" class="btn-login ms-lg-4 mt-3 mt-lg-0">
            <i class="bi bi-box-arrow-in-right me-1"></i> Login
            </a>
        </div>
        </div>
    </nav>

    <!-- MAIN -->
    <main class="pt-5 mt-4">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="footer-section text-white mt-5">
        <div class="container py-4">
            <div class="row justify-content-between align-items-center text-center text-md-start">

                <!-- KIRI -->
                <div class="col-md-6 mb-3 mb-md-0 d-flex flex-column flex-md-row align-items-center gap-3">
                    <img src="{{ asset('assets/img/icons/logo3.png') }}"
                        alt="Logo"
                        class="footer-logo img-fluid">
                    <div class="text-md-start text-center">
                        <h5 class="fw-bold mb-1">Umaro Tera Texindo</h5>
                        <p class="mb-0 small">
                            Ruko Parahyangan, Sukawangi Kaler Ruko Parahyangan No.3, Jelegong, Kec. Kutawaringin, Kabupaten Bandung, Jawa Barat 40911
                        </p>
                    </div>
                </div>

                <!-- KANAN (Shopee Only) -->
                <div class="col-md-5 text-md-end">
                    <div class="d-flex justify-content-center justify-content-md-end">
                        <a href="https://shopee.co.id/search?keyword=teman%20jarum"
                        target="_blank"
                        class="d-flex align-items-center gap-2 px-3 py-2"
                        style="background:#ee4d2d; border-radius:10px; color:white; font-weight:600; text-decoration:none;">
                            <i class="bi bi-bag-fill"></i>
                            Shopee
                        </a>
                    </div>
                </div>

            </div>

            <hr class="border-light opacity-75 mt-4 mb-3">

            <div class="text-center small">
                <p class="mb-0">© {{ date('Y') }} <strong>UMARO</strong> — All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- 📜 Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // 🌫️ Scroll blur & shadow navbar
        window.addEventListener('scroll', function () {
        const navbar = document.querySelector('.navbar');
        navbar.classList.toggle('scrolled', window.scrollY > 20);
        });

        // 🎯 Smooth highlight active menu
        const sections = document.querySelectorAll("section[id]");
        const navLinks = document.querySelectorAll(".nav-link");

        function activateMenu() {
        let current = "";
        const scrollY = window.pageYOffset + 150; // offset lebih smooth

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
            current = section.getAttribute("id");
            }
        });

        navLinks.forEach(link => {
            link.classList.remove("active");
            const href = link.getAttribute("href").substring(1);
            if (href === current) {
            link.classList.add("active");
            }
        });
        }

        window.addEventListener("scroll", activateMenu);

        // 🖱️ Smooth scroll saat klik menu
        navLinks.forEach(link => {
        link.addEventListener("click", e => {
            e.preventDefault();
            const target = document.querySelector(link.getAttribute("href"));
            navLinks.forEach(l => l.classList.remove("active"));
            link.classList.add("active");
            window.scrollTo({
            top: target.offsetTop - 100,
            behavior: "smooth"
            });
        });
        });

        AOS.init({ duration: 900, once: true, easing: 'ease-in-out' });
    </script>

@yield('scripts')
    </body>
    </html>
