<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center px-3 px-lg-5">
        <img src="{{ asset('assets/images/logo-sekolah.png') }}" alt="Logo SMK Bakti Nusantara 666" 
             class="d-none d-sm-block"
             style="height: 35px; width: auto; margin-right: 8px; object-fit: contain;"
             onerror="this.style.display='none';">
        <h2 class="m-0 text-primary d-none d-md-block">SMK BAKTI NUSANTARA 666</h2>
        <h5 class="m-0 text-primary d-block d-md-none">SMK BN 666</h5>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav mx-auto p-4 p-lg-0">
            <a href="{{ route('beranda') }}" class="nav-item nav-link {{ request()->is('/') ? 'active' : '' }}">Beranda</a>
            <a href="{{ route('tentang') }}" class="nav-item nav-link {{ request()->is('tentang') ? 'active' : '' }}">Tentang</a>
            <a href="{{ route('jurusan') }}" class="nav-item nav-link {{ request()->is('jurusan') ? 'active' : '' }}">Jurusan</a>
            <a href="{{ route('prestasi-fasilitas') }}" class="nav-item nav-link {{ request()->is('prestasi-fasilitas') ? 'active' : '' }}">Prestasi & Fasilitas</a>
            <a href="{{ route('pendaftaran') }}" class="nav-item nav-link {{ request()->is('pendaftaran') ? 'active' : '' }}">PPDB</a>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ route('login') }}" class="btn btn-primary d-none d-lg-block"><i class="fa fa-user me-2"></i>Login/Daftar</a>
        </div>
        <div class="d-lg-none p-3">
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm w-100"><i class="fa fa-user me-2"></i>Login/Daftar</a>
        </div>
    </div>
</nav>
<!-- Navbar End -->