@extends('layouts.app')

@section('title', 'Dashboard ' . ucfirst(auth()->user()->role) . ' - LembangFresh')

@section('content')

<div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-forest text-lime fw-bold rounded-circle d-flex align-items-center justify-content-center me-3"
                 style="width: 58px; height: 58px; font-size: 1.5rem;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h3 class="fw-bold text-dark m-0">{{ auth()->user()->name }}</h3>
                    <x-status-badge :status="auth()->user()->role" type="role" />
                </div>
                <div class="text-muted small mt-1">
                    <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ auth()->user()->address ?? 'Kecamatan Lembang, Bandung Barat' }}
                    <span class="mx-2">&bull;</span>
                    <i class="fas fa-envelope text-muted me-1"></i> {{ auth()->user()->email }}
                </div>
            </div>
        </div>

        <div>
            @if(auth()->user()->is_verified)
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                    <i class="fas fa-check-circle me-1"></i> Akun Terverifikasi
                </span>
            @else
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill">
                    <i class="fas fa-clock me-1"></i> Menunggu Verifikasi Admin
                </span>
            @endif
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin())
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center stat-card">
                <div class="text-muted small fw-semibold">Petani</div>
                <div class="fs-3 fw-bold text-success">{{ $adminStats['total_farmers'] }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center stat-card">
                <div class="text-muted small fw-semibold">Pembeli / UMKM</div>
                <div class="fs-3 fw-bold text-info">{{ $adminStats['total_buyers'] }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center stat-card">
                <div class="text-muted small fw-semibold">Mitra Logistik</div>
                <div class="fs-3 fw-bold text-primary">{{ $adminStats['total_mitra'] }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center stat-card">
                <div class="text-muted small fw-semibold">Hasil Panen</div>
                <div class="fs-3 fw-bold text-forest">{{ $adminStats['total_harvests'] }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center stat-card">
                <div class="text-muted small fw-semibold">Pesanan</div>
                <div class="fs-3 fw-bold text-dark">{{ $adminStats['total_orders'] }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center stat-card">
                <div class="text-muted small fw-semibold">Total Pengiriman</div>
                <div class="fs-3 fw-bold text-warning">{{ $adminStats['total_shipments'] }}</div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark m-0"><i class="fas fa-receipt text-forest me-2"></i> Transaksi Pesanan LembangFresh Terkini</h5>
            <span class="badge bg-light text-muted">{{ $allOrders->count() }} transaksi</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pembeli</th>
                        <th>Komoditas & Petani</th>
                        <th>Jumlah</th>
                        <th>Metode Kirim</th>
                        <th>Total Biaya</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allOrders as $ord)
                        <tr>
                            <td class="fw-bold">{{ $ord->order_number }}</td>
                            <td>{{ $ord->buyer->name ?? 'Pembeli' }}</td>
                            <td>
                                <div>{{ $ord->harvest->commodity ?? '-' }}</div>
                                <small class="text-muted">Petani: {{ $ord->harvest->farmer->name ?? '-' }}</small>
                            </td>
                            <td>{{ $ord->quantity }} kg</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ ucwords(str_replace('_', ' ', $ord->delivery_method)) }}
                                </span>
                            </td>
                            <td class="fw-bold text-success">Rp {{ number_format($ord->total_price, 0, ',', '.') }}</td>
                            <td><x-status-badge :status="$ord->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted">Belum ada transaksi pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark m-0"><i class="fas fa-user-check text-success me-2"></i> Kelola & Verifikasi Pengguna LembangFresh</h5>
            <span class="badge bg-light text-muted">{{ $allUsers->count() }} pengguna terdaftar</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Email & Telepon</th>
                        <th>Peran (Role)</th>
                        <th>Alamat di Lembang</th>
                        <th>Status</th>
                        <th class="text-end">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allUsers as $u)
                        <tr>
                            <td class="fw-bold text-dark">{{ $u->name }}</td>
                            <td>
                                <div>{{ $u->email }}</div>
                                <div class="small text-muted">{{ $u->phone ?? '-' }}</div>
                            </td>
                            <td>
                                <x-status-badge :status="$u->role" type="role" />
                            </td>
                            <td class="small">{{ $u->address ?? '-' }}</td>
                            <td>
                                <x-status-badge :status="$u->is_verified" />
                            </td>
                            <td class="text-end">
                                <form action="{{ route('admin.user.verify', $u->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $u->is_verified ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                        {{ $u->is_verified ? 'Cabut Verifikasi' : 'Verifikasi Akun' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-map-marked-alt text-forest me-2"></i> GIS Dashboard: Sebaran Aktor di Kecamatan Lembang</h5>
        <div id="adminGisMap" style="height: 380px; width: 100%; border-radius: 12px;"></div>
    </div>
@endif

@if(auth()->user()->isPetani())
    <div class="row g-4 mb-4">

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-truck text-forest me-2"></i> Pengaturan Antar Petani</h5>
                <p class="small text-muted mb-3">Tentukan apakah Anda bersedia mengantar hasil panen langsung ke pembeli beserta radius dan tarif tetap.</p>

                <form action="{{ route('farmer.delivery.update') }}" method="POST">
                    @csrf
                    <div class="form-check form-switch mb-3 p-3 bg-light rounded-3">
                        <input class="form-check-input ms-0 me-3" type="checkbox" role="switch"
                               name="delivery_available" value="1" id="deliverSwitch"
                               {{ auth()->user()->delivery_available ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-dark" for="deliverSwitch">
                            Aktifkan Layanan Antar Sendiri
                        </label>
                        <div class="small text-muted mt-1">Jika nonaktif, pembeli diarahkan menggunakan Mitra Pengangkut.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Radius Maks (km):</label>
                            <input type="number" step="0.5" name="delivery_radius" class="form-control"
                                   value="{{ auth()->user()->delivery_radius ?? 5.0 }}" required min="0.5" max="50">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tarif Antar Tetap (Rp):</label>
                            <input type="number" name="delivery_fee" class="form-control"
                                   value="{{ auth()->user()->delivery_fee ?? 25000 }}" required min="0">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-forest w-100 fw-semibold">
                        <i class="fas fa-save me-1"></i> Simpan Pengaturan Antar
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark m-0"><i class="fas fa-carrot text-success me-2"></i> Hasil Panen Saya</h5>
                    <a href="{{ route('harvests.create') }}" class="btn btn-sm btn-forest">
                        <i class="fas fa-plus me-1"></i> Tambah Panen
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Komoditas</th>
                                <th>Grade</th>
                                <th>Stok</th>
                                <th>Harga / kg</th>
                                <th>Jalur Pemasaran</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myHarvests as $h)
                                <tr>
                                    <td class="fw-bold">{{ $h->commodity }}</td>
                                    <td><x-status-badge :status="$h->grade" type="grade" /></td>
                                    <td>{{ number_format($h->quantity) }} kg</td>
                                    <td class="text-success fw-bold">Rp {{ number_format($h->price_per_kg, 0, ',', '.') }}</td>
                                    <td class="text-muted">{{ $h->marketing_channel }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('harvests.edit', $h->id) }}" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada hasil panen yang didaftarkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-shopping-basket text-success me-2"></i> Pesanan Masuk dari Pembeli</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pembeli</th>
                        <th>Komoditas</th>
                        <th>Jumlah</th>
                        <th>Pengiriman</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end">Aksi Konfirmasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receivedOrders as $ro)
                        <tr>
                            <td class="fw-bold">{{ $ro->order_number }}</td>
                            <td>
                                <div>{{ $ro->buyer->name ?? 'Pembeli' }}</div>
                                <small class="text-muted">{{ $ro->buyer->phone ?? '-' }}</small>
                            </td>
                            <td>{{ $ro->harvest->commodity ?? '-' }}</td>
                            <td><strong>{{ $ro->quantity }} kg</strong></td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ ucwords(str_replace('_', ' ', $ro->delivery_method)) }}
                                </span>
                            </td>
                            <td class="fw-bold text-success">Rp {{ number_format($ro->total_price, 0, ',', '.') }}</td>
                            <td><x-status-badge :status="$ro->status" /></td>
                            <td class="text-end">
                                @if($ro->status === 'dibuat')
                                    <form action="{{ route('order.status.update', $ro->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="diproses">
                                        <button type="submit" class="btn btn-sm btn-success">Terima & Siapkan</button>
                                    </form>
                                    <form action="{{ route('order.status.update', $ro->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="ditolak">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tolak pesanan ini?')">Tolak</button>
                                    </form>
                                @elseif($ro->status === 'diproses' && $ro->delivery_method === 'antar_petani')
                                    <form action="{{ route('order.status.update', $ro->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="dikirim">
                                        <button type="submit" class="btn btn-sm btn-primary">Mulai Antar</button>
                                    </form>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada pesanan masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-handshake text-forest me-2"></i> Kebutuhan Pembeli yang Cocok dengan Komoditas Anda</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Pembeli / UMKM</th>
                        <th>Komoditas Dibutuhkan</th>
                        <th>Jumlah Butuh</th>
                        <th>Grade</th>
                        <th>Harga Maks / kg</th>
                        <th>Alamat Pembeli</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matchingDemands as $d)
                        <tr>
                            <td class="fw-bold text-dark">{{ $d->buyer->name ?? 'Pembeli' }}</td>
                            <td>{{ $d->commodity }}</td>
                            <td><strong>{{ number_format($d->quantity) }} kg</strong></td>
                            <td><x-status-badge :status="$d->grade" type="grade" /></td>
                            <td class="text-success fw-bold">Rp {{ number_format($d->max_price_per_kg, 0, ',', '.') }}</td>
                            <td class="small text-muted">{{ $d->buyer->address ?? 'Lembang' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada permintaan yang cocok dengan komoditas panen Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

@if(auth()->user()->isPembeli())
    <div class="row g-4 mb-4">

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-bullhorn text-forest me-2"></i> Pasang Kebutuhan Sayuran</h5>
                <p class="small text-muted mb-3">Petani di Lembang dapat melihat kebutuhan Anda untuk pencocokan pasokan langsung.</p>

                <form action="{{ route('demands.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Komoditas yang Dibutuhkan:</label>
                        <input type="text" name="commodity" class="form-control" required placeholder="Contoh: Wortel, Kentang, Tomat">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Jumlah Butuh (kg):</label>
                            <input type="number" name="quantity" class="form-control" required min="1" placeholder="300">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Grade Mutu:</label>
                            <select name="grade" class="form-select" required>
                                <option value="A">Grade A (Supermarket)</option>
                                <option value="B" selected>Grade B (UMKM Kuliner)</option>
                                <option value="C">Grade C (Olahan)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Harga Maksimal Pembelian per kg (Rp):</label>
                        <input type="number" name="max_price_per_kg" class="form-control" required min="500" placeholder="5000">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan Tambahan:</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Spesifikasi ukuran, jadwal pengiriman rutin..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-forest w-100 fw-semibold">
                        <i class="fas fa-paper-plane me-1"></i> Pasang Kebutuhan Komoditas
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-list-alt text-primary me-2"></i> Daftar Kebutuhan Anda yang Sedang Terbuka</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Komoditas</th>
                                <th>Kebutuhan</th>
                                <th>Grade</th>
                                <th>Harga Maks</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myDemands as $d)
                                <tr>
                                    <td class="fw-bold">{{ $d->commodity }}</td>
                                    <td>{{ number_format($d->quantity) }} kg</td>
                                    <td><x-status-badge :status="$d->grade" type="grade" /></td>
                                    <td class="text-success fw-bold">Rp {{ number_format($d->max_price_per_kg, 0, ',', '.') }}</td>
                                    <td><x-status-badge :status="$d->status" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada kebutuhan komoditas yang dipasang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-shopping-cart text-forest me-2"></i> Riwayat Pesanan Sayuran Anda</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Komoditas & Petani</th>
                        <th>Jumlah</th>
                        <th>Metode Kirim</th>
                        <th>Total Biaya</th>
                        <th>Status</th>
                        <th class="text-end">Aksi Konfirmasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myOrders as $mo)
                        <tr>
                            <td class="fw-bold">{{ $mo->order_number }}</td>
                            <td>
                                <div>{{ $mo->harvest->commodity ?? '-' }}</div>
                                <small class="text-muted">Petani: {{ $mo->harvest->farmer->name ?? '-' }} ({{ $mo->harvest->farmer->phone ?? '-' }})</small>
                            </td>
                            <td><strong>{{ $mo->quantity }} kg</strong></td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ ucwords(str_replace('_', ' ', $mo->delivery_method)) }}
                                </span>
                            </td>
                            <td class="fw-bold text-success">Rp {{ number_format($mo->total_price, 0, ',', '.') }}</td>
                            <td><x-status-badge :status="$mo->status" /></td>
                            <td class="text-end">
                                @if($mo->status === 'dikirim')
                                    <form action="{{ route('order.status.update', $mo->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="selesai">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check me-1"></i> Konfirmasi Barang Diterima
                                        </button>
                                    </form>
                                @elseif($mo->status === 'selesai')
                                    <span class="badge bg-success-subtle text-success">Selesai & Diterima</span>
                                @else
                                    <span class="text-muted small">Menunggu proses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Anda belum memiliki riwayat pesanan. Jelajahi komoditas segar di <a href="{{ route('home') }}">Pasar Komoditas</a>.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

@if(auth()->user()->isMitra())
    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-toggle-on text-primary me-2"></i> Ketersediaan Armada Pickup</h5>
                <p class="small text-muted mb-3">Atur apakah armada Anda siap menerima penugasan pengantaran hasil hortikultura hari ini.</p>

                <div class="p-3 bg-light rounded-3 mb-3 text-center">
                    <div class="text-muted small">Status Armada Saat Ini:</div>
                    <div class="fs-4 fw-bold {{ auth()->user()->is_active ? 'text-success' : 'text-danger' }}">
                        {{ auth()->user()->is_active ? 'TERSEDIA & SIAP OPERASIONAL' : 'TIDAK TERSEDIA' }}
                    </div>
                    <div class="small text-muted mt-1">Armada: {{ auth()->user()->vehicle_desc ?? 'Pickup Mitsubishi L300' }}</div>
                </div>

                <form action="{{ route('mitra.availability.toggle') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn {{ auth()->user()->is_active ? 'btn-danger' : 'btn-success' }} w-100 fw-semibold">
                        <i class="fas fa-power-off me-1"></i> {{ auth()->user()->is_active ? 'Set Menjadi Tidak Tersedia' : 'Set Menjadi Tersedia' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark m-0"><i class="fas fa-truck-loading text-forest me-2"></i> Penugasan Distribusi Logistik</h5>
                    <a href="{{ route('logistics.index') }}" class="btn btn-sm btn-outline-forest">
                        Kelola Pengiriman Logistik <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Rute</th>
                                <th>Komoditas</th>
                                <th>Ongkos</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myShipments as $s)
                                <tr>
                                    <td class="fw-bold">{{ $s->tracking_code }}</td>
                                    <td>{{ $s->sender_name }} &rarr; {{ $s->receiver_name }}</td>
                                    <td>{{ $s->commodity_info }}</td>
                                    <td class="text-success fw-bold">Rp {{ number_format($s->shipping_cost, 0, ',', '.') }}</td>
                                    <td><x-status-badge :status="$s->status" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada penugasan distribusi aktif.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
@if(auth()->user()->isAdmin())
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const adminMap = L.map('adminGisMap').setView([-6.8120, 107.6180], 12);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap kontributor'
        }).addTo(adminMap);

        const roleColors = {
            'petani': '#245831',
            'pembeli': '#0284c7',
            'mitra': '#f59e0b'
        };

        @foreach($allUsers as $u)
            @if($u->lat && $u->lng)
                L.circleMarker([{{ $u->lat }}, {{ $u->lng }}], {
                    color: roleColors['{{ $u->role }}'] || '#64748b',
                    fillColor: roleColors['{{ $u->role }}'] || '#64748b',
                    fillOpacity: 0.8,
                    radius: 8
                }).addTo(adminMap).bindPopup(`
                    <div style="font-size: 13px;">
                        <strong>{{ $u->name }}</strong><br>
                        <span class="badge bg-secondary">{{ ucfirst($u->role) }}</span><br>
                        <small class="text-muted">{{ $u->address }}</small>
                    </div>
                `);
            @endif
        @endforeach
    });
</script>
@endif
@endpush

