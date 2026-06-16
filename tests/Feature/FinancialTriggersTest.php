<?php

namespace Tests\Feature;

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

class FinancialTriggersTest extends TestCase
{
    use RefreshDatabase;

    private $santri;

    private $kelas;

    private $kamar;

    private $ta;

    private $config;

    private $duTagihan;

    private $s1Tagihan;

    private $mmTagihan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ta = TahunAjaran::create([
            'nama' => '1447H',
            'tahun_hijri' => 1447,
            'is_aktif' => true,
            'koreksi_hilal' => 0,
        ]);

        $this->config = KonfigurasiKeuangan::create([
            'tahun_ajaran_id' => $this->ta->id,
            'nominal_daftar_ulang' => 500000,
            'nominal_syahriah_sem1' => 1000000,
            'nominal_syahriah_sem2' => 1000000,
            'nominal_majeg_makan' => 300000,
        ]);

        $this->kamar = Kamar::create(['nama_kamar' => 'Kamar 1']);
        $this->kelas = Kelas::create(['nama_kelas' => 'Kelas A']);

        $this->santri = Santri::create([
            'nama_lengkap' => 'Muhammad Ali',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2012-05-10',
            'alamat' => 'Jl. Kebagusan No. 12',
            'status' => 'aktif',
            'kelas_id' => $this->kelas->id,
            'kamar_id' => $this->kamar->id,
        ]);

        // Manually create tagihans for the tests
        $this->duTagihan = Tagihan::create([
            'santri_id' => $this->santri->id,
            'tahun_ajaran_id' => $this->ta->id,
            'kategori' => 'daftar_ulang',
            'nominal' => 500000,
            'status' => 'belum_bayar',
            'nominal_terbayar' => 0,
        ]);

        $this->s1Tagihan = Tagihan::create([
            'santri_id' => $this->santri->id,
            'tahun_ajaran_id' => $this->ta->id,
            'kategori' => 'syahriah_sem1',
            'nominal' => 1000000,
            'status' => 'belum_bayar',
            'nominal_terbayar' => 0,
        ]);

        $this->mmTagihan = Tagihan::create([
            'santri_id' => $this->santri->id,
            'tahun_ajaran_id' => $this->ta->id,
            'kategori' => 'majeg_makan',
            'bulan_hijri' => 11,
            'nominal' => 300000,
            'status' => 'belum_bayar',
            'nominal_terbayar' => 0,
        ]);
    }

    public function test_daftar_ulang_lunas_triggers_syahriah_and_semester_1_pelunasan()
    {
        $this->actingAs(User::factory()->create(['role' => 'bendahara']));

        Livewire::test(KeuanganManager::class)
            ->call('openPaymentForm', $this->duTagihan->id)
            ->set('paymentAmount', 500000)
            ->call('savePayment');

        // Verify Daftar Ulang is lunas
        $this->assertEquals('lunas', $this->duTagihan->fresh()->status);

        // Verify Syahriah Semester 1 and Majeg Makan Month 11 were auto-paid/lunas
        $this->assertEquals('lunas', $this->s1Tagihan->fresh()->status);
        $this->assertEquals('lunas', $this->mmTagihan->fresh()->status);
    }

    public function test_emergency_reset_action_restores_belum_bayar()
    {
        $this->actingAs(User::factory()->create(['role' => 'bendahara']));

        Livewire::test(KeuanganManager::class)
            ->call('openPaymentForm', $this->s1Tagihan->id)
            ->set('paymentAmount', 250000)
            ->call('savePayment');

        $this->assertEquals('dicicil', $this->s1Tagihan->fresh()->status);
        $this->assertEquals(250000, $this->s1Tagihan->fresh()->nominal_terbayar);

        // Reset the tagihan
        Livewire::test(KeuanganManager::class)
            ->call('openPaymentForm', $this->s1Tagihan->id)
            ->call('resetTagihan');

        $fresh = $this->s1Tagihan->fresh();
        $this->assertEquals('belum_bayar', $fresh->status);
        $this->assertEquals(0, $fresh->nominal_terbayar);
    }
}
