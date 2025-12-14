@extends('layouts.wellcome')

@section('content')

<!-- 🌇 HERO SECTION -->
<section id="awal" class="hero-section d-flex align-items-center min-vh-100 position-relative"
  style="background: url('{{ asset('assets/img/backgrounds/logo2.jpeg') }}') center/cover no-repeat;">
  <div class="hero-overlay-gradient"></div>
</section>

<!-- 🧠 ABOUT SECTION -->
<section id="about" class="py-5" style="background:#F2EDE4; color:#0B2447;">
  <div class="container" data-aos="fade-up">
    <div class="section-title text-center mb-5">
      <h2 class="fw-bold" style="color:#0B2447;">Tentang Sistem</h2>
      <p style="color:#0B2447;">Digitalisasi pengelolaan e-commerce yang lebih mudah serta transparansi dalam kinerja Umaro Tera Texindo.</p>
    </div>

    <div class="row align-items-center justify-content-center gy-5">
      <!-- 🖼️ Gambar Carousel -->
      <div class="col-lg-5 d-flex justify-content-center" data-aos="fade-right">
        <div id="aboutCarousel"
             class="carousel slide carousel-fade shadow-sm rounded-4 overflow-hidden w-100"
             data-bs-ride="carousel" data-bs-interval="4000"
             style="max-width: 420px; background:#0B2447;">
          <div class="carousel-inner">
            @for ($i = 1; $i <= 4; $i++)
              <div class="carousel-item {{ $i === 1 ? 'active' : '' }}">
                <img src="{{ asset('assets/img/about/about'.$i.'.png') }}"
                     class="d-block w-100 about-img"
                     alt="Slide {{ $i }}">
              </div>
            @endfor
          </div>

          <!-- 🔹 Tombol Lihat Produk -->
          <div class="text-center my-5" data-aos="fade-up" data-aos-delay="200">
              <a href="{{ route('produk') }}" 
                class="btn btn-lg px-5 py-3 fw-semibold rounded-3 shadow-sm"
                style="background:#D4A017; color:white; border:none;">
                  <i class="bi bi-box-seam me-2"></i> Lihat Produk
              </a>
          </div>

          <button class="carousel-control-prev" type="button" data-bs-target="#aboutCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon rounded-circle p-2" style="background:#0B2447;"></span>
            <span class="visually-hidden">Sebelumnya</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#aboutCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon rounded-circle p-2" style="background:#0B2447;"></span>
            <span class="visually-hidden">Berikutnya</span>
          </button>
        </div>
      </div>

      <!-- 🧾 Teks Deskripsi -->
      <div class="col-lg-6" data-aos="fade-left">
        <div class="about-text">
          <h4 class="fw-bold mb-3" style="color:#0B2447;">Digitalisasi Pengelolaan Produk</h4>
          <p style="color:#0B2447;">
            Sistem ini membantu proses pembelian milik Umaro Tera Texindo secara digital,
            sehingga pengelolaan menjadi lebih efisien, akurat, dan transparan.
          </p>
          <ul class="list-unstyled mt-3">
            <li><i class="bi bi-check-circle-fill me-2" style="color:#D4A017;"></i> Pendataan produk otomatis dan terintegrasi.</li>
            <li><i class="bi bi-check-circle-fill me-2" style="color:#D4A017;"></i> Mengurangi risiko kehilangan atau duplikasi data.</li>
            <li><i class="bi bi-check-circle-fill me-2" style="color:#D4A017;"></i> Efisiensi e-commerce berbasis web.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 📊 STATISTIK -->
<section id="stats" class="py-5" style="background:#A9B4C2; color:#0B2447;">
  <div class="container text-center" data-aos="fade-up">
    <h2 class="fw-bold mb-5" style="color:#0B2447;">Statistik Sistem</h2>
    <div class="row g-4 justify-content-center">
      <div class="col-6 col-md-4">
        <div class="stats-item py-5 px-4 rounded-4 shadow-sm" style="background:white;">
          <i class="bi bi-bar-chart-line fs-1 mb-3" style="color:#D4A017;"></i>
          <h3 class="fw-bold mb-1" style="color:#0B2447;">{{ $totalPengunjung ?? 0 }}</h3>
          <p class="fw-semibold mb-0" style="color:#0B2447;">Pengunjung Website</p>
        </div>
      </div>

      <div class="col-6 col-md-4">
        <div class="stats-item py-5 px-4 rounded-4 shadow-sm" style="background:white;">
          <i class="bi bi-person-check fs-1 mb-3" style="color:#D4A017;"></i>
          <h3 class="fw-bold mb-1" style="color:#0B2447;">{{ $pegawaiAktif ?? 0 }}</h3>
          <p class="fw-semibold mb-0" style="color:#0B2447;">Pegawai Aktif</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 📍 LOKASI -->
<section id="lokasi" class="py-5" style="background:#F2EDE4; color:#0B2447;">
  <div class="container" data-aos="fade-up">
    <div class="section-title text-center mb-4">
      <h2 class="fw-bold" style="color:#0B2447;">Lokasi Kami</h2>
      <p style="color:#0B2447;">Temukan lokasi Umaro Tera Texindo melalui peta berikut.</p>
    </div>

    <div class="map-container shadow rounded-4 overflow-hidden mx-auto" style="max-width: 900px;">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.962437417771!2d107.59425387499323!3d-6.895909993098043!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e63ec3b486df%3A0x485a589d63cf53a5!2sJl.%20Pasteur%20No.31%2C%20Pasir%20Kaliki%2C%20Kec.%20Cicendo%2C%20Kota%20Bandung!5e0!3m2!1sid!2sid!4v1731305900000!5m2!1sid!2sid"
        width="100%" height="360" style="border:0;" allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>

    <div class="text-center mt-3">
      <a href="https://www.google.com/maps/place/Marketing+office+Parahyangan+Gallery/@-6.948615,107.5245215,17z/data=!3m1!4b1!4m6!3m5!1s0x2e68ef1970f910ad:0xd4a461195253f1d1!8m2!3d-6.948615!4d107.5270964!16s%2Fg%2F11jgcc81gx?entry=ttu&g_ep=EgoyMDI1MTIwOC4wIKXMDSoKLDEwMDc5MjA3M0gBUAM%3D" target="_blank" class="btn px-4"
         style="background:#D4A017; color:white; border:none;">
        <i class="bi bi-geo-alt me-2"></i> Buka di Google Maps
      </a>
    </div>
  </div>
</section>

@endsection

@section('scripts')
<script>
  AOS.init({ duration: 900, once: true });
</script>

<style>
/* 🌇 Overlay gradient sesuai brand */
.hero-overlay-gradient {
  position: absolute; inset: 0;
  background: linear-gradient(120deg, rgba(11,36,71,0.75), rgba(212,160,23,0.55), rgba(242,237,228,0.4));
  z-index: 1;
  mix-blend-mode: multiply;
}

/* 🧠 Gambar Carousel */
.about-img {
  max-height: 280px;
  object-fit: contain;
  border-radius: 0.75rem;
  display: block;
  margin: 0 auto;
}

/* 📘 Kartu Tutorial */
.tutorial-card {
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.tutorial-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}
.tutorial-img {
  height: 240px;
  width: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}
.tutorial-card:hover .tutorial-img {
  transform: scale(1.08);
}

/* 📱 Responsif */
@media (max-width: 992px) {
  .about-img { max-height: 240px; }
  .tutorial-img { height: 210px; }
}
@media (max-width: 768px) {
  .about-img { max-height: 200px; }
  .tutorial-img { height: 180px; }
}
</style>
@endsection