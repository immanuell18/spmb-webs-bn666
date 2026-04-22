<?php

namespace Tests\Feature;

use App\Models\Gelombang;
use App\Models\Jurusan;
use App\Models\Pendaftar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PendaftaranTest
 *
 * Test alur pendaftaran siswa SPMB:
 * 1. Halaman pendaftaran dapat diakses
 * 2. Validasi form pendaftaran
 * 3. Pendaftaran berhasil → status SUBMIT
 * 4. Verifikasi administrasi → ADM_PASS / ADM_REJECT
 * 5. Scope-scope Pendaftar model
 * 6. Status label & progress percentage
 * 7. Model helper methods
 */
class PendaftaranTest extends TestCase
{
    use RefreshDatabase;

    // ================================================================
    // Helpers
    // ================================================================

    /** Buat user pendaftar yang sudah login */
    private function loginAsPendaftar(?array $attributes = []): User
    {
        $user = User::factory()->create(array_merge(['role' => 'pendaftar'], $attributes));
        $this->actingAs($user);
        return $user;
    }

    /** Buat user admin yang sudah login */
    private function loginAsAdmin(): User
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);
        return $user;
    }

    /** Buat jurusan + gelombang aktif untuk test — pakai factory */
    private function buatJurusanDanGelombang(): array
    {
        $jurusan   = Jurusan::factory()->create();
        $gelombang = Gelombang::factory()->aktif()->create();

        return compact('jurusan', 'gelombang');
    }

    // ================================================================
    // Akses Halaman
    // ================================================================

    #[Test]
    public function halaman_pendaftaran_dapat_diakses_siswa(): void
    {
        $this->loginAsPendaftar();

        // Pakai factory agar tidak redirect ke dashboard
        Gelombang::factory()->aktif()->create();
        Jurusan::factory()->create();

        $response = $this->get(route('siswa.pendaftaran'));

        $response->assertStatus(200);
    }

    #[Test]
    public function halaman_pendaftaran_tidak_dapat_diakses_tamu(): void
    {
        $response = $this->get(route('siswa.pendaftaran'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function halaman_dashboard_siswa_dapat_diakses(): void
    {
        $this->loginAsPendaftar();

        $response = $this->get(route('siswa.dashboard'));

        $response->assertStatus(200);
    }

    // ================================================================
    // Status & Label Model
    // ================================================================

    #[Test]
    public function pendaftar_baru_punya_status_submit(): void
    {
        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();

        $pendaftar = Pendaftar::factory()->create([
            'jurusan_id'   => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);

        $this->assertEquals(Pendaftar::STATUS_SUBMIT, $pendaftar->status);
    }

    #[Test]
    public function status_label_sesuai_dengan_status_pendaftar(): void
    {
        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();

        $pendaftar = Pendaftar::factory()->create([
            'jurusan_id'   => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'status'       => Pendaftar::STATUS_SUBMIT,
        ]);

        $this->assertStringContainsString(
            'Menunggu',
            $pendaftar->getStatusLabel()
        );
    }

    #[Test]
    public function status_label_adm_pass(): void
    {
        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();

        $pendaftar = Pendaftar::factory()->admPass()->create([
            'jurusan_id'   => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);

        $this->assertEquals(Pendaftar::STATUS_ADM_PASS, $pendaftar->status);
        $this->assertStringContainsString('Bayar', $pendaftar->getStatusLabel());
    }

    #[Test]
    public function status_label_adm_reject(): void
    {
        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();

        $pendaftar = Pendaftar::factory()->rejected()->create([
            'jurusan_id'   => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);

        $this->assertEquals(Pendaftar::STATUS_ADM_REJECT, $pendaftar->status);
        $this->assertStringContainsString('Ditolak', $pendaftar->getStatusLabel());
    }

    #[Test]
    public function status_label_paid(): void
    {
        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();

        $pendaftar = Pendaftar::factory()->paid()->create([
            'jurusan_id'   => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);

        $this->assertEquals(Pendaftar::STATUS_PAID, $pendaftar->status);
        $this->assertStringContainsString('Bayar', $pendaftar->getStatusLabel());
    }

    #[Test]
    public function status_akhir_lulus_tampil_dengan_benar(): void
    {
        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();

        $pendaftar = Pendaftar::factory()->announced('LULUS')->create([
            'jurusan_id'   => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);

        $this->assertEquals('LULUS', $pendaftar->getStatusLabel());
        $this->assertEquals(100, $pendaftar->getProgressPercentage());
    }

    #[Test]
    public function status_akhir_tidak_lulus_tampil_dengan_benar(): void
    {
        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();

        $pendaftar = Pendaftar::factory()->announced('TIDAK_LULUS')->create([
            'jurusan_id'   => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);

        $this->assertEquals('TIDAK LULUS', $pendaftar->getStatusLabel());
    }

    // ================================================================
    // Progress Percentage
    // ================================================================

    #[Test]
    public function progress_percentage_sesuai_tiap_status(): void
    {
        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();

        $base = ['jurusan_id' => $jurusan->id, 'gelombang_id' => $gelombang->id];

        $submit = Pendaftar::factory()->create($base + ['status' => Pendaftar::STATUS_SUBMIT]);
        $this->assertEquals(25, $submit->getProgressPercentage());

        $admPass = Pendaftar::factory()->admPass()->create($base);
        $this->assertEquals(50, $admPass->getProgressPercentage());

        $paid = Pendaftar::factory()->paid()->create($base);
        $this->assertEquals(75, $paid->getProgressPercentage());
    }

    // ================================================================
    // Helper Methods
    // ================================================================

    #[Test]
    public function can_proceed_to_payment_hanya_saat_adm_pass(): void
    {
        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();

        $base = ['jurusan_id' => $jurusan->id, 'gelombang_id' => $gelombang->id];

        $submit = Pendaftar::factory()->create($base);
        $this->assertFalse($submit->canProceedToPayment());

        $admPass = Pendaftar::factory()->admPass()->create($base);
        $this->assertTrue($admPass->canProceedToPayment());
    }

    #[Test]
    public function can_be_announced_hanya_saat_paid(): void
    {
        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();

        $base = ['jurusan_id' => $jurusan->id, 'gelombang_id' => $gelombang->id];

        $admPass = Pendaftar::factory()->admPass()->create($base);
        $this->assertFalse($admPass->canBeAnnounced());

        $paid = Pendaftar::factory()->paid()->create($base);
        $this->assertTrue($paid->canBeAnnounced());
    }

    // ================================================================
    // Eloquent Scopes
    // ================================================================

    #[Test]
    public function scope_menunggu_verifikasi_hanya_kembalikan_status_submit(): void
    {
        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();
        $base = ['jurusan_id' => $jurusan->id, 'gelombang_id' => $gelombang->id];

        Pendaftar::factory()->count(3)->create($base);                      // SUBMIT
        Pendaftar::factory()->admPass()->create($base);                     // ADM_PASS
        Pendaftar::factory()->paid()->create($base);                        // PAID

        $this->assertEquals(3, Pendaftar::menungguVerifikasi()->count());
    }

    #[Test]
    public function scope_sudah_verifikasi_hanya_kembalikan_adm_pass(): void
    {
        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();
        $base = ['jurusan_id' => $jurusan->id, 'gelombang_id' => $gelombang->id];

        Pendaftar::factory()->count(2)->create($base);                      // SUBMIT
        Pendaftar::factory()->count(3)->admPass()->create($base);           // ADM_PASS

        $this->assertEquals(3, Pendaftar::sudahVerifikasi()->count());
    }

    #[Test]
    public function scope_sudah_bayar_hanya_kembalikan_paid(): void
    {
        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();
        $base = ['jurusan_id' => $jurusan->id, 'gelombang_id' => $gelombang->id];

        Pendaftar::factory()->count(2)->create($base);                      // SUBMIT
        Pendaftar::factory()->count(2)->paid()->create($base);              // PAID

        $this->assertEquals(2, Pendaftar::sudahBayar()->count());
    }

    #[Test]
    public function scope_ditolak_hanya_kembalikan_adm_reject(): void
    {
        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();
        $base = ['jurusan_id' => $jurusan->id, 'gelombang_id' => $gelombang->id];

        Pendaftar::factory()->count(2)->create($base);                      // SUBMIT
        Pendaftar::factory()->count(2)->rejected()->create($base);          // ADM_REJECT

        $this->assertEquals(2, Pendaftar::ditolak()->count());
    }

    #[Test]
    public function scope_filter_gelombang_filter_dengan_benar(): void
    {
        ['jurusan' => $jurusan] = $this->buatJurusanDanGelombang();

        $gel1 = Gelombang::factory()->aktif()->create();
        $gel2 = Gelombang::factory()->nonaktif()->create();

        Pendaftar::factory()->count(3)->create(['jurusan_id' => $jurusan->id, 'gelombang_id' => $gel1->id]);
        Pendaftar::factory()->count(2)->create(['jurusan_id' => $jurusan->id, 'gelombang_id' => $gel2->id]);

        $this->assertEquals(3, Pendaftar::filterGelombang($gel1->id)->count());
        $this->assertEquals(2, Pendaftar::filterGelombang($gel2->id)->count());
        // Tanpa filter: semua (5 dari gel1+gel2, + 1 dari buatJurusanDanGelombang tidak ada pendaftar)
        $this->assertEquals(5, Pendaftar::filterGelombang(null)->count());
    }

    // ================================================================
    // Admin Monitoring Berkas (pagination)
    // ================================================================

    #[Test]
    public function admin_monitoring_berkas_mengembalikan_halaman_pertama_terpaginasi(): void
    {
        $this->loginAsAdmin();

        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();

        // Buat 25 pendaftar — lebih dari 1 halaman (pageSize=20)
        Pendaftar::factory()->count(25)->create([
            'jurusan_id'   => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);

        $response = $this->get(route('admin.monitoring-berkas'));

        $response->assertStatus(200);
        $response->assertViewHas('pendaftar', fn ($p) => $p->count() <= 20);
    }

    // ================================================================
    // Verifikasi Admin (status flow)
    // ================================================================

    #[Test]
    public function admin_dapat_approve_berkas_pendaftar(): void
    {
        $this->loginAsAdmin();

        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();

        $pendaftar = Pendaftar::factory()->create([
            'jurusan_id'   => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'status'       => Pendaftar::STATUS_SUBMIT,
        ]);

        $response = $this->post(route('admin.verifikasi-berkas', $pendaftar->id), [
            'status'  => 'ADM_PASS',
            'catatan' => null,
        ]);

        $response->assertRedirect();
        $this->assertEquals(
            Pendaftar::STATUS_ADM_PASS,
            $pendaftar->fresh()->status
        );
    }

    #[Test]
    public function admin_dapat_tolak_berkas_pendaftar(): void
    {
        $this->loginAsAdmin();

        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();

        $pendaftar = Pendaftar::factory()->create([
            'jurusan_id'   => $jurusan->id,
            'gelombang_id' => $gelombang->id,
            'status'       => Pendaftar::STATUS_SUBMIT,
        ]);

        $this->post(route('admin.verifikasi-berkas', $pendaftar->id), [
            'status'  => 'ADM_REJECT',
            'catatan' => 'Scan ijazah tidak terbaca.',
        ]);

        $this->assertEquals(
            Pendaftar::STATUS_ADM_REJECT,
            $pendaftar->fresh()->status
        );
    }

    #[Test]
    public function verifikasi_tidak_bisa_dilakukan_user_biasa(): void
    {
        $this->loginAsPendaftar();

        ['jurusan' => $jurusan, 'gelombang' => $gelombang] = $this->buatJurusanDanGelombang();
        $pendaftar = Pendaftar::factory()->create([
            'jurusan_id'   => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);

        $response = $this->post(route('admin.verifikasi-berkas', $pendaftar->id), [
            'status' => 'ADM_PASS',
        ]);

        // Harus ditolak (403 atau redirect ke login/dashboard)
        $this->assertTrue(
            in_array($response->status(), [302, 403]),
            "Expected redirect or 403, got {$response->status()}"
        );

        // Status tidak berubah
        $this->assertEquals(Pendaftar::STATUS_SUBMIT, $pendaftar->fresh()->status);
    }
}
