<!-- Sidebar Start -->
<aside class="left-sidebar">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ route('kepsek.dashboard') }}" class="text-nowrap logo-img">
                <h4 class="text-primary mb-0">👨‍💼 Kepala Sekolah</h4>
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
                    <a class="sidebar-link {{ request()->is('kepsek/dashboard') ? 'active' : '' }}" href="{{ route('kepsek.dashboard') }}" aria-expanded="false">
                        <span><i class="ti ti-layout-dashboard"></i></span>
                        <span class="hide-menu">Dashboard Eksekutif</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('kepsek/dashboard-executive') ? 'active' : '' }}" href="{{ route('dashboard.executive') }}" aria-expanded="false">
                        <span><i class="ti ti-chart-pie"></i></span>
                        <span class="hide-menu">Executive Dashboard</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">LAPORAN</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('kepsek/laporan') ? 'active' : '' }}" href="{{ route('kepsek.laporan') }}" aria-expanded="false">
                        <span><i class="ti ti-chart-bar"></i></span>
                        <span class="hide-menu">Laporan Eksekutif</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('kepsek/peta-sebaran') ? 'active' : '' }}" href="{{ route('kepsek.peta-sebaran') }}" aria-expanded="false">
                        <span><i class="ti ti-map"></i></span>
                        <span class="hide-menu">Peta Sebaran Domisili</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('reports*') ? 'active' : '' }}" href="{{ route('reports.index') }}" aria-expanded="false">
                        <span><i class="ti ti-file-export"></i></span>
                        <span class="hide-menu">Export Laporan</span>
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