@extends('layouts.app')

@section('title', 'Detail Komoditas: ' . $harvest->commodity . ' - LembangFresh')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-forest text-decoration-none">Pasar Komoditas</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $harvest->commodity }}</li>
    </ol>
</nav>

<div class="row g-4">
    <!-- Gambar & Info Petani -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3 bg-white">
            <div class="position-relative text-center bg-light" style="min-height: 280px;">
                @if($harvest->photo_path && file_exists(public_path('storage/' . $harvest->photo_path)))
                    <img src="{{ asset('storage/' . $harvest->photo_path) }}" 
                         alt="{{ $harvest->commodity }}" 
                         class="img-fluid w-100 object-fit-cover" style="max-height: 380px;">
                @else
                    <div class="p-5 text-muted">
                        <i class="fas fa-carrot fa-4x text-success mb-2 opacity-50"></i>
                        <p>Foto Komoditas Pertanian</p>
                    </div>
                @endif
                <div class="position-absolute top-0 start-0 m-3">
                    <x-status-badge :status="$harvest->grade" type="grade" class="fs-6 px-3 py-1" />
                </div>
                <div class="position-absolute top-0 end-0 m-3">
                    <x-status-badge :status="$harvest->status" class="fs-6 px-3 py-1" />
                </div>
            </div>

            <div class="card-body p-4">
                <h6 class="fw-bold text-muted mb-2 text-uppercase small">Profil Petani Pemilik</h6>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success-subtle text-success p-3 rounded-circle me-3">
                        <i class="fas fa-user-circle fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold m-0 text-dark">
                            {{ $harvest->farmer->name ?? 'Petani Lembang' }}
                            @if(!empty($harvest->farmer->is_verified))
                                <i class="fas fa-check-circle text-primary" title="Petani Terverifikasi"></i>
                            @endif
                        </h5>
                        <div class="text-muted small"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $harvest->farmer->address }}</div>
                        <div class="text-muted small"><i class="fas fa-phone text-success me-1"></i> {{ $harvest->farmer->phone ?? '-' }}</div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3">
                    <div class="small fw-semibold text-forest mb-1"><i class="fas fa-truck me-1"></i> Layanan Antar Petani:</div>
                    @if($harvest->farmer->delivery_available)
                        <div class="small text-muted">
                            <span class="badge bg-success">AKTIF</span> Radius: <strong>{{ $harvest->farmer->delivery_radius }} km</strong> | Tarif Tetap: <strong>Rp {{ number_format($harvest->farmer->delivery_fee, 0, ',', '.') }}</strong>
                        </div>
                    @else
                        <div class="small text-muted">
                            <span class="badge bg-secondary">NONAKTIF</span> Petani tidak menyediakan pengantaran langsung (Disarankan menggunakan Mitra Pengangkut).
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Produk & Pemesanan -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h2 class="fw-bold text-dark mb-1">{{ $harvest->commodity }}</h2>
                    <div class="text-muted">Varietas: <strong>{{ $harvest->variety ?? 'Hortikultura Unggul' }}</strong></div>
                </div>
                <div class="text-end">
                    <div class="fs-2 fw-bold text-success">Rp {{ number_format($harvest->price_per_kg, 0, ',', '.') }}</div>
                    <small class="text-muted">harga per kilogram</small>
                </div>
            </div>

            <!-- Rekomendasi Jalur Pemasaran Sesuai SDD Bab 4 -->
            <div class="alert alert-light border border-success-subtle rounded-3 p-3 my-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-route text-success fs-3 me-3"></i>
                    <div>
                        <div class="fw-bold text-dark">Rekomendasi Jalur Pemasaran Berdasarkan Kualitas:</div>
                        <div class="text-forest fw-semibold">{{ $harvest->marketing_channel }}</div>
                        <div class="small text-muted">
                            @if($harvest->grade === 'A')
                                Bentuk dan kualitas prima, ditujukan untuk supermarket, hotel, restoran, dan pasar normal.
                            @elseif($harvest->grade === 'B')
                                Bentuk/ukuran kurang seragam namun layak konsumsi, ditujukan untuk UMKM kuliner atau rumah makan.
                            @else
                                Kualitas visual lebih rendah, diarahkan ke industri mitra pengolahan (saus, sambal, pasta, keripik).
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 my-2">
                <div class="col-sm-4">
                    <div class="p-3 bg-light rounded-3 text-center">
                        <div class="small text-muted">Stok Tersedia</div>
                        <div class="fs-4 fw-bold text-dark">{{ number_format($harvest->quantity) }} kg</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-3 bg-light rounded-3 text-center">
                        <div class="small text-muted">Tanggal Panen</div>
                        <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($harvest->harvest_date)->format('d M Y') }}</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-3 bg-light rounded-3 text-center">
                        <div class="small text-muted">Siap Didistribusikan</div>
                        <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($harvest->ready_date)->format('d M Y') }}</div>
                    </div>
                </div>
            </div>

            @if($harvest->description)
                <div class="my-3">
                    <h6 class="fw-bold text-dark">Deskripsi & Catatan Panen:</h6>
                    <p class="text-muted small mb-0">{{ $harvest->description }}</p>
                </div>
            @endif

            <hr class="my-4">

            <!-- Formulir Pemesanan Sesuai Konsep GIS & Logistik LembangFresh -->
            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-shopping-cart text-forest me-2"></i> Form Pesan Komoditas</h5>

            @auth
                @if(auth()->user()->isPetani() && auth()->id() === $harvest->farmer_id)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> Ini adalah komoditas hasil panen Anda sendiri. Anda dapat mengeditnya di menu <a href="{{ route('harvests.edit', $harvest->id) }}" class="fw-bold">Kelola Hasil Panen</a>.
                    </div>
                @else
                    <form action="{{ route('order.create', $harvest->id) }}" method="POST">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah yang Ingin Dipesan (kg):</label>
                                <div class="input-group">
                                    <input type="number" name="quantity" id="orderQty" class="form-control" 
                                           min="1" max="{{ $harvest->quantity }}" value="10" required>
                                    <span class="input-group-text">kg</span>
                                </div>
                                <div class="form-text small">Maksimal pemesanan: {{ $harvest->quantity }} kg</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jarak GIS ke Lokasi Anda:</label>
                                <div class="form-control bg-light text-muted">
                                    @if($distance !== null)
                                        <i class="fas fa-location-arrow text-danger me-1"></i> <strong>{{ $distance }} km</strong> (Dihitung dari koordinat profil Anda)
                                    @else
                                        <i class="fas fa-exclamation-circle text-warning me-1"></i> Koordinat belum disetel di profil (Diasumsikan 5 km)
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Jalur Distribusi & Pengiriman:</label>
                            
                            @foreach($deliveryOptions as $methodKey => $opt)
                                <div class="card p-3 mb-2 border {{ $opt['available'] ? 'border-light-subtle' : 'opacity-50 bg-light' }}">
                                    <div class="form-check d-flex align-items-center justify-content-between">
                                        <div>
                                            <input class="form-check-input delivery-radio" type="radio" name="delivery_method" 
                                                   id="method_{{ $methodKey }}" value="{{ $methodKey }}" 
                                                   data-fee="{{ $opt['fee'] }}"
                                                   {{ $loop->first ? 'checked' : '' }}
                                                   {{ !$opt['available'] ? 'disabled' : '' }} required>
                                            <label class="form-check-label fw-bold text-dark ms-2" for="method_{{ $methodKey }}">
                                                {{ $opt['name'] }}
                                                @if($opt['available'])
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle ms-2"><i class="fas fa-check-circle me-1"></i> Tersedia</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border ms-2"><i class="fas fa-ban me-1"></i> Tidak Tersedia</span>
                                                @endif
                                            </label>
                                            <div class="small text-muted ms-4">{{ $opt['note'] }}</div>
                                        </div>
                                        <div class="text-end">
                                            @if($opt['fee'] == 0)
                                                <span class="badge bg-success fs-6">GRATIS</span>
                                            @else
                                                <span class="fw-bold text-dark">Rp {{ number_format($opt['fee'], 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Ringkasan Total Pembayaran -->
                        <div class="p-3 bg-forest text-white rounded-3 mb-3">
                            <div class="d-flex justify-content-between mb-1 small">
                                <span>Subtotal Produk (<span id="summaryQty">10</span> kg x Rp {{ number_format($harvest->price_per_kg, 0, ',', '.') }}):</span>
                                <span id="summarySubtotal">Rp {{ number_format(10 * $harvest->price_per_kg, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small border-bottom border-white border-opacity-25 pb-2">
                                <span>Ongkos Kirim Terpilih:</span>
                                <span id="summaryShipping">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center fs-5 fw-bold">
                                <span>Estimasi Total Pembayaran:</span>
                                <span class="text-lime" id="summaryTotal">Rp {{ number_format(10 * $harvest->price_per_kg, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-lime btn-lg w-100 shadow-sm fw-bold">
                            <i class="fas fa-check-circle me-1"></i> Konfirmasi & Buat Pesanan
                        </button>
                    </form>
                @endif
            @else
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="fas fa-lock fs-4 me-3"></i>
                    <div>
                        <strong>Ingin memesan komoditas hasil panen ini?</strong><br>
                        Silakan <a href="{{ route('login') }}" class="fw-bold text-dark">Masuk</a> atau <a href="{{ route('register') }}" class="fw-bold text-dark">Daftar Akun Pembeli</a> terlebih dahulu untuk melakukan transaksi.
                    </div>
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const unitPrice = {{ $harvest->price_per_kg }};
        const qtyInput = document.getElementById('orderQty');
        const radios = document.querySelectorAll('.delivery-radio');
        const summaryQty = document.getElementById('summaryQty');
        const summarySubtotal = document.getElementById('summarySubtotal');
        const summaryShipping = document.getElementById('summaryShipping');
        const summaryTotal = document.getElementById('summaryTotal');

        function formatRp(num) {
            return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function calculate() {
            if (!qtyInput) return;
            const qty = parseInt(qtyInput.value) || 0;
            let shippingFee = 0;

            radios.forEach(r => {
                if (r.checked) {
                    shippingFee = parseInt(r.getAttribute('data-fee')) || 0;
                }
            });

            const subtotal = qty * unitPrice;
            const total = subtotal + shippingFee;

            if (summaryQty) summaryQty.textContent = qty;
            if (summarySubtotal) summarySubtotal.textContent = formatRp(subtotal);
            if (summaryShipping) summaryShipping.textContent = formatRp(shippingFee);
            if (summaryTotal) summaryTotal.textContent = formatRp(total);
        }

        if (qtyInput) {
            qtyInput.addEventListener('input', calculate);
            radios.forEach(r => r.addEventListener('change', calculate));
            calculate();
        }
    });
</script>
@endpush

