<!-- Sidebar Start -->
<aside class="left-sidebar" style="background: #fff; border-right: 1px solid #e2e8f0;">
    <div style="display: flex; flex-direction: column; height: 100%;">
        <!-- Logo -->
        <div class="brand-logo d-flex align-items-center justify-content-between" style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0;">
            <a href="{{ url('/admin') }}" class="text-nowrap logo-img d-flex align-items-center gap-3" style="text-decoration: none;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366f1, #4f46e5); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);">
                    <i class="ti ti-school" style="font-size: 20px; color: #fff;"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 18px; font-weight: 700; color: #0f172a; letter-spacing: -0.5px;">SPMB</h4>
                    <p style="margin: 0; font-size: 11px; color: #64748b; font-weight: 500;">Admin Panel</p>
                </div>
            </a>
            <div class="close-btn d-xl-none d-block cursor-pointer" onclick="document.querySelector('.left-sidebar').classList.remove('show-sidebar')" style="color: #64748b;">
                <i class="ti ti-x" style="font-size: 20px;"></i>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="" style="flex: 1; padding: 16px 12px;">
            <ul id="sidebarnav" style="list-style: none; padding: 0; margin: 0;">
                <li class="sidebar-item" style="margin-bottom: 4px;">
                    <a class="sidebar-link {{ request()->is('admin/dashboard') || request()->is('admin') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" style="
                        display: flex; align-items: center; gap: 12px;
                        padding: 10px 14px; border-radius: 10px;
                        text-decoration: none; transition: all 0.2s;
                        {{ request()->is('admin/dashboard') || request()->is('admin') ? 'background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);' : 'color: #64748b;' }}
                    " onmouseover="if(!this.classList.contains('active')) this.style.background='#f1f5f9'" onmouseout="if(!this.classList.contains('active')) this.style.background='transparent'">
                        <i class="ti ti-layout-dashboard" style="font-size: 20px;"></i>
                        <span class="hide-menu" style="font-size: 14px; font-weight: 500;">Dashboard</span>
                    </a>
                </li>

                <li class="nav-small-cap" style="margin: 20px 0 8px 0;">
                    <span class="hide-menu" style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; padding: 0 14px;">Manajemen</span>
                </li>
                <li class="sidebar-item" style="margin-bottom: 4px;">
                    <a class="sidebar-link {{ request()->is('admin/master-data') ? 'active' : '' }}" href="{{ route('admin.master-data') }}" style="
                        display: flex; align-items: center; gap: 12px;
                        padding: 10px 14px; border-radius: 10px;
                        text-decoration: none; transition: all 0.2s;
                        {{ request()->is('admin/master-data') ? 'background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);' : 'color: #64748b;' }}
                    " onmouseover="if(!this.classList.contains('active')) this.style.background='#f1f5f9'" onmouseout="if(!this.classList.contains('active')) this.style.background='transparent'">
                        <i class="ti ti-database" style="font-size: 20px;"></i>
                        <span class="hide-menu" style="font-size: 14px; font-weight: 500;">Master Data</span>
                    </a>
                </li>
                <li class="sidebar-item" style="margin-bottom: 4px;">
                    <a class="sidebar-link {{ request()->is('admin/monitoring-berkas') ? 'active' : '' }}" href="{{ route('admin.monitoring-berkas') }}" style="
                        display: flex; align-items: center; gap: 12px;
                        padding: 10px 14px; border-radius: 10px;
                        text-decoration: none; transition: all 0.2s;
                        {{ request()->is('admin/monitoring-berkas') ? 'background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);' : 'color: #64748b;' }}
                    " onmouseover="if(!this.classList.contains('active')) this.style.background='#f1f5f9'" onmouseout="if(!this.classList.contains('active')) this.style.background='transparent'">
                        <i class="ti ti-file-check" style="font-size: 20px;"></i>
                        <span class="hide-menu" style="font-size: 14px; font-weight: 500;">Monitoring Berkas</span>
                    </a>
                </li>
                <li class="sidebar-item" style="margin-bottom: 4px;">
                    <a class="sidebar-link {{ request()->is('admin/peta-sebaran') ? 'active' : '' }}" href="{{ route('admin.peta-sebaran') }}" style="
                        display: flex; align-items: center; gap: 12px;
                        padding: 10px 14px; border-radius: 10px;
                        text-decoration: none; transition: all 0.2s;
                        {{ request()->is('admin/peta-sebaran') ? 'background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);' : 'color: #64748b;' }}
                    " onmouseover="if(!this.classList.contains('active')) this.style.background='#f1f5f9'" onmouseout="if(!this.classList.contains('active')) this.style.background='transparent'">
                        <i class="ti ti-map" style="font-size: 20px;"></i>
                        <span class="hide-menu" style="font-size: 14px; font-weight: 500;">Peta Sebaran</span>
                    </a>
                </li>
                <li class="sidebar-item" style="margin-bottom: 4px;">
                    <a class="sidebar-link {{ request()->is('admin/pengumuman') ? 'active' : '' }}" href="{{ route('admin.pengumuman') }}" style="
                        display: flex; align-items: center; gap: 12px;
                        padding: 10px 14px; border-radius: 10px;
                        text-decoration: none; transition: all 0.2s;
                        {{ request()->is('admin/pengumuman') ? 'background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);' : 'color: #64748b;' }}
                    " onmouseover="if(!this.classList.contains('active')) this.style.background='#f1f5f9'" onmouseout="if(!this.classList.contains('active')) this.style.background='transparent'">
                        <i class="ti ti-speakerphone" style="font-size: 20px;"></i>
                        <span class="hide-menu" style="font-size: 14px; font-weight: 500;">Pengumuman</span>
                    </a>
                </li>
                <li class="sidebar-item" style="margin-bottom: 4px;">
                    <a class="sidebar-link {{ request()->is('reports*') ? 'active' : '' }}" href="{{ route('reports.index') }}" style="
                        display: flex; align-items: center; gap: 12px;
                        padding: 10px 14px; border-radius: 10px;
                        text-decoration: none; transition: all 0.2s;
                        {{ request()->is('reports*') ? 'background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);' : 'color: #64748b;' }}
                    " onmouseover="if(!this.classList.contains('active')) this.style.background='#f1f5f9'" onmouseout="if(!this.classList.contains('active')) this.style.background='transparent'">
                        <i class="ti ti-file-export" style="font-size: 20px;"></i>
                        <span class="hide-menu" style="font-size: 14px; font-weight: 500;">Export Laporan</span>
                    </a>
                </li>
                <li class="sidebar-item" style="margin-bottom: 4px;">
                    <a class="sidebar-link {{ request()->is('admin/payment*') ? 'active' : '' }}" href="{{ route('admin.payment.dashboard') }}" style="
                        display: flex; align-items: center; gap: 12px;
                        padding: 10px 14px; border-radius: 10px;
                        text-decoration: none; transition: all 0.2s;
                        {{ request()->is('admin/payment*') ? 'background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);' : 'color: #64748b;' }}
                    " onmouseover="if(!this.classList.contains('active')) this.style.background='#f1f5f9'" onmouseout="if(!this.classList.contains('active')) this.style.background='transparent'">
                        <i class="ti ti-credit-card" style="font-size: 20px;"></i>
                        <span class="hide-menu" style="font-size: 14px; font-weight: 500;">Payment Gateway</span>
                    </a>
                </li>

                <li class="nav-small-cap" style="margin: 20px 0 8px 0;">
                    <span class="hide-menu" style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; padding: 0 14px;">Sistem</span>
                </li>
                <li class="sidebar-item" style="margin-bottom: 4px;">
                    <a class="sidebar-link {{ request()->is('admin/audit-logs*') ? 'active' : '' }}" href="{{ route('admin.audit-logs.index') }}" style="
                        display: flex; align-items: center; gap: 12px;
                        padding: 10px 14px; border-radius: 10px;
                        text-decoration: none; transition: all 0.2s;
                        {{ request()->is('admin/audit-logs*') ? 'background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);' : 'color: #64748b;' }}
                    " onmouseover="if(!this.classList.contains('active')) this.style.background='#f1f5f9'" onmouseout="if(!this.classList.contains('active')) this.style.background='transparent'">
                        <i class="ti ti-history" style="font-size: 20px;"></i>
                        <span class="hide-menu" style="font-size: 14px; font-weight: 500;">Audit Log</span>
                    </a>
                </li>
                <li class="sidebar-item" style="margin-bottom: 4px;">
                    <a class="sidebar-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}" style="
                        display: flex; align-items: center; gap: 12px;
                        padding: 10px 14px; border-radius: 10px;
                        text-decoration: none; transition: all 0.2s;
                        {{ request()->is('admin/users*') ? 'background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);' : 'color: #64748b;' }}
                    " onmouseover="if(!this.classList.contains('active')) this.style.background='#f1f5f9'" onmouseout="if(!this.classList.contains('active')) this.style.background='transparent'">
                        <i class="ti ti-users" style="font-size: 20px;"></i>
                        <span class="hide-menu" style="font-size: 14px; font-weight: 500;">Kelola Akun</span>
                    </a>
                </li>
                <li class="sidebar-item" style="margin-bottom: 4px;">
                    <a class="sidebar-link {{ request()->is('admin/system-settings') ? 'active' : '' }}" href="{{ route('admin.system-settings') }}" style="
                        display: flex; align-items: center; gap: 12px;
                        padding: 10px 14px; border-radius: 10px;
                        text-decoration: none; transition: all 0.2s;
                        {{ request()->is('admin/system-settings') ? 'background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);' : 'color: #64748b;' }}
                    " onmouseover="if(!this.classList.contains('active')) this.style.background='#f1f5f9'" onmouseout="if(!this.classList.contains('active')) this.style.background='transparent'">
                        <i class="ti ti-settings" style="font-size: 20px;"></i>
                        <span class="hide-menu" style="font-size: 14px; font-weight: 500;">Pengaturan</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- User Profile -->
        <div style="padding: 16px 20px; border-top: 1px solid #e2e8f0; background: #f8fafc;">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #818cf8, #6366f1); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);">
                    <span style="color: #fff; font-weight: 700; font-size: 14px;">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div style="font-weight: 600; font-size: 13px; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->name ?? 'Admin SPMB' }}</div>
                    <div style="font-size: 11px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->email ?? 'admin@spmb.sch.id' }}</div>
                </div>
            </div>
        </div>
    </div>
</aside>
<!-- Sidebar End -->