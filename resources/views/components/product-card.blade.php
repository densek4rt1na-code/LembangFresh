@props(['harvest'])

<div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden product-card transition-all">
    <div class="position-relative bg-light text-center" style="height: 190px;">
        @if($harvest->photo_path && file_exists(public_path('storage/' . $harvest->photo_path)))
            <img src="{{ asset('storage/' . $harvest->photo_path) }}" 
                 alt="{{ $harvest->commodity }}" 
                 class="w-100 h-100 object-fit-cover">
        @else
            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light text-muted">
                <i class="fas fa-carrot fa-3x text-success mb-2 opacity-50"></i>
                <small>Foto Komoditas</small>
            </div>
        @endif

        <div class="position-absolute top-0 start-0 m-2">
            <x-status-badge :status="$harvest->grade" type="grade" />
        </div>

        <div class="position-absolute top-0 end-0 m-2">
            <x-status-badge :status="$harvest->status" />
        </div>
    </div>

    <div class="card-body d-flex flex-column">
        <h5 class="card-title fw-bold text-dark mb-1">
            <a href="{{ route('products.show', $harvest->id) }}" class="text-decoration-none text-dark hover-green">
                {{ $harvest->commodity }}
            </a>
        </h5>

        <div class="text-muted small mb-2">
            <i class="fas fa-user-circle me-1 text-success"></i> {{ $harvest->farmer->name ?? 'Petani Lembang' }}
            @if(!empty($harvest->farmer->is_verified))
                <i class="fas fa-check-circle text-primary" title="Petani Terverifikasi"></i>
            @endif
        </div>

        <div class="d-flex align-items-baseline mb-2">
            <span class="fs-4 fw-bold text-success">Rp {{ number_format($harvest->price_per_kg, 0, ',', '.') }}</span>
            <span class="text-muted small ms-1">/ kg</span>
        </div>

        <div class="small text-muted mb-2">
            <div><i class="fas fa-boxes me-1"></i> Stok: <strong>{{ number_format($harvest->quantity) }} kg</strong></div>
            <div><i class="fas fa-calendar-alt me-1"></i> Siap kirim: {{ \Carbon\Carbon::parse($harvest->ready_date)->format('d M Y') }}</div>
            @if(isset($harvest->distance_km) && $harvest->distance_km !== null)
                <div class="text-danger fw-semibold"><i class="fas fa-map-marker-alt me-1"></i> {{ $harvest->distance_km }} km dari lokasi Anda</div>
            @endif
        </div>

        <div class="mt-auto pt-2 border-top">
            <span class="badge bg-light text-secondary border d-block text-truncate mb-2 text-start" title="{{ $harvest->marketing_channel }}">
                <i class="fas fa-route text-success me-1"></i> {{ $harvest->marketing_channel }}
            </span>
            <a href="{{ route('products.show', $harvest->id) }}" class="btn btn-outline-success btn-sm w-100 fw-semibold">
                <i class="fas fa-eye me-1"></i> Detail & Pesan
            </a>
        </div>
    </div>
</div>

