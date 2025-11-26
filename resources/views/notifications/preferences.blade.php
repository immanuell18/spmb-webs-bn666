@extends('layouts.siswa')

@section('title', 'Preferensi Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">⚙️ Preferensi Notifikasi</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('notifications.preferences.update') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5>📧 Metode Notifikasi</h5>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="email_enabled" value="1" 
                                           {{ $preferences->email_enabled ? 'checked' : '' }} id="email_enabled">
                                    <label class="form-check-label" for="email_enabled">
                                        <strong>Email</strong>
                                        <small class="text-muted d-block">Terima notifikasi melalui email</small>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="sms_enabled" value="1" 
                                           {{ $preferences->sms_enabled ? 'checked' : '' }} id="sms_enabled">
                                    <label class="form-check-label" for="sms_enabled">
                                        <strong>SMS</strong>
                                        <small class="text-muted d-block">Terima notifikasi melalui SMS</small>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="whatsapp_enabled" value="1" 
                                           {{ $preferences->whatsapp_enabled ? 'checked' : '' }} id="whatsapp_enabled">
                                    <label class="form-check-label" for="whatsapp_enabled">
                                        <strong>WhatsApp</strong>
                                        <small class="text-muted d-block">Terima notifikasi melalui WhatsApp</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>🔔 Jenis Notifikasi</h5>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="event_preferences[registration]" value="1" 
                                           {{ ($preferences->event_preferences['registration'] ?? true) ? 'checked' : '' }} id="notif_registration">
                                    <label class="form-check-label" for="notif_registration">
                                        <strong>Pendaftaran</strong>
                                        <small class="text-muted d-block">Konfirmasi pendaftaran akun</small>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="event_preferences[verification]" value="1" 
                                           {{ ($preferences->event_preferences['verification'] ?? true) ? 'checked' : '' }} id="notif_verification">
                                    <label class="form-check-label" for="notif_verification">
                                        <strong>Verifikasi Berkas</strong>
                                        <small class="text-muted d-block">Status verifikasi dan perbaikan berkas</small>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="event_preferences[payment]" value="1" 
                                           {{ ($preferences->event_preferences['payment'] ?? true) ? 'checked' : '' }} id="notif_payment">
                                    <label class="form-check-label" for="notif_payment">
                                        <strong>Pembayaran</strong>
                                        <small class="text-muted d-block">Instruksi dan konfirmasi pembayaran</small>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="event_preferences[selection_result]" value="1" 
                                           {{ ($preferences->event_preferences['selection_result'] ?? true) ? 'checked' : '' }} id="notif_result">
                                    <label class="form-check-label" for="notif_result">
                                        <strong>Hasil Seleksi</strong>
                                        <small class="text-muted d-block">Pengumuman hasil seleksi</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('siswa.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Preferensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection