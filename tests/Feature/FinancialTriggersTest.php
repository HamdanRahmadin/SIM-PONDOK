<?php

namespace Tests\Feature;

use App\Models\Santri;
use App\Models\Keuangan;
use App\Models\Kelas;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Bendahara\KeuanganManager;

class FinancialTriggersTest extends TestCase
{
    use RefreshDatabase;

    private $santri;
    private $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::setByKey('current_tahun_ajaran', '1447');

        $this->kelas = Kelas::create(['nama_kelas' => 'Kelas A']);
        $this->santri = Santri::create([
            'nama_lengkap' => 'Muhammad Ali',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2012-05-10',
            'alamat' => 'Jl. Kebagusan No. 12',
            'status' => 'aktif',
            'kelas_id' => $this->kelas->id,
        ]);
        
        // Seed default billing records
        $categories = [
            'daftar_ulang',
            'syahriah_dzulqadah',
            'syahriah_semester_1',
            'syahriah_semester_2',
        ];
        foreach ($categories as $cat) {
            Keuangan::create([
                'santri_id' => $this->santri->id,
                'tahun_ajaran' => 1447,
                'kategori' => $cat,
                'status' => 'belum_bayar',
                'nominal_bayar' => 0,
                'catatan' => null
            ]);
        }
    }

    public function test_daftar_ulang_lunas_triggers_syahriah_and_semester_1_pelunasan()
    {
        $daftarUlangBill = Keuangan::where('santri_id', $this->santri->id)
            ->where('kategori', 'daftar_ulang')
            ->first();

        // Acting as a bendahara, run the Livewire component method
        $this->actingAs(\App\Models\User::factory()->create(['role' => 'bendahara']));

        Livewire::test(KeuanganManager::class)
            ->call('markAsLunas', $daftarUlangBill->id);

        // Verify Daftar Ulang is lunas
        $this->assertEquals('lunas', $daftarUlangBill->fresh()->status);

        // Verify Syahriah Dzulqa'dah and Semester 1 were auto-paid
        $syahriahDzul = Keuangan::where('santri_id', $this->santri->id)->where('kategori', 'syahriah_dzulqadah')->first();
        $semester1 = Keuangan::where('santri_id', $this->santri->id)->where('kategori', 'syahriah_semester_1')->first();

        $this->assertEquals('lunas', $syahriahDzul->status);
        $this->assertEquals('lunas', $semester1->status);
    }

    public function test_emergency_reset_action_restores_belum_bayar()
    {
        $bill = Keuangan::where('santri_id', $this->santri->id)
            ->where('kategori', 'syahriah_semester_2')
            ->first();

        $bill->update([
            'status' => 'dicicil',
            'nominal_bayar' => 250000,
            'catatan' => 'Cicilan pertama'
        ]);

        $this->actingAs(\App\Models\User::factory()->create(['role' => 'bendahara']));

        Livewire::test(KeuanganManager::class)
            ->call('resetBill', $bill->id);

        $freshBill = $bill->fresh();
        $this->assertEquals('belum_bayar', $freshBill->status);
        $this->assertEquals(0, $freshBill->nominal_bayar);
        $this->assertNull($freshBill->catatan);
    }
}
