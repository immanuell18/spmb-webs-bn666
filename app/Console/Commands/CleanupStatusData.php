<?php

namespace App\Console\Commands;

use App\Models\Pendaftar;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupStatusData extends Command
{
    protected $signature = 'spmb:cleanup-status {--dry-run : Show what would be changed without making changes}';
    protected $description = 'Cleanup and standardize status data according to UKK requirements';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        $this->info('Starting status data cleanup...');
        
        // Migrate existing status data
        $pendaftarCount = Pendaftar::count();
        $this->info("Processing {$pendaftarCount} pendaftar records...");
        
        $bar = $this->output->createProgressBar($pendaftarCount);
        $bar->start();
        
        $updated = 0;
        
        Pendaftar::chunk(100, function ($pendaftars) use (&$updated, $bar, $dryRun) {
            foreach ($pendaftars as $pendaftar) {
                $newData = $this->migrateStatusData($pendaftar);
                
                if (!$dryRun && !empty($newData)) {
                    $pendaftar->update($newData);
                    $updated++;
                }
                
                $bar->advance();
            }
        });
        
        $bar->finish();
        $this->newLine();
        
        if ($dryRun) {
            $this->info("Would update {$updated} records");
        } else {
            $this->info("Updated {$updated} records successfully");
        }
        
        // Validate coordinates
        $this->validateCoordinates($dryRun);
        
        $this->info('Status cleanup completed!');
    }

    private function migrateStatusData($pendaftar)
    {
        $updates = [];
        
        // Migrate status based on old fields
        if ($pendaftar->status_pembayaran === 'LUNAS') {
            $updates['status'] = 'PAID';
            $updates['tanggal_pembayaran'] = $pendaftar->tanggal_validasi_bayar;
        } elseif (isset($pendaftar->status_verifikasi) && $pendaftar->status_verifikasi === 'DITERIMA') {
            $updates['status'] = 'ADM_PASS';
            $updates['tanggal_verifikasi'] = $pendaftar->tanggal_verifikasi ?? null;
        } elseif (isset($pendaftar->status_verifikasi) && $pendaftar->status_verifikasi === 'DITOLAK') {
            $updates['status'] = 'ADM_REJECT';
        }
        
        // Consolidate notes
        $notes = [];
        if ($pendaftar->catatan_verifikasi) {
            $notes[] = 'Verifikasi: ' . $pendaftar->catatan_verifikasi;
        }
        if ($pendaftar->catatan_pembayaran) {
            $notes[] = 'Pembayaran: ' . $pendaftar->catatan_pembayaran;
        }
        
        if (!empty($notes)) {
            $updates['catatan_admin'] = implode(' | ', $notes);
        }
        
        return $updates;
    }

    private function validateCoordinates($dryRun)
    {
        $this->info('Validating coordinates...');
        
        $invalidCoords = DB::table('pendaftar_data_siswa')
            ->where(function($query) {
                $query->whereNotNull('lat')
                      ->whereNotNull('lng')
                      ->where(function($q) {
                          $q->where('lat', '<', -11)    // Indonesia southernmost
                            ->orWhere('lat', '>', 6)     // Indonesia northernmost  
                            ->orWhere('lng', '<', 95)    // Indonesia westernmost
                            ->orWhere('lng', '>', 141);  // Indonesia easternmost
                      });
            })
            ->count();
            
        if ($invalidCoords > 0) {
            $this->warn("Found {$invalidCoords} records with invalid coordinates");
            
            if (!$dryRun) {
                DB::table('pendaftar_data_siswa')
                    ->where(function($query) {
                        $query->whereNotNull('lat')
                              ->whereNotNull('lng')
                              ->where(function($q) {
                                  $q->where('lat', '<', -11)
                                    ->orWhere('lat', '>', 6)
                                    ->orWhere('lng', '<', 95)
                                    ->orWhere('lng', '>', 141);
                              });
                    })
                    ->update([
                        'koordinat_valid' => false,
                        'sumber_koordinat' => 'INVALID'
                    ]);
                    
                $this->info('Marked invalid coordinates');
            }
        }
        
        // Mark valid coordinates
        $validCoords = DB::table('pendaftar_data_siswa')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->whereBetween('lat', [-11, 6])
            ->whereBetween('lng', [95, 141])
            ->count();
            
        if (!$dryRun && $validCoords > 0) {
            DB::table('pendaftar_data_siswa')
                ->whereNotNull('lat')
                ->whereNotNull('lng')
                ->whereBetween('lat', [-11, 6])
                ->whereBetween('lng', [95, 141])
                ->whereNull('sumber_koordinat')
                ->update([
                    'koordinat_valid' => true,
                    'sumber_koordinat' => 'EXISTING'
                ]);
        }
        
        $this->info("Validated {$validCoords} coordinate records");
    }
}