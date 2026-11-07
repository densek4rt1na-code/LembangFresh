@extends('layouts.app')

@section('title', 'Sistem Informasi GIS Kecamatan Lembang - LembangFresh')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark m-0"><i class="fas fa-map-marked-alt text-forest me-2"></i> Sistem Informasi GIS Wilayah Kecamatan Lembang</h3>
        <p class="text-muted small m-0">Pemetaan Spasial Petani, Pembeli, Mitra Logistik, dan Simulasi Jalur Distribusi Hortikultura.</p>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-success-subtle text-success border px-3 py-2 rounded-pill">
            <i class="fas fa-satellite me-1"></i> Koordinat Lembang: -6.8120, 107.6180
        </span>
    </div>
</div>

<div class="row g-4">

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div id="gisMap" style="height: 540px; width: 100%; border-radius: 12px;"></div>

            <div class="d-flex flex-wrap gap-4 align-items-center justify-content-center p-3 bg-light rounded-3 mt-3 small">
                <div class="d-flex align-items-center">
                    <span class="badge rounded-circle bg-success p-1 me-2" style="width: 14px; height: 14px;"> </span>
                    <span class="fw-semibold text-dark">Petani Hortikultura ({{ $farmers->count() }})</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge rounded-circle bg-info p-1 me-2" style="width: 14px; height: 14px;"> </span>
                    <span class="fw-semibold text-dark">Pembeli / UMKM ({{ $buyers->count() }})</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge rounded-circle bg-warning p-1 me-2" style="width: 14px; height: 14px;"> </span>
                    <span class="fw-semibold text-dark">Mitra Pickup Lokal ({{ $mitras->count() }})</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h5 class="fw-bold text-dark mb-3">
                <i class="fas fa-calculator text-forest me-2"></i> Kalkulator Jarak & Tarif GIS
            </h5>
            <p class="small text-muted mb-3">
                Pilih titik kebun petani dan pembeli untuk menghitung jarak Haversine serta rekomendasi opsi pengiriman secara otomatis.
            </p>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Titik Asal (Kebun Petani):</label>
                <select id="simFarmer" class="form-select form-select-sm" onchange="runSimulation()">
                    @foreach($farmers as $f)
                        @if($f->lat && $f->lng)
                            <option value="{{ $f->id }}"
                                    data-lat="{{ $f->lat }}"
                                    data-lng="{{ $f->lng }}"
                                    data-name="{{ $f->name }}"
                                    data-deliver="{{ $f->delivery_available ? 1 : 0 }}"
                                    data-radius="{{ $f->delivery_radius }}"
                                    data-fee="{{ $f->delivery_fee }}">
                                {{ $f->name }} ({{ $f->address ?? 'Lembang' }})
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Titik Tujuan (Lokasi Pembeli):</label>
                <select id="simBuyer" class="form-select form-select-sm" onchange="runSimulation()">
                    @foreach($buyers as $b)
                        @if($b->lat && $b->lng)
                            <option value="{{ $b->id }}"
                                    data-lat="{{ $b->lat }}"
                                    data-lng="{{ $b->lng }}"
                                    data-name="{{ $b->name }}">
                                {{ $b->name }} ({{ $b->address ?? 'Lembang' }})
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="p-3 bg-light rounded-3 mb-3 border">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Jarak GIS Antar Titik:</span>
                    <span class="fs-4 fw-bold text-danger" id="simDistance">3.5 km</span>
                </div>
                <div class="small text-muted" id="simRouteInfo">Kebun Pak Agus &rarr; UMKM Salad Fresh</div>
            </div>

            <h6 class="fw-bold text-dark mb-2 small text-uppercase">Rekomendasi Biaya Distribusi:</h6>

            <div class="card p-2 mb-2 border border-light-subtle" id="cardAmbil">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small fw-semibold text-dark">1. Ambil Sendiri</span>
                    <span class="badge bg-success">Rp 0 (GRATIS)</span>
                </div>
            </div>

            <div class="card p-2 mb-2 border border-light-subtle" id="cardPetani">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small fw-semibold text-dark">2. Diantar Petani</span>
                    <span class="fw-bold text-forest small" id="simFarmerFee">Rp 25.000</span>
                </div>
                <div class="small text-muted" id="simFarmerStatus">Status: Tersedia dalam radius</div>
            </div>

            <div class="card p-2 mb-3 border border-light-subtle" id="cardMitra">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small fw-semibold text-dark">3. Mitra Pickup Lembang</span>
                    <span class="fw-bold text-primary small" id="simMitraFee">Rp 70.000</span>
                </div>
                <div class="small text-muted" id="simMitraFormula">Rp 50.000 + (4 km x Rp 5.000)</div>
            </div>

            <button type="button" class="btn btn-outline-forest btn-sm w-100" onclick="centerOnRoute()">
                <i class="fas fa-compress-arrows-alt me-1"></i> Pusatkan Peta pada Rute Ini
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let gisMap;
    let routePolyline;

    function deg2rad(deg) {
        return deg * (Math.PI / 180);
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = deg2rad(lat2 - lat1);
        const dLon = deg2rad(lon2 - lon1);
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return Math.round(R * c * 10) / 10;
    }

    function formatRp(num) {
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function runSimulation() {
        const farmerOpt = document.getElementById('simFarmer').selectedOptions[0];
        const buyerOpt = document.getElementById('simBuyer').selectedOptions[0];

        if (!farmerOpt || !buyerOpt) return;

        const fLat = parseFloat(farmerOpt.dataset.lat);
        const fLng = parseFloat(farmerOpt.dataset.lng);
        const fName = farmerOpt.dataset.name;
        const fDeliver = parseInt(farmerOpt.dataset.deliver) === 1;
        const fRadius = parseFloat(farmerOpt.dataset.radius) || 5;
        const fFee = parseInt(farmerOpt.dataset.fee) || 25000;

        const bLat = parseFloat(buyerOpt.dataset.lat);
        const bLng = parseFloat(buyerOpt.dataset.lng);
        const bName = buyerOpt.dataset.name;

        const dist = calculateDistance(fLat, fLng, bLat, bLng);
        document.getElementById('simDistance').textContent = dist + ' km';
        document.getElementById('simRouteInfo').textContent = `${fName} → ${bName}`;

        const cardPetani = document.getElementById('cardPetani');
        const simFarmerFee = document.getElementById('simFarmerFee');
        const simFarmerStatus = document.getElementById('simFarmerStatus');

        if (fDeliver && dist <= fRadius) {
            cardPetani.classList.remove('opacity-50');
            simFarmerFee.textContent = formatRp(fFee);
            simFarmerStatus.innerHTML = `<span class="badge bg-success">TERSEDIA</span> Jarak ${dist} km ≤ Radius ${fRadius} km`;
        } else {
            cardPetani.classList.add('opacity-50');
            simFarmerFee.textContent = '-';
            simFarmerStatus.innerHTML = `<span class="badge bg-secondary">TIDAK TERSEDIA</span> Di luar radius (${fRadius} km) / Nonaktif`;
        }

        const mitraFee = 50000 + (Math.ceil(dist) * 5000);
        document.getElementById('simMitraFee').textContent = formatRp(mitraFee);
        document.getElementById('simMitraFormula').textContent = `Tarif Dasar Rp 50.000 + (${Math.ceil(dist)} km x Rp 5.000)`;

        if (routePolyline) {
            gisMap.removeLayer(routePolyline);
        }

        routePolyline = L.polyline([[fLat, fLng], [bLat, bLng]], {
            color: '#d9480f',
            weight: 4,
            dashArray: '8, 8'
        }).addTo(gisMap);
    }

    function centerOnRoute() {
        if (routePolyline) {
            gisMap.fitBounds(routePolyline.getBounds(), { padding: [50, 50] });
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        gisMap = L.map('gisMap').setView([-6.8120, 107.6180], 12);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap kontributor'
        }).addTo(gisMap);

        @foreach($farmers as $f)
            @if($f->lat && $f->lng)
                L.marker([{{ $f->lat }}, {{ $f->lng }}])
                    .addTo(gisMap)
                    .bindPopup(`
                        <div style="font-size: 13px;">
                            <strong style="color: #245831;"><i class="fas fa-leaf"></i> {{ $f->name }}</strong><br>
                            <small class="text-muted">{{ $f->address }}</small><br>
                            <span class="badge bg-success mt-1">Petani Hortikultura</span>
                            <div class="mt-1 small">Layanan Antar: <strong>{{ $f->delivery_available ? 'Aktif (' . $f->delivery_radius . ' km)' : 'Nonaktif' }}</strong></div>
                        </div>
                    `);

                @if($f->delivery_available)
                    L.circle([{{ $f->lat }}, {{ $f->lng }}], {
                        color: '#245831',
                        fillColor: '#245831',
                        fillOpacity: 0.08,
                        radius: {{ $f->delivery_radius * 1000 }}
                    }).addTo(gisMap);
                @endif
            @endif
        @endforeach

        @foreach($buyers as $b)
            @if($b->lat && $b->lng)
                L.circleMarker([{{ $b->lat }}, {{ $b->lng }}], {
                    color: '#0284c7',
                    fillColor: '#38bdf8',
                    fillOpacity: 0.9,
                    radius: 8
                }).addTo(gisMap).bindPopup(`
                    <div style="font-size: 13px;">
                        <strong style="color: #0369a1;"><i class="fas fa-shopping-basket"></i> {{ $b->name }}</strong><br>
                        <small class="text-muted">{{ $b->address }}</small><br>
                        <span class="badge bg-info mt-1">Pembeli / UMKM</span>
                    </div>
                `);
            @endif
        @endforeach

        @foreach($mitras as $m)
            @if($m->lat && $m->lng)
                L.circleMarker([{{ $m->lat }}, {{ $m->lng }}], {
                    color: '#b45309',
                    fillColor: '#f59e0b',
                    fillOpacity: 0.9,
                    radius: 9
                }).addTo(gisMap).bindPopup(`
                    <div style="font-size: 13px;">
                        <strong style="color: #b45309;"><i class="fas fa-truck"></i> {{ $m->name }}</strong><br>
                        <small class="text-muted">{{ $m->vehicle_desc ?? 'Pickup Lembang' }}</small><br>
                        <span class="badge bg-warning text-dark mt-1">Mitra Logistik Lembang</span>
                    </div>
                `);
            @endif
        @endforeach

        runSimulation();
    });
</script>
@endpush

