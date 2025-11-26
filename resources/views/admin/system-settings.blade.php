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
                        
                        <div class="mb-3">
                            <label for="biaya_pendaftaran" class="form-label">Biaya Pendaftaran (Rp)</label>
                            <input type="number" class="form-control @error('biaya_pendaftaran') is-invalid @enderror" 
                                   id="biaya_pendaftaran" name="biaya_pendaftaran" 
                                   value="{{ old('biaya_pendaftaran', $settings['biaya_pendaftaran']->value ?? 250000) }}" 
                                   min="0" step="1000">
                            @error('biaya_pendaftaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Biaya pendaftaran yang akan dikenakan kepada calon siswa</div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection