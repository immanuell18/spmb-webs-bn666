@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pengaturan Sistem</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('admin.system-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="biaya_pendaftaran" class="form-label">Biaya Pendaftaran (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="text" class="form-control rupiah-input @error('biaya_pendaftaran') is-invalid @enderror" 
                                       data-target="#biaya_pendaftaran_hidden" placeholder="0" required>
                                <input type="hidden" id="biaya_pendaftaran_hidden" name="biaya_pendaftaran" 
                                       value="{{ old('biaya_pendaftaran', $settings['biaya_pendaftaran']->value ?? 250000) }}">
                            </div>
                            @error('biaya_pendaftaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">Biaya *default* yang dikenakan saat pendaftar membuat akun baru. (Bisa ditimpa oleh harga dari Gelombang yang aktif)</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection