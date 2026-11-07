@extends('layouts.app')

@section('title', 'Masuk ke Sistem - LembangFresh')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-forest rounded-circle p-3 mb-2" style="width: 56px; height: 56px;">
                    <i class="fas fa-shield-halved fs-3 text-success"></i>
                </div>
                <h4 class="fw-bold text-dark">Autentikasi Pengguna</h4>
                <p class="text-muted small">Sistem Informasi Pemasaran & Distribusi Hortikultura Lembang</p>
            </div>

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small">Alamat Email:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-envelope"></i></span>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus 
                               placeholder="nama@email.com">
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold small">Kata Sandi:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-lock"></i></span>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="current-password" placeholder="••••••••">
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label small text-muted" for="remember">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                <button type="submit" class="btn btn-forest w-100 py-2 rounded-2 shadow-sm fw-semibold">
                    <i class="fas fa-right-to-bracket me-1"></i> Masuk ke Sistem
                </button>
            </form>

            <div class="mt-4 pt-3 border-top text-center small text-muted">
                Belum memiliki akun terdaftar? 
                <a href="{{ route('register') }}" class="text-forest fw-bold text-decoration-none">Registrasi Akun Baru</a>
            </div>
        </div>
    </div>
</div>
@endsection
