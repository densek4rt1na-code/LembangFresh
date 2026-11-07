<nav class="navbar navbar-expand-lg navbar-dark bg-forest sticky-top shadow-sm py-2">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold fs-4 text-white" href="{{ route('home') }}">
            <span class="brand-icon bg-white text-forest rounded-2 d-inline-flex align-items-center justify-content-center me-2 p-1" style="width: 32px; height: 32px;">
                <i class="fas fa-leaf text-success fs-5"></i>
            </span>
            <span>Lembang<span class="text-lime">Fresh</span></span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active fw-bold' : '' }}" href="{{ route('home') }}">
                        <i class="fas fa-store me-1"></i> Pasar Komoditas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('gis.index') ? 'active fw-bold' : '' }}" href="{{ route('gis.index') }}">
                        <i class="fas fa-map-location-dot me-1"></i> Peta Spasial GIS
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('matching.index') ? 'active fw-bold' : '' }}" href="{{ route('matching.index') }}">
                        <i class="fas fa-arrows-split-up-and-left me-1"></i> Supply-Demand Matching
                    </a>
                </li>

                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-chart-pie me-1"></i> Dashboard
                        </a>
                    </li>

                    @if(auth()->user()->isAdmin() || auth()->user()->isPetani())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('harvests.*') ? 'active fw-bold' : '' }}" href="{{ route('harvests.index') }}">
                                <i class="fas fa-wheat-awn me-1"></i> Hasil Panen
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->isAdmin() || auth()->user()->isMitra())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('logistics.*') ? 'active fw-bold' : '' }}" href="{{ route('logistics.index') }}">
                                <i class="fas fa-truck-fast me-1"></i> Pengiriman Logistik
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

            <ul class="navbar-nav ms-auto align-items-lg-center">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar bg-white text-forest fw-bold rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <i class="fas fa-user-check small text-success"></i>
                            </div>
                            <span class="d-none d-lg-inline">{{ auth()->user()->name }}</span>
                            <span class="badge bg-light text-dark ms-2 text-uppercase" style="font-size: 0.7rem;">{{ auth()->user()->role }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenu">
                            <li class="dropdown-header">
                                <strong>{{ auth()->user()->name }}</strong>
                                <div class="small text-muted">{{ auth()->user()->email }}</div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fas fa-chart-pie me-2 text-success"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="fas fa-user-gear me-2 text-primary"></i> Pengaturan Akun & Lokasi
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-arrow-right-from-bracket me-2"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link text-white me-2" href="{{ route('login') }}">
                            <i class="fas fa-lock me-1"></i> Masuk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-lime text-forest fw-bold px-3 py-1 shadow-sm rounded-2" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i> Registrasi
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

