@extends('layouts.app')

@section('title', 'Supply-Demand Matching Hortikultura - LembangFresh')

@section('content')
<div class="card border-0 shadow-sm rounded-4 bg-forest text-white p-4 p-md-5 mb-4">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <span class="badge bg-lime text-forest fw-bold px-3 py-2 rounded-pill mb-3">
                <i class="fas fa-handshake me-1"></i> Supply-Demand Matching Engine
            </span>
            <h2 class="fw-extrabold text-white mb-2">Pencocokan Cerdas Pasokan Petani & Permintaan Pembeli</h2>
            <p class="text-white-50 m-0">
                Sistem LembangFresh menganalisis jenis komoditas, jumlah stok, tingkatan mutu (Grade A/B/C), harga maksimal, dan lokasi spasial untuk mempertemukan pasokan panen dengan kebutuhan pasar secara efisien.
            </p>
        </div>
        <div class="col-lg-4 text-center mt-3 mt-lg-0">
            <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25">
                <div class="text-lime fw-bold mb-1"><i class="fas fa-chart-line me-1"></i> Mekanisme Penyerapan Pasokan:</div>
                <div class="small text-white-50">Stok Petani (500 kg) &harr; Butuh Resto (300 kg) &rarr; <strong>MATCH 300 kg Terjual, Sisa 200 kg Tersedia</strong>.</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    @forelse($matchResults as $result)
        @php
            $demand = $result['demand'];
            $matches = $result['matches'];
        @endphp

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">

                <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                    <div>
                        <span class="badge bg-info text-white rounded-pill px-3 py-1 mb-1">
                            <i class="fas fa-bullhorn me-1"></i> Kebutuhan Terbuka
                        </span>
                        <h4 class="fw-bold text-dark m-0">{{ $demand->commodity }}</h4>
                        <div class="text-muted small">
                            Pembeli: <strong>{{ $demand->buyer->name ?? 'Pembeli' }}</strong> ({{ $demand->buyer->address ?? 'Lembang' }})
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fs-5 fw-bold text-success">Maks. Rp {{ number_format($demand->max_price_per_kg, 0, ',', '.') }}/kg</div>
                        <div class="small text-muted">Kebutuhan: <strong>{{ number_format($demand->quantity) }} kg</strong></div>
                        <x-status-badge :status="$demand->grade" type="grade" class="mt-1" />
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-2">
                    <i class="fas fa-check-circle text-success me-1"></i> Pasokan Petani yang Cocok (MATCH):
                </h6>

                @if($matches->count() > 0)
                    <div class="list-group list-group-flush mb-3">
                        @foreach($matches as $harvest)
                            @php
                                $isFullyMatched = $harvest->quantity >= $demand->quantity;
                                $surplus = $harvest->quantity - $demand->quantity;
                            @endphp

                            <div class="list-group-item px-0 py-3 border-light-subtle">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="fw-bold text-forest">
                                        <i class="fas fa-carrot me-1"></i> {{ $harvest->commodity }}
                                        <small class="text-muted fw-normal">({{ $harvest->farmer->name ?? 'Petani' }})</small>
                                    </div>
                                    <span class="badge bg-success">
                                        Rp {{ number_format($harvest->price_per_kg, 0, ',', '.') }}/kg
                                    </span>
                                </div>

                                <div class="p-2 bg-light rounded-3 small mb-2 border">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Stok Petani: <strong>{{ number_format($harvest->quantity) }} kg</strong></span>
                                        <span>Diserap: <strong class="text-success">{{ number_format($harvest->absorbed_qty ?? min($harvest->quantity, $demand->quantity)) }} kg</strong></span>
                                    </div>
                                    <div class="text-muted">
                                        @if($harvest->is_fully_matched ?? ($harvest->quantity >= $demand->quantity))
                                            <span class="badge bg-success-subtle text-success border border-success-subtle me-1">
                                                <i class="fas fa-check-double me-1"></i> 100% Kebutuhan Terserap
                                            </span>
                                            Sisa stok petani: <strong>{{ number_format($harvest->remaining_stock ?? ($harvest->quantity - $demand->quantity)) }} kg</strong> tetap tersedia untuk pembeli lain.
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle me-1">
                                                <i class="fas fa-pie-chart me-1"></i> Menyerap Sebagian
                                            </span>
                                            Seluruh stok ({{ number_format($harvest->quantity) }} kg) terserap.
                                        @endif
                                    </div>
                                    @if(isset($harvest->gis_distance) && $harvest->gis_distance !== null)
                                        <div class="mt-2 pt-1 border-top d-flex justify-content-between text-muted" style="font-size: 0.8rem;">
                                            <span><i class="fas fa-location-arrow text-danger me-1"></i> Jarak GIS: <strong>{{ $harvest->gis_distance }} km</strong></span>
                                            <span>
                                                Antar Petani:
                                                @if(!empty($harvest->farmer_can_deliver))
                                                    <span class="text-success fw-bold"><i class="fas fa-check-circle"></i> Tersedia (≤ {{ $harvest->farmer->delivery_radius }} km)</span>
                                                @else
                                                    <span class="text-muted"><i class="fas fa-truck text-primary"></i> Gunakan Mitra Pengangkut</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-secondary border">
                                        <i class="fas fa-route text-success me-1"></i> {{ $harvest->marketing_channel }}
                                    </span>
                                    <a href="{{ route('products.show', $harvest->id) }}" class="btn btn-sm btn-lime fw-semibold">
                                        <i class="fas fa-shopping-cart me-1"></i> Pesan Komoditas
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-3 bg-light rounded-3 text-center text-muted small my-3">
                        <i class="fas fa-search me-1"></i> Belum ada pasokan hasil panen yang memenuhi kriteria Grade {{ $demand->grade }} dengan harga maksimal Rp {{ number_format($demand->max_price_per_kg, 0, ',', '.') }}/kg.
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-4">
                <i class="fas fa-handshake fa-3x text-muted mb-3 opacity-50"></i>
                <h5 class="fw-bold text-dark">Belum ada data permintaan kebutuhan pembeli</h5>
                <p class="text-muted small">Pembeli dapat memasang permintaan pada menu Dashboard untuk menjalankan pencocokan otomatis.</p>
            </div>
        </div>
    @endforelse
</div>
@endsection

