<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800 font-heading">Manajemen Lembar Keuangan</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola tagihan, catat cicilan masuk, dan verifikasi status lunas Daftar Ulang.</p>
        </div>
        <div class="flex items-center gap-2 self-start">
            <button wire:click="openConfigModal" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 transition duration-200 flex items-center gap-1.5 cursor-pointer">
                <x-lucide-settings class="w-4 h-4 text-slate-500" /> <span>Konfigurasi Tarif Default</span>
            </button>
            <button wire:click="export" class="bg-emerald-800 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md shadow-emerald-800/10 transition duration-200 flex items-center gap-1.5 cursor-pointer">
                <x-lucide-download class="w-4 h-4" /> <span>Ekspor Excel</span>
            </button>
        </div>
    </div>
 
    <!-- Filters Panel -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center gap-4">
        <!-- Search -->
        <div class="w-full md:flex-1 relative">
            <x-lucide-search class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
            <input wire:model.live="search" type="text" placeholder="Cari nama santri..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/10 transition duration-200">
        </div>
 
        <!-- Kamar Filter -->
        <div class="w-full md:w-52">
            <select wire:model.live="filterKamarId" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-700 focus:outline-none focus:border-emerald-600 cursor-pointer">
                <option value="0">Semua Kamar</option>
                @foreach($kamars as $kamar)
                    <option value="{{ $kamar->id }}">{{ $kamar->nama_kamar }}</option>
                @endforeach
            </select>
        </div>
 
        <!-- Year Filter -->
        <div class="w-full md:w-40">
            <select wire:model.live="selectedYearId" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-700 focus:outline-none focus:border-emerald-600 cursor-pointer">
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_aktif ? '(Aktif)' : '' }}</option>
                @endforeach
            </select>
        </div>
    </div>
 
    <!-- Success/Error Flash Alerts -->
    <x-alert type="success" />
    <x-alert type="error" />
 
    <!-- Santri List Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-bold tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="py-4 px-4 w-10">No</th>
                        <th class="py-4 px-4">Nama Santri</th>
                        <th class="py-4 px-4">Kamar</th>
                        <th class="py-4 px-4 text-center">Daftar Ulang</th>
                        <th class="py-4 px-4 text-center">Syahriah Sem 1</th>
                        <th class="py-4 px-4 text-center">Syahriah Sem 2</th>
                        <th class="py-4 px-4 text-center">Majeg Makan</th>
                        <th class="py-4 px-4 text-right">Sisa Tagihan</th>
                        <th class="py-4 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($santris as $index => $santri)
                        @php
                            // Ambil data tagihan tahun ajaran aktif secara inline
                            $du = $santri->tagihans->where('tahun_ajaran_id', $selectedYearId)->where('kategori', 'daftar_ulang')->first();
                            $s1 = $santri->tagihans->where('tahun_ajaran_id', $selectedYearId)->where('kategori', 'syahriah_sem1')->first();
                            $s2 = $santri->tagihans->where('tahun_ajaran_id', $selectedYearId)->where('kategori', 'syahriah_sem2')->first();
                            $mm = $santri->tagihans->where('tahun_ajaran_id', $selectedYearId)->where('kategori', 'majeg_makan');
                            
                            $mmLunasCount = $mm->where('status', 'lunas')->count();
                            $mmTotalCount = $mm->count();
                            $sisaTagihan = $santri->tagihans->where('tahun_ajaran_id', $selectedYearId)->sum('sisa_tagihan');
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-4 text-slate-400 font-medium">
                                {{ ($santris->currentPage() - 1) * $santris->perPage() + $index + 1 }}
                            </td>
                            <td class="py-4 px-4 font-bold text-slate-800">
                                {{ $santri->nama_lengkap }}
                            </td>
                            <td class="py-4 px-4 text-slate-500 font-medium">
                                {{ $santri->kamar ? $santri->kamar->nama_kamar : 'Tanpa Kamar' }}
                            </td>
                            
                            <!-- Daftar Ulang Badge -->
                            <td class="py-4 px-4 text-center">
                                @if($du)
                                    <x-status-badge :status="$du->status" />
                                @else
                                    <span class="text-slate-300 font-bold">—</span>
                                @endif
                            </td>

                            <!-- Syahriah Sem 1 Badge -->
                            <td class="py-4 px-4 text-center">
                                @if($s1)
                                    <x-status-badge :status="$s1->status" />
                                @else
                                    <span class="text-slate-300 font-bold">—</span>
                                @endif
                            </td>

                            <!-- Syahriah Sem 2 Badge -->
                            <td class="py-4 px-4 text-center">
                                @if($s2)
                                    <x-status-badge :status="$s2->status" />
                                @else
                                    <span class="text-slate-300 font-bold">—</span>
                                @endif
                            </td>
 
                            <!-- Majeg Makan Badge (Aggregated) -->
                            <td class="py-4 px-4 text-center">
                                @if($mmTotalCount > 0)
                                    @if($mmLunasCount === $mmTotalCount)
                                        <span class="px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200">LUNAS</span>
                                    @elseif($mm->where('status', 'pulang')->count() === $mmTotalCount)
                                        <span class="px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-red-50 text-red-800 border border-red-200">PULANG</span>
                                    @elseif($mmLunasCount > 0 || $mm->where('status', 'dicicil')->count() > 0)
                                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 shadow-sm" title="Dicicil">{{ $mmLunasCount }}/{{ $mmTotalCount }} LUNAS</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-500 border border-slate-200">BELUM</span>
                                    @endif
                                @else
                                    <span class="text-slate-300 font-bold">—</span>
                                @endif
                            </td>
 
                            <!-- Sisa Tagihan -->
                            <td class="py-4 px-4 text-right font-extrabold text-slate-800">
                                Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                            </td>
 
                            <!-- Aksi -->
                            <td class="py-4 px-4 text-center">
                                <button wire:click="selectSantri({{ $santri->id }})" class="px-3.5 py-1.5 bg-emerald-800 hover:bg-emerald-950 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-800/10 transition cursor-pointer">
                                    Kelola Keuangan
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400 italic text-xs">
                                Tidak ditemukan data santri yang cocok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $santris->links() }}
        </div>
    </div>
 
    <!-- Keuangan Detail Modal -->
    <x-modal :show="$isModalOpen && $selectedSantri" title="Lembar Tagihan Keuangan Santri" :subtitle="$selectedSantri ? $selectedSantri->nama_lengkap . ' | Kamar: ' . ($selectedSantri->kamar?->nama_kamar ?? '-') : ''" maxWidth="max-w-2xl" closeAction="closeModal">
        @if($selectedSantri && $selectedSantri->status === 'lulus')
            <div class="bg-blue-50 border border-blue-100 p-3 rounded-2xl text-blue-900 text-xs font-semibold flex items-start gap-2 mb-4">
                <x-lucide-info class="w-4 h-4 mt-0.5 shrink-0" /> <span><strong>Arsip Alumni (Lulus)</strong>: Lembar tagihan santri ini bersifat Read-Only (hanya baca). Status pembayaran tidak dapat diubah kembali.</span>
            </div>
        @endif

        @if($selectedSantri && $selectedSantri->status === 'nonaktif')
            <div class="bg-red-50 border border-red-100 p-3 rounded-2xl text-red-900 text-xs font-semibold flex items-start gap-2 mb-4">
                <x-lucide-alert-triangle class="w-4 h-4 mt-0.5 shrink-0" /> <span><strong>Santri Nonaktif</strong>: Sisa tagihan berjalan santri nonaktif dapat ditandai "Pulang" untuk menangguhkan kewajiban bayar.</span>
            </div>
        @endif

        <div class="divide-y divide-slate-100 border border-slate-200/80 rounded-2xl overflow-hidden bg-slate-50/50 p-2">
            @forelse($billings as $bill)
                <div class="p-3.5 hover:bg-white rounded-xl transition space-y-2">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="space-y-0.5">
                            <span class="font-bold text-slate-800 text-[13px] capitalize">
                                {{ str_replace('_', ' ', $bill->kategori) }}
                                @if($bill->bulan_hijri)
                                    @php
                                        $chronologicalMonths = [11 => 1, 12 => 2, 1 => 3, 2 => 4, 3 => 5, 4 => 6, 5 => 7, 6 => 8, 7 => 9, 8 => 10, 9 => 11];
                                        $monthIndex = $chronologicalMonths[$bill->bulan_hijri] ?? $bill->bulan_hijri;
                                        $monthName = \App\Helpers\HijriHelper::getMonthName($bill->bulan_hijri);
                                    @endphp
                                    (Bulan {{ $monthIndex }} - {{ $monthName }})
                                @endif
                            </span>
                            <div class="text-xs text-slate-500 font-medium">
                                Nominal: Rp {{ number_format($bill->nominal, 0, ',', '.') }} | Terbayar: <span class="font-bold text-emerald-800">Rp {{ number_format($bill->nominal_terbayar, 0, ',', '.') }}</span>
                            </div>
                            @if($bill->catatan)
                                <div class="text-xs text-slate-400 italic font-medium">Catatan: {{ $bill->catatan }}</div>
                            @endif
                        </div>

                        <div class="flex items-center space-x-2 shrink-0">
                            <x-status-badge :status="$bill->status" />

                            @if($selectedSantri && $selectedSantri->status !== 'lulus')
                                <div class="flex items-center space-x-1.5 pl-2 border-l border-slate-200">
                                    @if($bill->status !== 'lunas' && $bill->status !== 'pulang')
                                        <button type="button" wire:click="openPaymentForm({{ $bill->id }})" class="px-2 py-1 bg-emerald-800 hover:bg-emerald-950 text-white rounded-lg text-xs font-bold transition cursor-pointer">
                                            Bayar
                                        </button>
                                    @endif
                                    @if($bill->status !== 'belum_bayar')
                                        <button type="button" wire:click="openPaymentForm({{ $bill->id }})" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-bold transition cursor-pointer">
                                            Ubah
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($selectedTagihanId === $bill->id)
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3.5 mt-2 shadow-inner">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Nominal Tagihan (Rp)</label>
                                    <input type="number" wire:model.live="billNominal" class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-600 font-bold" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Nominal Cicilan Baru (Rp)</label>
                                    <input type="number" wire:model="paymentAmount" class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-600 font-bold" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Catatan Pembayaran</label>
                                    <input type="text" wire:model="paymentNote" placeholder="Contoh: Cash, Transfer BCA..." class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-600">
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2.5 border-t border-slate-200">
                                <button type="button" wire:click="resetTagihan" class="py-1.5 px-3 border border-slate-300 hover:bg-red-50 hover:text-red-700 hover:border-red-200 text-slate-500 text-xs font-bold rounded-lg transition-colors cursor-pointer text-left sm:text-center">
                                    Reset ke Belum Bayar
                                </button>
                                <div class="flex items-center justify-end space-x-2">
                                    <button type="button" wire:click="closePaymentForm" class="py-1.5 px-3 border border-slate-200 hover:bg-white text-slate-600 text-xs font-bold rounded-lg cursor-pointer transition">
                                        Batal
                                    </button>
                                    @if($selectedSantri && $selectedSantri->status === 'nonaktif' && $bill->status !== 'pulang')
                                        <button type="button" wire:click="markAsPulang" class="py-1.5 px-3 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-lg border border-red-200 transition cursor-pointer">
                                            Tandai Pulang
                                        </button>
                                    @endif
                                    <button type="button" wire:click="savePayment" wire:loading.attr="disabled" wire:target="savePayment" class="py-1.5 px-4 bg-emerald-800 hover:bg-emerald-950 text-white text-xs font-bold rounded-lg shadow-sm cursor-pointer transition disabled:opacity-50">
                                        <span wire:loading.remove wire:target="savePayment">Simpan Perubahan</span>
                                        <span wire:loading wire:target="savePayment" class="flex items-center gap-1"><x-lucide-loader-circle class="w-3 h-3 animate-spin" /> Menyimpan...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <p class="p-6 text-center text-xs text-slate-400 italic">Tidak ada data tagihan yang sesuai.</p>
            @endforelse
        </div>

        <x-slot:footer>
            <div></div>
            <button wire:click="closeModal" class="px-5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold shadow-sm cursor-pointer transition">
                Tutup Lembar
            </button>
        </x-slot:footer>
    </x-modal>

    <!-- Global Config Modal -->
    <x-modal :show="$isConfigModalOpen" title="Konfigurasi Tarif Default Keuangan" subtitle="Atur nominal default biaya pendidikan untuk tahun ajaran terpilih" maxWidth="max-w-md" closeAction="closeConfigModal">
        <form wire:submit.prevent="saveGlobalConfig" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Nominal Daftar Ulang (Rp)</label>
                <input wire:model="configDaftarUlang" type="number" min="0" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/10" required>
                @error('configDaftarUlang') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Nominal Syahriah Semester 1 (Rp)</label>
                <input wire:model="configSyahriahSem1" type="number" min="0" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/10" required>
                @error('configSyahriahSem1') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Nominal Syahriah Semester 2 (Rp)</label>
                <input wire:model="configSyahriahSem2" type="number" min="0" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/10" required>
                @error('configSyahriahSem2') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Nominal Majeg Makan Bulanan (Rp)</label>
                <input wire:model="configMajegMakan" type="number" min="0" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/10" required>
                @error('configMajegMakan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>



            <div class="flex items-center justify-end space-x-2.5 pt-3 border-t border-slate-100">
                <button type="button" wire:click="closeConfigModal" class="py-2 px-4 border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-bold rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" wire:loading.attr="disabled" class="py-2 px-5 bg-emerald-800 hover:bg-emerald-900 text-white text-xs font-bold rounded-xl transition shadow-md shadow-emerald-800/10 cursor-pointer disabled:opacity-50">
                    <span wire:loading.remove>Simpan Perubahan</span>
                    <span wire:loading class="flex items-center gap-1.5"><x-lucide-loader-circle class="w-3.5 h-3.5 animate-spin" /> Menyimpan...</span>
                </button>
            </div>
        </form>
    </x-modal>
</div>
