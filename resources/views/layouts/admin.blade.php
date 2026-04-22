@extends('layouts.app')

@section('styles')
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logo-sekolah.png') }}" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Spike Admin base -->
    <link rel="stylesheet" href="{{ asset('assets/admin_css/css/styles.min.css') }}" />

    <!-- Premium Panel Override -->
    <link href="{{ asset('assets/css/panel-premium.css') }}" rel="stylesheet">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('body')
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        @if(auth()->user()->role == 'admin')
            @include('partials.admin.sidebar')
        @elseif(auth()->user()->role == 'verifikator_adm')
            @include('partials.verifikator.sidebar')
        @elseif(auth()->user()->role == 'keuangan')
            @include('partials.keuangan.sidebar')
        @elseif(auth()->user()->role == 'kepsek')
            @include('partials.kepsek.sidebar')
        @endif

        <div class="body-wrapper">
            <!-- Hidden default header -->
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <ul class="navbar-nav">
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </header>

            <div class="container-fluid">
                <!-- Top Bar (like reference search/date bar) -->
                <div class="d-flex align-items-center justify-content-between mb-4" style="
                    background: #fff;
                    border-radius: 16px;
                    padding: 12px 20px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
                ">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Mobile menu toggle -->
                        <a class="d-block d-xl-none" href="javascript:void(0)" onclick="document.querySelector('.left-sidebar').classList.toggle('show-sidebar')" style="color: #64748b;">
                            <i class="ti ti-menu-2" style="font-size: 20px;"></i>
                        </a>
                        <div class="d-none d-md-flex align-items-center position-relative" style="min-width: 220px;">
                            <i class="ti ti-search position-absolute" style="color: #94a3b8; font-size: 16px; left: 16px; z-index: 10;"></i>
                            <input type="text" id="menuSearch" class="form-control" placeholder="Cari menu..." autocomplete="off" style="
                                background: #f0f2f5;
                                border: none;
                                border-radius: 10px;
                                padding: 8px 16px 8px 42px !important;
                                font-size: 13px;
                                width: 100%;
                                color: #334155;
                                box-shadow: none;
                            " onfocus="this.style.background='#fff'; this.style.boxShadow='0 0 0 2px rgba(99,102,241,0.2)'" onblur="this.style.background='#f0f2f5'; this.style.boxShadow='none'">
                        </div>
                        <span class="d-none d-md-inline" style="color: #64748b; font-size: 13px; font-weight: 500;">
                            <i class="ti ti-calendar-event" style="margin-right: 4px;"></i>
                            {{ now()->translatedFormat('l, j F Y') }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="d-none d-sm-inline" style="
                            background: #f0f2f5;
                            border-radius: 10px;
                            padding: 6px 14px;
                            font-size: 12px;
                            font-weight: 500;
                            color: #334155;
                        ">
                            {{ ucfirst(str_replace('_adm','',auth()->user()->role)) }}: {{ auth()->user()->name }}
                        </span>
                        <a href="{{ url('/') }}" target="_blank" class="d-none d-md-flex align-items-center" style="
                            background: #0f172a;
                            color: #fff;
                            border-radius: 10px;
                            padding: 7px 14px;
                            font-size: 12px;
                            font-weight: 500;
                            text-decoration: none;
                            gap: 5px;
                            transition: all .2s;
                        " onmouseover="this.style.background='#1e293b'" onmouseout="this.style.background='#0f172a'">
                            <i class="ti ti-external-link" style="font-size: 14px;"></i> Lihat Situs
                        </a>
                        <div class="dropdown">
                            <a href="javascript:void(0)" data-bs-toggle="dropdown">
                                <div style="
                                    width: 36px; height: 36px;
                                    background: linear-gradient(135deg, #818cf8, #6366f1);
                                    border-radius: 10px;
                                    display: flex; align-items: center; justify-content: center;
                                    color: #fff; font-weight: 600; font-size: 13px;
                                    cursor: pointer;
                                ">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" style="min-width: 200px;">
                                <div style="padding: 10px 14px; border-bottom: 1px solid #f3f4f6; margin-bottom: 4px;">
                                    <div style="font-weight: 600; font-size: 13px; color: #0f172a;">{{ auth()->user()->name }}</div>
                                    <div style="font-size: 11px; color: #94a3b8;">{{ auth()->user()->email }}</div>
                                </div>
                                <a href="{{ route('admin.profile') }}" class="dropdown-item">
                                    <i class="ti ti-user" style="font-size: 16px; margin-right: 6px;"></i> Profil Saya
                                </a>
                                <div style="padding: 6px;">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm d-block w-100" style="justify-content: center;">
                                            <i class="ti ti-logout"></i> Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @yield('content')
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/admin_css/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/admin_css/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admin_css/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('assets/admin_css/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/admin_css/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin_css/libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="{{ asset('assets/admin_css/js/dashboard.js') }}"></script>
    
    <!-- Menu Search Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById('menuSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const term = e.target.value.toLowerCase().trim();
                    const sidebarItems = document.querySelectorAll('#sidebarnav .sidebar-item');
                    
                    if (term === '') {
                        sidebarItems.forEach(item => item.style.display = '');
                        document.querySelectorAll('.nav-small-cap').forEach(cap => cap.style.display = '');
                        return;
                    }
                    
                    sidebarItems.forEach(function(item) {
                        const text = item.textContent.toLowerCase();
                        if (text.includes(term)) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // Hide nav headers if all child items are hidden
                    const navItems = document.querySelectorAll('#sidebarnav > li');
                    let currentCap = null;
                    let capHasVisibleChild = false;
                    
                    navItems.forEach(function(item) {
                        if (item.classList.contains('nav-small-cap')) {
                            if (currentCap) {
                                currentCap.style.display = capHasVisibleChild ? '' : 'none';
                            }
                            currentCap = item;
                            capHasVisibleChild = false;
                        } else if (item.style.display !== 'none') {
                            capHasVisibleChild = true;
                        }
                    });
                    if (currentCap) {
                        currentCap.style.display = capHasVisibleChild ? '' : 'none';
                    }
                });
            }
        });
    </script>
@endsection