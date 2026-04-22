<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pendaftar;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ExpirePendaftarBelumBayar extends Command
{
    protected $signature = 'spmb:expire-pendaftar
                            {--dry-run : Simulasi tanpa benar-benar mengubah data}';

    protected $description = 'Cek otomatis pendaftar yang sudah lulus ADM tapi belum bayar lebih dari 3x24 jam, 
                              dan kirim email pengingat untuk yang sudah 24 jam belum bayar.';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('⚠️  DRY RUN MODE — Tidak ada data yang diubah.');
        }

        $this->info('🔍 Memulai pengecekan pendaftar belum bayar...');
        $this->newLine();

        // --- 1. Kirim reminder untuk yang 24 jam belum bayar ---
        $reminderPendaftar = Pendaftar::with(['user'])
            ->where('status', Pendaftar::STATUS_ADM_PASS)
            ->where('tgl_verifikasi_adm', '<=', now()->subHours(24))
            ->where('tgl_verifikasi_adm', '>', now()->subHours(72))
            ->get();

        $this->info("📧 Ditemukan {$reminderPendaftar->count()} pendaftar untuk dikirim reminder...");

        foreach ($reminderPendaftar as $pendaftar) {
            $jamBerlalu = now()->diffInHours($pendaftar->tgl_verifikasi_adm);

            if (!$isDryRun) {
                try {
                    Mail::to($pendaftar->email)->send(
                        new \App\Mail\PaymentInstructionMail($pendaftar)
                    );
                    Log::info('Reminder pembayaran terkirim', [
                        'no_pendaftaran' => $pendaftar->no_pendaftaran,
                        'email'          => $pendaftar->email,
                        'jam_berlalu'    => $jamBerlalu,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Gagal kirim reminder: ' . $e->getMessage(), [
                        'no_pendaftaran' => $pendaftar->no_pendaftaran,
                    ]);
                }
            }

            $this->line("  📬 Reminder → {$pendaftar->no_pendaftaran} | {$pendaftar->nama} | {$jamBerlalu}jam berlalu");
        }

        $this->newLine();

        // --- 2. Tandai EXPIRED untuk yang 72 jam (3 hari) belum bayar ---
        $expiredPendaftar = Pendaftar::where('status', Pendaftar::STATUS_ADM_PASS)
            ->where('tgl_verifikasi_adm', '<=', now()->subHours(72))
            ->get();

        $this->warn("⏰ Ditemukan {$expiredPendaftar->count()} pendaftar yang melebihi batas waktu bayar...");

        foreach ($expiredPendaftar as $pendaftar) {
            $jamBerlalu = now()->diffInHours($pendaftar->tgl_verifikasi_adm);

            if (!$isDryRun) {
                $pendaftar->update([
                    'status'       => Pendaftar::STATUS_ADM_REJECT,
                    'catatan_admin' => 'Otomatis ditolak: batas waktu pembayaran 72 jam terlewat.',
                ]);

                Log::info('Pendaftar di-expire otomatis', [
                    'no_pendaftaran' => $pendaftar->no_pendaftaran,
                    'jam_berlalu'    => $jamBerlalu,
                ]);
            }

            $this->line("  ❌ Expired → {$pendaftar->no_pendaftaran} | {$pendaftar->nama} | {$jamBerlalu}jam");
        }

        $this->newLine();
        $this->info('✅ Selesai!');
        $this->table(
            ['Aksi', 'Jumlah'],
            [
                ['📧 Reminder terkirim', $reminderPendaftar->count()],
                ['❌ Di-expire', $expiredPendaftar->count()],
            ]
        );

        Log::info('[Scheduler] spmb:expire-pendaftar selesai', [
            'reminder_count' => $reminderPendaftar->count(),
            'expired_count'  => $expiredPendaftar->count(),
            'dry_run'        => $isDryRun,
        ]);

        return Command::SUCCESS;
    }
}
