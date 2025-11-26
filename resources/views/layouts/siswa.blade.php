@extends('layouts.app')

@section('styles')
    <!-- Favicon -->
    <link href="{{ asset('assets/images/logo-sekolah.png') }}" rel="icon" type="image/png">
    <link href="{{ asset('assets/images/logo-sekolah.png') }}" rel="shortcut icon" type="image/png">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('assets/public_css/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/public_css/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('assets/public_css/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('assets/public_css/css/style.css') }}" rel="stylesheet">
    
    <!-- Cursor Fix -->
    <link href="{{ asset('assets/css/cursor-fix.css') }}" rel="stylesheet">
@endsection

@section('body')
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    @include('partials.siswa.header')

    @yield('content')

    @include('partials.footer')
@endsection

@section('scripts')
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/public_css/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('assets/public_css/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('assets/public_css/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/public_css/lib/owlcarousel/owl.carousel.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('assets/public_css/js/main.js') }}"></script>
@endsection