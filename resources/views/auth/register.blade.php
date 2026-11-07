@extends('layouts.app')

@section('title', 'Daftar Akun Baru - LembangFresh')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <div class="text-center mb-4">
                <div class="d-inline-block bg-success-subtle text-forest p-3 rounded-circle mb-2">
                    <i class="fas fa-user-plus fa-2x"></i>
                </div>
                <h3 class="fw-bold text-dark">Daftar Akun LembangFresh</h3>
                <p class="text-muted small">Bergabunglah dalam ekosistem pemasaran & distribusi hortikultura Lembang</p>
            </div>

            <form method="POST" action="{{ route('register.post') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nama Lengkap / Nama Usaha:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-user"></i></span>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required autofocus placeholder="Contoh: Kelompok Tani Mandiri / Resto Lembang">
                    </div>
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">Alamat Email:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-envelope"></i></span>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required placeholder="email@domain.com">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold">Nomor WhatsApp / Telp:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-phone"></i></span>
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   name="phone" value="{{ old('phone') }}" required placeholder="08123456789">
                        </div>
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label fw-semibold">Pilih Peran Pengguna (Role):</label>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="radio" class="btn-check" name="role" id="rolePetani" value="petani" 
                                   {{ old('role', 'petani') === 'petani' ? 'checked' : '' }}>
                            <label class="btn btn-outline-success w-100 p-3 text-start h-100" for="rolePetani">
                                <div class="fw-bold"><i class="fas fa-seedling me-1"></i> Petani</div>
                                <div class="small text-muted" style="font-size: 0.75rem;">Menjual hasil panen sayuran & hortikultura</div>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <input type="radio" class="btn-check" name="role" id="rolePembeli" value="pembeli"
                                   {{ old('role') === 'pembeli' ? 'checked' : '' }}>
                            <label class="btn btn-outline-info w-100 p-3 text-start h-100" for="rolePembeli">
                                <div class="fw-bold"><i class="fas fa-shopping-basket me-1"></i> Pembeli / UMKM</div>
                                <div class="small text-muted" style="font-size: 0.75rem;">Membeli sayuran atau memasang kebutuhan</div>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <input type="radio" class="btn-check" name="role" id="roleMitra" value="mitra"
                                   {{ old('role') === 'mitra' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary w-100 p-3 text-start h-100" for="roleMitra">
                                <div class="fw-bold"><i class="fas fa-truck-moving me-1"></i> Mitra Pengangkut</div>
                                <div class="small text-muted" style="font-size: 0.75rem;">Penyedia armada logistik & pengiriman lokal</div>
                            </label>
                        </div>
                    </div>
                    @error('role')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label fw-semibold">Alamat Lokasi di Kecamatan Lembang:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-map-marker-alt"></i></span>
                        <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" 
                               name="address" value="{{ old('address') }}" required placeholder="Contoh: Jl. Maribaya No. 20 / Desa Cikidang">
                    </div>
                    @error('address')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-semibold">Kata Sandi:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-lock"></i></span>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required placeholder="Minimal 6 karakter">
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label fw-semibold">Ulangi Kata Sandi:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-lock"></i></span>
                            <input id="password_confirmation" type="password" class="form-control" 
                                   name="password_confirmation" required placeholder="Konfirmasi sandi">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-lime btn-lg w-100 py-2 rounded-3 shadow-sm fw-bold">
                    <i class="fas fa-user-check me-1"></i> Daftarkan Akun Saya
                </button>
            </form>

            <div class="mt-4 pt-3 border-top text-center small text-muted">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="text-forest fw-bold text-decoration-none">Masuk ke Sistem</a>
            </div>
        </div>
    </div>
</div>
@endsection
