<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\Pendaftar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * JurusanTest
 *
 * Test CRUD jurusan via Admin\JurusanController:
 * 1. Store → validasi Form Request
 * 2. Update → validasi Form Request
 * 3. Delete → cek guard pendaftar
 * 4. Akses ditolak non-admin
 */
class JurusanTest extends TestCase
{
    use RefreshDatabase;

    private function loginAsAdmin(): User
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        return $admin;
    }

    private function loginAsPendaftar(): User
    {
        $user = User::factory()->create(['role' => 'pendaftar']);
        $this->actingAs($user);
        return $user;
    }

    // ================================================================
    // STORE
    // ================================================================

    #[Test]
    public function admin_dapat_tambah_jurusan_baru(): void
    {
        $this->loginAsAdmin();

        $response = $this->post(route('admin.jurusan.store'), [
            'kode'      => 'RPL',
            'nama'      => 'Rekayasa Perangkat Lunak',
            'deskripsi' => 'Program keahlian RPL',
            'kuota'     => 36,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('jurusan', [
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
        ]);
    }

    #[Test]
    public function store_jurusan_gagal_jika_kode_duplikat(): void
    {
        $this->loginAsAdmin();

        Jurusan::create(['kode' => 'TKJ', 'nama' => 'TKJ Lama', 'kuota' => 36]);

        $response = $this->post(route('admin.jurusan.store'), [
            'kode'  => 'TKJ',   // duplikat!
            'nama'  => 'TKJ Baru',
            'kuota' => 36,
        ]);

        $response->assertSessionHasErrors('kode');
        $this->assertEquals(1, Jurusan::where('kode', 'TKJ')->count());
    }

    #[Test]
    public function store_jurusan_gagal_jika_kuota_nol(): void
    {
        $this->loginAsAdmin();

        $response = $this->post(route('admin.jurusan.store'), [
            'kode'  => 'MM',
            'nama'  => 'Multimedia',
            'kuota' => 0,   // invalid! min:1
        ]);

        $response->assertSessionHasErrors('kuota');
        $this->assertDatabaseMissing('jurusan', ['kode' => 'MM']);
    }

    #[Test]
    public function store_jurusan_gagal_jika_nama_kosong(): void
    {
        $this->loginAsAdmin();

        $response = $this->post(route('admin.jurusan.store'), [
            'kode'  => 'MM',
            'nama'  => '',    // wajib
            'kuota' => 36,
        ]);

        $response->assertSessionHasErrors('nama');
    }

    // ================================================================
    // UPDATE
    // ================================================================

    #[Test]
    public function admin_dapat_update_jurusan(): void
    {
        $this->loginAsAdmin();

        $jurusan = Jurusan::create(['kode' => 'RPL', 'nama' => 'RPL Lama', 'kuota' => 36]);

        $response = $this->put(route('admin.jurusan.update', $jurusan->id), [
            'kode'  => 'RPL',
            'nama'  => 'RPL Baru',
            'kuota' => 40,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('jurusan', [
            'id'   => $jurusan->id,
            'nama' => 'RPL Baru',
            'kuota' => 40,
        ]);
    }

    #[Test]
    public function update_gagal_jika_kuota_tidak_valid(): void
    {
        $this->loginAsAdmin();

        $jurusan = Jurusan::create(['kode' => 'RPL', 'nama' => 'RPL', 'kuota' => 36]);

        $response = $this->put(route('admin.jurusan.update', $jurusan->id), [
            'kode'  => 'RPL',
            'nama'  => 'RPL',
            'kuota' => -5,   // invalid
        ]);

        $response->assertSessionHasErrors('kuota');
        $this->assertDatabaseHas('jurusan', ['id' => $jurusan->id, 'kuota' => 36]);
    }

    // ================================================================
    // DESTROY
    // ================================================================

    #[Test]
    public function admin_dapat_hapus_jurusan_tanpa_pendaftar(): void
    {
        $this->loginAsAdmin();

        $jurusan = Jurusan::create(['kode' => 'AK', 'nama' => 'Akuntansi', 'kuota' => 36]);

        $response = $this->delete(route('admin.jurusan.delete', $jurusan->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('jurusan', ['id' => $jurusan->id]);
    }

    #[Test]
    public function admin_tidak_bisa_hapus_jurusan_yang_sudah_ada_pendaftar(): void
    {
        $this->loginAsAdmin();

        $jurusan   = Jurusan::create(['kode' => 'RPL', 'nama' => 'RPL', 'kuota' => 36]);
        $gelombang = \App\Models\Gelombang::factory()->aktif()->create();

        Pendaftar::factory()->create([
            'jurusan_id'   => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);

        $response = $this->delete(route('admin.jurusan.delete', $jurusan->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        // Jurusan masih ada
        $this->assertDatabaseHas('jurusan', ['id' => $jurusan->id]);
    }

    // ================================================================
    // Akses Kontrol
    // ================================================================

    #[Test]
    public function pendaftar_tidak_bisa_tambah_jurusan(): void
    {
        $this->loginAsPendaftar();

        $response = $this->post(route('admin.jurusan.store'), [
            'kode'  => 'XX',
            'nama'  => 'Hacked',
            'kuota' => 36,
        ]);

        // Harus di-redirect atau 403 — tidak boleh 200
        $this->assertTrue(
            in_array($response->status(), [302, 403]),
            "Expected 302/403, got {$response->status()}"
        );
        $this->assertDatabaseMissing('jurusan', ['kode' => 'XX']);
    }

    #[Test]
    public function tamu_tidak_bisa_akses_jurusan_store(): void
    {
        $response = $this->post(route('admin.jurusan.store'), [
            'kode'  => 'XX',
            'nama'  => 'Hacked',
            'kuota' => 36,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('jurusan', ['kode' => 'XX']);
    }
}
