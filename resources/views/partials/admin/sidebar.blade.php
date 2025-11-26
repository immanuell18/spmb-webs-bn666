<!-- Sidebar Start -->
<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ url('/admin') }}" class="text-nowrap logo-img">
                <h4 class="text-primary mb-0">🚀 Spike Admin</h4>
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">DASHBOARD</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('admin') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('admin/dashboard-advanced') ? 'active' : '' }}" href="{{ route('dashboard.admin-advanced') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-chart-line"></i>
                        </span>
                        <span class="hide-menu">Advanced Dashboard</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">MANAJEMEN SPMB</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('admin/master-data') ? 'active' : '' }}" href="{{ route('admin.master-data') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-database"></i>
                        </span>
                        <span class="hide-menu">Master Data</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('admin/monitoring-berkas') ? 'active' : '' }}" href="{{ route('admin.monitoring-berkas') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-file-check"></i>
                        </span>
                        <span class="hide-menu">Monitoring Berkas</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('admin/peta-sebaran') ? 'active' : '' }}" href="{{ route('admin.peta-sebaran') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-map"></i>
                        </span>
                        <span class="hide-menu">Peta Sebaran</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('admin/pengumuman') ? 'active' : '' }}" href="{{ route('admin.pengumuman') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-trophy"></i>
                        </span>
                        <span class="hide-menu">Pengumuman</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('reports*') ? 'active' : '' }}" href="{{ route('reports.index') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-file-export"></i>
                        </span>
                        <span class="hide-menu">Export Laporan</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('admin/payment*') ? 'active' : '' }}" href="{{ route('admin.payment.dashboard') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-credit-card"></i>
                        </span>
                        <span class="hide-menu">Payment Gateway</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('admin/audit-logs*') ? 'active' : '' }}" href="{{ route('admin.audit-logs.index') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-history"></i>
                        </span>
                        <span class="hide-menu">Audit Log</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="hide-menu">Kelola Akun</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->is('admin/system-settings') ? 'active' : '' }}" href="{{ route('admin.system-settings') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-settings"></i>
                        </span>
                        <span class="hide-menu">Pengaturan Sistem</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">AUTH</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();" aria-expanded="false">
                        <span>
                            <i class="ti ti-logout"></i>
                        </span>
                        <span class="hide-menu">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
<!-- Sidebar End -->