<?php

namespace Tests\Feature;

use App\Livewire\Admin\SantriManager;
use App\Models\Kamar;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SantriManagerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Kamar $kamar;

    private Kelas $kelas;

    private TahunAjaran $ta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ta = TahunAjaran::create([
            'nama' => '1447H',
            'tahun_hijri' => 1447,
            'is_aktif' => true,
            'koreksi_hilal' => 0,
        ]);

        $this->kamar = Kamar::create(['nama_kamar' => 'Kamar 1']);
        $this->kelas = Kelas::create(['nama_kelas' => 'Kelas 1', 'urutan' => 1]);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_access_santri_manager()
    {
        $response = $this->actingAs($this->admin)->get('/admin/santri');
        $response->assertStatus(200);
    }

    public function test_admin_can_delete_santri()
    {
        $santri = Santri::create([
            'nama_lengkap' => 'Santri Aktif',
            'status' => 'aktif',
            'kamar_id' => $this->kamar->id,
            'kelas_id' => $this->kelas->id,
        ]);

        $this->assertDatabaseHas('santris', ['id' => $santri->id]);

        Livewire::actingAs($this->admin)
            ->test(SantriManager::class)
            ->call('delete', $santri->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('santris', ['id' => $santri->id]);
    }

    public function test_admin_cannot_delete_graduated_santri()
    {
        $santri = Santri::create([
            'nama_lengkap' => 'Santri Lulus',
            'status' => 'lulus',
            'kamar_id' => $this->kamar->id,
            'kelas_id' => $this->kelas->id,
        ]);

        $this->assertDatabaseHas('santris', ['id' => $santri->id]);

        Livewire::actingAs($this->admin)
            ->test(SantriManager::class)
            ->call('delete', $santri->id)
            ->assertHasNoErrors();

        // Should not be deleted because it is 'lulus' (read-only archive)
        $this->assertDatabaseHas('santris', ['id' => $santri->id]);
    }
}
