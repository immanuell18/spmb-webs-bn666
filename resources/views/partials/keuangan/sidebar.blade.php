<!-- Sidebar Start -->
<aside class="left-sidebar">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ route('keuangan.dashboard') }}" class="text-nowrap logo-img">
                <h4 class="text-primary mb-0">💰 Keuangan</h4>
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">DASHBOARD</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('keuangan/dashboard') ? 'active' : '' }}" href="{{ route('keuangan.dashboard') }}" aria-expanded="false">
                        <span><i class="ti ti-layout-dashboard"></i></span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">PEMBAYARAN</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('keuangan/verifikasi-pembayaran') ? 'active' : '' }}" href="{{ route('keuangan.verifikasi') }}" aria-expanded="false">
                        <span><i class="ti ti-credit-card"></i></span>
                        <span class="hide-menu">Verifikasi Pembayaran</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('keuangan/rekap') ? 'active' : '' }}" href="{{ route('keuangan.rekap') }}" aria-expanded="false">
                        <span><i class="ti ti-report"></i></span>
                        <span class="hide-menu">Rekap Keuangan</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">AUTH</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();" aria-expanded="false">
                        <span><i class="ti ti-logout"></i></span>
                        <span class="hide-menu">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
<!-- Sidebar End -->