<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPMB — Sistem Penerimaan Murid Baru')</title>
    <meta name="description" content="@yield('meta_description', 'Sistem Penerimaan Murid Baru online yang cepat, mudah, dan transparan.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('styles')
    @stack('styles')
</head>
<body>
    @yield('body')
    @yield('scripts')
    @stack('scripts')
</body>
</html>