@extends('layouts.app')

@section('styles')
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logo-sekolah.png') }}" />
    <link href="{{ asset('assets/images/logo-sekolah.png') }}" rel="icon" type="image/png">
    <link rel="stylesheet" href="{{ asset('assets/admin_css/css/styles.min.css') }}" />
    <link href="{{ asset('assets/css/cursor-fix.css') }}" rel="stylesheet">
    <style>
        .left-sidebar {
            top: 0 !important;
            margin-top: 0 !important;
        }
        .body-wrapper {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        .app-header {
            top: 0 !important;
        }
        .container-fluid {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }
    </style>
@endsection

@section('body')
    <!--  Body Wrapper -->
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
        
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <ul class="navbar-nav">
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-icon-hover" href="javascript:void(0)">
                                <i class="ti ti-bell-ringing"></i>
                                <div class="notification bg-primary rounded-circle"></div>
                            </a>
                        </li>
                    </ul>
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                            <span class="me-3 text-muted">
                                @if(auth()->user()->role == 'verifikator_adm')
                                    Verifikator: {{ auth()->user()->name }}
                                @else
                                    {{ ucfirst(auth()->user()->role) }}: {{ auth()->user()->name }}
                                @endif
                            </span>
                            <a href="{{ url('/') }}" target="_blank" class="btn btn-primary me-2">View Site</a>
                            <li class="nav-item dropdown">
                                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <img src="{{ asset('assets/admin_css/images/profile/user1.jpg') }}" alt="" width="35" height="35" class="rounded-circle">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                                    <div class="message-body">
                                        <a href="{{ route('admin.profile') }}" class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-user fs-6"></i>
                                            <p class="mb-0 fs-3">My Profile</p>
                                        </a>
                                        <form action="{{ route('logout') }}" method="POST" class="mx-3 mt-2">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary d-block w-100">Logout</button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!--  Header End -->
            
            <div class="container-fluid">
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
@endsection