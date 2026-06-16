<?php

namespace Tests\Feature;

use App\Livewire\Bendahara\Dashboard;
use App\Livewire\Bendahara\KeuanganManager;
use App\Models\Kamar;
use App\Models\Kelas;
use App\Models\KonfigurasiKeuangan;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BendaharaDashboardTest extends TestCase
{
    use RefreshDatabase;

    private $ta;

    private $kamar;

    private $kelas;

    private $bendahara;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ta = TahunAjaran::create([
            'nama' => '1447H',
            'tahun_hijri' => 1447,
            'is_aktif' => true,
            'koreksi_hilal' => 0,
        ]);

        $this->kamar = Kamar::create(['nama_kamar' => 'Kamar Utama']);
        $this->kelas = Kelas::create(['nama_kelas' => 'Kelas Satu']);
        $this->bendahara = User::factory()->create(['role' => 'bendahara']);
    }

    public function test_non_bendahara_cannot_access_dashboard()
    {
        $ustaz = User::factory()->create(['role' => 'ustaz']);

        $this->actingAs($ustaz)
            ->get('/bendahara/dashboard')
            ->assertStatus(403);
    }

    public function test_bendahara_dashboard_calculates_payment_metrics_correctly()
    {
        // Student A: Fully Paid (Lunas)
        $santriA = Santri::create([
            'nama_lengkap' => 'Santri Lunas',
            'tempat_lahir' => 'Semarang',
            'tanggal_lahir' => '2010-01-01',
            'alamat' => 'Jl. Lunas',
            'status' => 'aktif',
            'kelas_id' => $this->kelas->id,
            'kamar_id' => $this->kamar->id,
        ]);

        Tagihan::create([
            'santri_id' => $santriA->id,
            'tahun_ajaran_id' => $this->ta->id,
            'kategori' => 'daftar_ulang',
            'nominal' => 500000,
            'status' => 'lunas',
            'nominal_terbayar' => 500000,
        ]);

        Tagihan::create([
            'santri_id' => $santriA->id,
            'tahun_ajaran_id' => $this->ta->id,
            'kategori' => 'majeg_makan',
            'nominal' => 300000,
            'status' => 'pulang',
            'nominal_terbayar' => 0,
        ]);

        // Student B: Unpaid (Belum Lunas/Tunggakan)
        $santriB = Santri::create([
            'nama_lengkap' => 'Santri Menunggak',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '2010-02-02',
            'alamat' => 'Jl. Tunggakan',
            'status' => 'aktif',
            'kelas_id' => $this->kelas->id,
            'kamar_id' => $this->kamar->id,
        ]);

        Tagihan::create([
            'santri_id' => $santriB->id,
            'tahun_ajaran_id' => $this->ta->id,
            'kategori' => 'daftar_ulang',
            'nominal' => 500000,
            'status' => 'lunas',
            'nominal_terbayar' => 500000,
        ]);

        Tagihan::create([
            'santri_id' => $santriB->id,
            'tahun_ajaran_id' => $this->ta->id,
            'kategori' => 'majeg_makan',
            'nominal' => 300000,
            'status' => 'belum_bayar',
            'nominal_terbayar' => 0,
        ]);

        // Student C: Non-active student with outstanding bill (should not be counted in active dashboard stats)
        $santriC = Santri::create([
            'nama_lengkap' => 'Santri Nonaktif',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2010-03-03',
            'alamat' => 'Jl. Nonaktif',
            'status' => 'nonaktif',
            'kelas_id' => $this->kelas->id,
            'kamar_id' => $this->kamar->id,
        ]);

        Tagihan::create([
            'santri_id' => $santriC->id,
            'tahun_ajaran_id' => $this->ta->id,
            'kategori' => 'majeg_makan',
            'nominal' => 300000,
            'status' => 'belum_bayar',
            'nominal_terbayar' => 0,
        ]);

        // Run component test
        Livewire::actingAs($this->bendahara)
            ->test(Dashboard::class)
            ->assertSet('santriLunasCount', 1)
            ->assertSet('santriBelumLunasCount', 1)
            ->assertSet('persenSantriLunas', 50)
            ->assertSee('Status Pembayaran Santri')
            ->assertSee('Lunas:')
            ->assertSee('Belum:')
            ->assertSee('50%');
    }

    public function test_bendahara_can_update_global_financial_configuration()
    {
        // Create a configuration first
        KonfigurasiKeuangan::create([
            'tahun_ajaran_id' => $this->ta->id,
            'nominal_daftar_ulang' => 500000,
            'nominal_syahriah_sem1' => 1200000,
            'nominal_syahriah_sem2' => 1000000,
            'nominal_majeg_makan' => 300000,
        ]);

        // Create an active student with a bill
        $santri = Santri::create([
            'nama_lengkap' => 'Santri Tes Sync',
            'tempat_lahir' => 'Semarang',
            'tanggal_lahir' => '2010-01-01',
            'alamat' => 'Jl. Test',
            'status' => 'aktif',
            'kelas_id' => $this->kelas->id,
            'kamar_id' => $this->kamar->id,
        ]);

        $tagihan = Tagihan::create([
            'santri_id' => $santri->id,
            'tahun_ajaran_id' => $this->ta->id,
            'kategori' => 'daftar_ulang',
            'nominal' => 500000,
            'status' => 'belum_bayar',
            'nominal_terbayar' => 0,
        ]);

        Livewire::actingAs($this->bendahara)
            ->test(KeuanganManager::class)
            ->call('openConfigModal')
            ->assertSet('isConfigModalOpen', true)
            ->set('configDaftarUlang', 600000)
            ->set('configSyahriahSem1', 1300000)
            ->set('configSyahriahSem2', 1100000)
            ->set('configMajegMakan', 350000)
            ->call('saveGlobalConfig')
            ->assertSet('isConfigModalOpen', false);

        $this->assertDatabaseHas('konfigurasi_keuangans', [
            'tahun_ajaran_id' => $this->ta->id,
            'nominal_daftar_ulang' => 600000,
            'nominal_syahriah_sem1' => 1300000,
            'nominal_syahriah_sem2' => 1100000,
            'nominal_majeg_makan' => 350000,
        ]);

        // Assert that the student's bill was also updated to 600,000!
        $this->assertEquals(600000, $tagihan->fresh()->nominal);
    }
}
