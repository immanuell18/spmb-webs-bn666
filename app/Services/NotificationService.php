<?php

namespace App\Services;

use App\Mail\PendaftarNotification;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendNotification($pendaftar, $type, $data = [])
    {
        try {
            // Send email
            Mail::to($pendaftar->user->email)->send(new PendaftarNotification($pendaftar, $type, $data));
            
            // Log notification
            $this->logNotification($pendaftar, $type, 'sent', $data);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'pendaftar_id' => $pendaftar->id,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            
            // Log failed notification
            $this->logNotification($pendaftar, $type, 'failed', $data, $e->getMessage());
            
            return false;
        }
    }
    
    private function logNotification($pendaftar, $type, $status, $data = [], $error = null)
    {
        try {
            NotificationLog::create([
                'pendaftar_id' => $pendaftar->id,
                'type' => $type,
                'status' => $status,
                'data' => json_encode($data),
                'error_message' => $error,
                'sent_at' => $status === 'sent' ? now() : null
            ]);
        } catch (\Exception $e) {
            // Log to Laravel log instead if database logging fails
            Log::warning('Failed to log notification to database', [
                'pendaftar_id' => $pendaftar->id,
                'type' => $type,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function sendAktivasiAkun($pendaftar)
    {
        return $this->sendNotification($pendaftar, 'aktivasi');
    }
    
    public function sendBerkasDitolak($pendaftar, $alasan = null)
    {
        return $this->sendNotification($pendaftar, 'berkas_ditolak', ['alasan' => $alasan]);
    }
    
    public function sendBerkasDiterima($pendaftar)
    {
        return $this->sendNotification($pendaftar, 'berkas_diterima');
    }
    
    public function sendInstruksiBayar($pendaftar, $jumlah, $batasWaktu = '3 hari')
    {
        return $this->sendNotification($pendaftar, 'instruksi_bayar', [
            'jumlah' => $jumlah,
            'batas_waktu' => $batasWaktu
        ]);
    }
    
    public function sendPembayaranDiterima($pendaftar)
    {
        return $this->sendNotification($pendaftar, 'pembayaran_diterima');
    }
    
    public function sendPengumuman($pendaftar, $status)
    {
        return $this->sendNotification($pendaftar, 'pengumuman', ['status' => $status]);
    }
}