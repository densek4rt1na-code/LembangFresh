@extends('layouts.app')

@section('title', 'Pasar Hortikultura LembangFresh - Hasil Panen Petani Lembang')

@section('content')

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-forest text-white">
    <div class="card-body p-4 p-md-5">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-lime text-forest fw-bold px-3 py-2 rounded-pill mb-3">
                    <i class="fas fa-map-marker-alt me-1"></i> GIS Pertanian Kec. Lembang, Bandung Barat
                </span>
                <h1 class="display-6 fw-extrabold mb-3 text-white">
                    Hasil Panen Lembang, Sampai ke Pembeli yang Tepat.
                </h1>
                <p class="lead text-white-50 mb-4 fs-6">
                    Temukan sayuran dan hortikultura segar langsung dari petani Lembang. Setiap produk diarahkan ke jalur pemasaran sesuai tingkatan mutu (Grade A, B, atau C) dengan koordinasi distribusi cerdas berbasis GIS.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#katalog" class="btn btn-lime px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-shopping-basket me-1"></i> Jelajahi Komoditas
                    </a>
                    <a href="{{ route('gis.index') }}" class="btn btn-outline-light px-4 py-2 rounded-pill">
                        <i class="fas fa-map-marked-alt me-1"></i> Buka Peta GIS
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center mt-4 mt-lg-0 d-none d-lg-block">
                <div class="bg-white bg-opacity-10 p-4 rounded-4 border border-white border-opacity-25 text-start">
                    <h6 class="text-lime fw-bold mb-3"><i class="fas fa-layer-group me-1"></i> 3 Jalur Pemasaran Berdasarkan Kualitas:</h6>
                    <div class="mb-2 small">
                        <span class="badge bg-success me-1">Grade A</span> <strong>Pasar Normal:</strong> Supermarket, Hotel, Restoran
                    </div>
                    <div class="mb-2 small">
                        <span class="badge bg-warning text-dark me-1">Grade B</span> <strong>Pasar Alternatif:</strong> UMKM Kuliner, Rumah Makan, Katering
                    </div>
                    <div class="small">
                        <span class="badge bg-secondary me-1">Grade C</span> <strong>Jalur Pengolahan:</strong> Bahan Industri Pasta, Saus, & Keripik
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white" id="katalog">
    <form action="{{ route('home') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                <input type="text" name="search" class="form-control bg-light border-start-0"
                       placeholder="Cari wortel, tomat, brokoli..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-md-2">
            <select name="grade" class="form-select bg-light">
                <option value="">Semua Grade Mutu</option>
                <option value="A" {{ request('grade') === 'A' ? 'selected' : '' }}>Grade A (Pasar Normal)</option>
                <option value="B" {{ request('grade') === 'B' ? 'selected' : '' }}>Grade B (Pasar Alternatif)</option>
                <option value="C" {{ request('grade') === 'C' ? 'selected' : '' }}>Grade C (Pengolahan)</option>
            </select>
        </div>

        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted">Rp</span>
                <input type="number" name="max_price" class="form-control bg-light border-start-0"
                       placeholder="Harga maks / kg" value="{{ request('max_price') }}">
            </div>
        </div>

        <div class="col-md-2">
            @auth
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="sort_distance" value="1" id="sortDist"
                           {{ request('sort_distance') ? 'checked' : '' }}>
                    <label class="form-check-label small fw-semibold" for="sortDist">
                        <i class="fas fa-location-arrow text-danger me-1"></i> Terdekat Dulu
                    </label>
                </div>
            @else
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i> <a href="{{ route('login') }}" class="text-decoration-none">Masuk</a> untuk hitung jarak
                </div>
            @endauth
        </div>

        <div class="col-md-1 d-grid">
            <button type="submit" class="btn btn-forest fw-semibold">
                Filter
            </button>
        </div>
    </form>
</div>

<div class="row g-4">

    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold m-0 text-dark">
                <i class="fas fa-carrot text-success me-2"></i> Komoditas Hasil Panen Tersedia
                <span class="badge bg-success-subtle text-success ms-2">{{ $harvests->count() }} komoditas</span>
            </h5>
            @if(request()->hasAny(['search', 'grade', 'max_price', 'sort_distance']))
                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> Reset Filter
                </a>
            @endif
        </div>

        <div class="row g-3">

            @forelse($harvests as $harvest)
                <div class="col-md-6">
                    <x-product-card :harvest="$harvest" />
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-3">
                        <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
                        <h5 class="fw-bold text-dark">Belum ada komoditas panen yang cocok</h5>
                        <p class="text-muted small mb-3">Coba ubah kata kunci pencarian atau sesuaikan filter harga dan grade mutu.</p>
                        <div>
                            <a href="{{ route('home') }}" class="btn btn-sm btn-forest">Lihat Semua Komoditas</a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 sticky-top bg-white" style="top: 80px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold m-0 text-dark">
                    <i class="fas fa-map-marked-alt text-primary me-1"></i> Peta Sebaran Petani Lembang
                </h6>
                <a href="{{ route('gis.index') }}" class="small text-forest fw-semibold text-decoration-none">
                    Layar Penuh <i class="fas fa-external-link-alt ms-1"></i>
                </a>
            </div>
            <p class="small text-muted mb-2">
                Titik lokasi kebun petani hortikultura di wilayah Kecamatan Lembang.
            </p>

            <div id="mainMap" style="height: 380px; width: 100%;"></div>

            <div class="mt-3 pt-2 border-top small text-muted">
                <div class="d-flex align-items-center mb-1">
                    <span class="badge rounded-circle bg-success p-1 me-2" style="width: 12px; height: 12px;"> </span>
                    <span>Kebun Petani Lembang</span>
                </div>
                @auth
                    @if(auth()->user()->lat && auth()->user()->lng)
                        <div class="d-flex align-items-center">
                            <span class="badge rounded-circle bg-danger p-1 me-2" style="width: 12px; height: 12px;"> </span>
                            <span>Lokasi Anda ({{ auth()->user()->address ?? 'Kec. Lembang' }})</span>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5 mt-4 mb-2">
    <div class="text-center mb-4">
        <span class="badge bg-success-subtle text-forest fw-bold px-3 py-2 rounded-pill mb-2">
            <i class="fas fa-certificate me-1"></i> Arsitektur & Konsep LembangFresh
        </span>
        <h3 class="fw-bold text-dark">6 Pembeda Utama Sistem LembangFresh</h3>
        <p class="text-muted small mx-auto" style="max-width: 650px;">
            Bukan sekadar marketplace sayuran biasa, LembangFresh dirancang untuk optimalisasi pemasaran dan distribusi hasil hortikultura di wilayah Kecamatan Lembang melalui integrasi mutu, supply-demand matching, dan spasial GIS.
        </p>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <div class="p-3 bg-light rounded-3 h-100 border border-light-subtle">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-forest text-lime rounded-2 p-2 me-2 d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h6 class="fw-bold text-dark m-0">1. Quality-Based Marketing</h6>
                </div>
                <p class="small text-muted mb-0">
                    Setiap komoditas dikelompokkan ke 3 tingkatan mutu: <strong>Grade A</strong> (Pasar Normal/Supermarket), <strong>Grade B</strong> (Pasar Alternatif/UMKM), dan <strong>Grade C</strong> (Industri Pengolahan Saus/Pasta).
                </p>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="p-3 bg-light rounded-3 h-100 border border-light-subtle">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-forest text-lime rounded-2 p-2 me-2 d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h6 class="fw-bold text-dark m-0">2. Supply-Demand Matching</h6>
                </div>
                <p class="small text-muted mb-0">
                    Mencocokkan pasokan stok petani dengan kebutuhan pembeli secara otomatis berdasarkan jenis komoditas, grade mutu, harga batas, dan volume penyerapan.
                </p>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="p-3 bg-light rounded-3 h-100 border border-light-subtle">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-forest text-lime rounded-2 p-2 me-2 d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-map-location-dot"></i>
                    </div>
                    <h6 class="fw-bold text-dark m-0">3. GIS-Based Distribution</h6>
                </div>
                <p class="small text-muted mb-0">
                    Memetakan koordinat kebun petani, pembeli, dan armada di Kecamatan Lembang. Menghitung jarak spasial riil menggunakan algoritma geodesi Haversine.
                </p>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="p-3 bg-light rounded-3 h-100 border border-light-subtle">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-forest text-lime rounded-2 p-2 me-2 d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                    <h6 class="fw-bold text-dark m-0">4. Fleksibilitas 3 Jalur Kirim</h6>
                </div>
                <p class="small text-muted mb-0">
                    Menyediakan 3 opsi pengiriman: <strong>Ambil Sendiri</strong> (Rp 0), <strong>Diantar Langsung Petani</strong> (dalam radius), dan <strong>Mitra Pengangkut</strong> (Pickup L300 lokal).
                </p>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="p-3 bg-light rounded-3 h-100 border border-light-subtle">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-forest text-lime rounded-2 p-2 me-2 d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-sliders"></i>
                    </div>
                    <h6 class="fw-bold text-dark m-0">5. Farmer-Controlled Delivery</h6>
                </div>
                <p class="small text-muted mb-0">
                    Petani memiliki kendali mandiri untuk mengaktifkan/menonaktifkan layanan antar, menentukan batas radius jelajah (km), tarif tetap, dan ketersediaan armada.
                </p>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="p-3 bg-light rounded-3 h-100 border border-light-subtle">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-forest text-lime rounded-2 p-2 me-2 d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-recycle"></i>
                    </div>
                    <h6 class="fw-bold text-dark m-0">6. Penyerapan Pasar Alternatif</h6>
                </div>
                <p class="small text-muted mb-0">
                    Hasil panen yang melimpah atau berukuran non-standar tidak dibuang atau dijual rugi, melainkan disalurkan ke UMKM kuliner dan industri pengolahan pangan.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const map = L.map('mainMap').setView([-6.8120, 107.6180], 12);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap kontributor'
        }).addTo(map);

        @foreach($farmerMarkers as $farmer)
            @if($farmer->lat && $farmer->lng)
                L.marker([{{ $farmer->lat }}, {{ $farmer->lng }}])
                    .addTo(map)
                    .bindPopup(`
                        <div style="font-size: 13px;">
                            <strong style="color: #245831;">{{ $farmer->name }}</strong><br>
                            <small class="text-muted"><i class="fas fa-map-pin"></i> {{ $farmer->address }}</small><br>
                            <span class="badge bg-success mt-1">Petani Hortikultura</span>
                        </div>
                    `);
            @endif
        @endforeach

        @auth
            @if(auth()->user()->lat && auth()->user()->lng)
                L.circleMarker([{{ auth()->user()->lat }}, {{ auth()->user()->lng }}], {
                    color: '#dc2626',
                    fillColor: '#ef4444',
                    fillOpacity: 0.8,
                    radius: 9
                }).addTo(map).bindPopup("<b>Lokasi Anda:</b> {{ auth()->user()->name }}");
            @endif
        @endauth
    });
</script>
@endpush

