<footer class="bg-forest-dark text-white-50 py-5 mt-auto border-top border-success-subtle">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="text-white fw-bold d-flex align-items-center mb-3">
                    <span class="brand-icon bg-white text-forest rounded-2 d-inline-flex align-items-center justify-content-center me-2 p-1" style="width: 28px; height: 28px;">
                        <i class="fas fa-leaf text-success fs-6"></i>
                    </span>
                    <span>Lembang<span class="text-lime">Fresh</span></span>
                </h5>
                <p class="small text-white-50">
                    Sistem Informasi Geografis (GIS) untuk Optimalisasi Pemasaran dan Distribusi Hasil Hortikultura di Kecamatan Lembang, Kabupaten Bandung Barat.
                </p>
                <div class="d-flex gap-2">
                    <span class="badge bg-success text-white"><i class="fas fa-shield-halved me-1"></i> Terintegrasi</span>
                    <span class="badge bg-lime text-forest"><i class="fas fa-map-location-dot me-1"></i> Berbasis GIS</span>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="text-white fw-semibold mb-3">Tautan Cepat</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none hover-lime">Pasar Komoditas</a></li>
                    <li class="mb-2"><a href="{{ route('gis.index') }}" class="text-white-50 text-decoration-none hover-lime">Peta GIS Lembang</a></li>
                    <li class="mb-2"><a href="{{ route('matching.index') }}" class="text-white-50 text-decoration-none hover-lime">Supply Matching</a></li>
                    @auth
                        <li class="mb-2"><a href="{{ route('dashboard') }}" class="text-white-50 text-decoration-none hover-lime">Dashboard Saya</a></li>
                    @else
                        <li class="mb-2"><a href="{{ route('login') }}" class="text-white-50 text-decoration-none hover-lime">Masuk Sistem</a></li>
                        <li class="mb-2"><a href="{{ route('register') }}" class="text-white-50 text-decoration-none hover-lime">Daftar Akun Baru</a></li>
                    @endauth
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="text-white fw-semibold mb-3">Jalur Pemasaran Kualitas</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2 text-white-50"><span class="badge bg-success me-1">Grade A</span> Pasar Normal (Supermarket/Hotel)</li>
                    <li class="mb-2 text-white-50"><span class="badge bg-warning text-dark me-1">Grade B</span> Pasar Alternatif (UMKM/Katering)</li>
                    <li class="mb-2 text-white-50"><span class="badge bg-secondary me-1">Grade C</span> Jalur Pengolahan (Saus/Pasta/Keripik)</li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="text-white fw-semibold mb-3">Wilayah Cakupan GIS</h6>
                <p class="small text-white-50 mb-2">
                    <i class="fas fa-location-dot text-danger me-1"></i> Fokus Layanan: Kecamatan Lembang, Kabupaten Bandung Barat, Jawa Barat.
                </p>
                <p class="small text-white-50 mb-0">
                    <i class="fas fa-truck text-lime me-1"></i> Koordinasi Distribusi: Ambil Sendiri (Rp 0), Antar Petani, & Mitra Pengangkut Pickup Lokal.
                </p>
            </div>
        </div>

        <div class="pt-4 border-top border-secondary text-center small text-white-50">
            <div>&copy; {{ date('Y') }} <strong>LembangFresh</strong> — Sistem Informasi Berbasis GIS untuk Optimalisasi Pemasaran dan Distribusi Hasil Hortikultura di Kecamatan Lembang, Kabupaten Bandung Barat.</div>
        </div>
    </div>
</footer>

