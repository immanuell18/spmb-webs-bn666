@extends('layouts.admin')

@section('title', 'Profil - SPMB Admin')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h3 class="mb-1">Profil Saya</h3>
        <p class="mb-0" style="color: var(--text3); font-size: 13px;">Kelola informasi akun Anda</p>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card mb-4">
                <div class="card-body text-center" style="padding: 32px 20px !important;">
                    <div style="width: 80px; height: 80px; background: var(--p-100); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <span style="font-size: 28px; font-weight: 700; color: var(--p);">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                    </div>
                    <h5 style="font-weight: 600; color: var(--text); font-size: 16px; margin-bottom: 4px;">{{ auth()->user()->name }}</h5>
                    <p style="color: var(--text3); font-size: 13px; margin-bottom: 12px;">{{ auth()->user()->email }}</p>
                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-success">Online</span>
                        <span class="badge bg-primary">{{ ucfirst(str_replace('_adm','',auth()->user()->role)) }}</span>
                    </div>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 fw-semibold">Statistik</h6>
                </div>
                <div class="card-body" style="font-size: 13px;">
                    <div class="d-flex justify-content-between mb-3 pb-3" style="border-bottom: 1px solid var(--border2);">
                        <span style="color: var(--text3);">Total Login</span>
                        <span style="font-weight: 600;">127 kali</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3" style="border-bottom: 1px solid var(--border2);">
                        <span style="color: var(--text3);">Login Terakhir</span>
                        <span style="font-weight: 600;">{{ date('d M Y, H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color: var(--text3);">Member Sejak</span>
                        <span style="font-weight: 600;">{{ auth()->user()->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Profile Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 fw-semibold">Informasi Profil</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ auth()->user()->email }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="{{ ucfirst(str_replace('_adm','',auth()->user()->role)) }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" value="Aktif" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 fw-semibold">Aktivitas Terakhir</h6>
                </div>
                <div class="card-body" style="padding: 20px !important;">
                    @php
                        $activities = [
                            ['time' => date('H:i'), 'text' => 'Login ke sistem', 'icon' => 'ti-login', 'color' => 'var(--p)'],
                            ['time' => date('H:i', strtotime('-30 min')), 'text' => 'Mengakses dashboard', 'icon' => 'ti-layout-dashboard', 'color' => 'var(--ok)'],
                            ['time' => date('H:i', strtotime('-1 hour')), 'text' => 'Verifikasi berkas pendaftar', 'icon' => 'ti-file-check', 'color' => 'var(--info)'],
                            ['time' => date('H:i', strtotime('-2 hours')), 'text' => 'Export data pendaftar', 'icon' => 'ti-download', 'color' => 'var(--warn)'],
                        ];
                    @endphp

                    @foreach($activities as $i => $act)
                    <div class="d-flex align-items-start gap-3 {{ $i < count($activities)-1 ? 'mb-4' : '' }}" style="position: relative;">
                        @if($i < count($activities)-1)
                        <div style="position: absolute; left: 15px; top: 36px; bottom: -16px; width: 1px; background: var(--border2);"></div>
                        @endif
                        <div style="width: 32px; height: 32px; background: {{ str_replace(')', ',0.1)', str_replace('var(', 'rgba(', str_replace('--p', '99,102,241', str_replace('--ok', '16,185,129', str_replace('--info', '6,182,212', str_replace('--warn', '245,158,11', $act['color'])))))) }}; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; z-index: 1;">
                            <i class="ti {{ $act['icon'] }}" style="font-size: 14px; color: {{ $act['color'] }};"></i>
                        </div>
                        <div>
                            <div style="font-weight: 500; font-size: 13px; color: var(--text);">{{ $act['text'] }}</div>
                            <div style="font-size: 11px; color: var(--text4); margin-top: 2px;">{{ $act['time'] }} WIB</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection