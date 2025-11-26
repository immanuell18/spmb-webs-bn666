<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="{{ route('siswa.dashboard') }}" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <h2 class="m-0 text-primary"><i class="fa fa-graduation-cap me-3"></i>SPMB Siswa</h2>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav mx-auto p-4 p-lg-0">
            <a href="{{ route('siswa.dashboard') }}" class="nav-item nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('siswa.profile') }}" class="nav-item nav-link {{ request()->routeIs('siswa.profile') ? 'active' : '' }}">Profil</a>
            <a href="{{ route('siswa.pendaftaran') }}" class="nav-item nav-link {{ request()->routeIs('siswa.pendaftaran') ? 'active' : '' }}">Pendaftaran</a>
            <a href="{{ route('siswa.berkas') }}" class="nav-item nav-link {{ request()->routeIs('siswa.berkas') ? 'active' : '' }}">Upload Berkas</a>
            <a href="{{ route('siswa.bayar') }}" class="nav-item nav-link {{ request()->routeIs('siswa.bayar') ? 'active' : '' }}">Pembayaran</a>
            <a href="{{ route('siswa.status') }}" class="nav-item nav-link {{ request()->routeIs('siswa.status') ? 'active' : '' }}">Status</a>
            <a href="{{ route('siswa.cetak-kartu') }}" class="nav-item nav-link {{ request()->routeIs('siswa.cetak-kartu') ? 'active' : '' }}">Cetak Kartu</a>
        </div>
        <div class="dropdown">
            <a href="#" class="btn btn-primary py-4 px-lg-5 dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fa fa-user me-2"></i>{{ Auth::user()->name }}
            </a>
            <div class="dropdown-menu">
                <a href="{{ route('siswa.profile') }}" class="dropdown-item">Profil</a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>
<!-- Navbar End -->