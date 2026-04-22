<!-- Sidebar Start -->
<aside class="left-sidebar" style="background: #fff; border-right: 1px solid #e2e8f0;">
    <div style="display: flex; flex-direction: column; height: 100%;">
        <!-- Logo -->
        <div class="brand-logo d-flex align-items-center justify-content-between" style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0;">
            <a href="{{ url('/verifikator/dashboard') }}" class="text-nowrap logo-img d-flex align-items-center gap-3" style="text-decoration: none;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);">
                    <i class="ti ti-school" style="font-size: 20px; color: #fff;"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 18px; font-weight: 700; color: #0f172a; letter-spacing: -0.5px;">SPMB</h4>
                    <p style="margin: 0; font-size: 11px; color: #64748b; font-weight: 500;">Verifikator Panel</p>
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
                    <a class="sidebar-link {{ request()->is('verifikator/dashboard') ? 'active' : '' }}" href="{{ route('verifikator.dashboard') }}" style="
                        display: flex; align-items: center; gap: 12px;
                        padding: 10px 14px; border-radius: 10px;
                        text-decoration: none; transition: all 0.2s;
                        {{ request()->is('verifikator/dashboard') ? 'background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);' : 'color: #64748b;' }}
                    " onmouseover="if(!this.classList.contains('active')) this.style.background='#f1f5f9'" onmouseout="if(!this.classList.contains('active')) this.style.background='transparent'">
                        <i class="ti ti-layout-dashboard" style="font-size: 20px;"></i>
                        <span class="hide-menu" style="font-size: 14px; font-weight: 500;">Dashboard</span>
                    </a>
                </li>

                <li class="nav-small-cap" style="margin: 20px 0 8px 0;">
                    <span class="hide-menu" style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; padding: 0 14px;">Verifikasi</span>
                </li>
                
                <li class="sidebar-item" style="margin-bottom: 4px;">
                    <a class="sidebar-link {{ request()->is('verifikator/administrasi') ? 'active' : '' }}" href="{{ route('verifikator.administrasi') }}" style="
                        display: flex; align-items: center; gap: 12px;
                        padding: 10px 14px; border-radius: 10px;
                        text-decoration: none; transition: all 0.2s;
                        {{ request()->is('verifikator/administrasi') ? 'background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);' : 'color: #64748b;' }}
                    " onmouseover="if(!this.classList.contains('active')) this.style.background='#f1f5f9'" onmouseout="if(!this.classList.contains('active')) this.style.background='transparent'">
                        <i class="ti ti-clipboard-list" style="font-size: 20px;"></i>
                        <span class="hide-menu" style="font-size: 14px; font-weight: 500;">Administrasi</span>
                    </a>
                </li>
                <li class="sidebar-item" style="margin-bottom: 4px;">
                    <a class="sidebar-link {{ request()->is('verifikator/verifikasi') || request()->is('verifikator/detail/*') ? 'active' : '' }}" href="{{ route('verifikator.verifikasi') }}" style="
                        display: flex; align-items: center; gap: 12px;
                        padding: 10px 14px; border-radius: 10px;
                        text-decoration: none; transition: all 0.2s;
                        {{ request()->is('verifikator/verifikasi') || request()->is('verifikator/detail/*') ? 'background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);' : 'color: #64748b;' }}
                    " onmouseover="if(!this.classList.contains('active')) this.style.background='#f1f5f9'" onmouseout="if(!this.classList.contains('active')) this.style.background='transparent'">
                        <i class="ti ti-file-check" style="font-size: 20px;"></i>
                        <span class="hide-menu" style="font-size: 14px; font-weight: 500;">Verifikasi Berkas</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- User Profile -->
        <div style="padding: 16px 20px; border-top: 1px solid #e2e8f0; background: #f8fafc;">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #fbbf24, #f59e0b); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);">
                    <span style="color: #fff; font-weight: 700; font-size: 14px;">{{ strtoupper(substr(auth()->user()->name ?? 'V', 0, 1)) }}</span>
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div style="font-weight: 600; font-size: 13px; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->name ?? 'Verifikator' }}</div>
                    <div style="font-size: 11px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->email ?? 'verifikator@sch.id' }}</div>
                </div>
            </div>
        </div>
    </div>
</aside>
<!-- Sidebar End -->