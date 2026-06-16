<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800 font-heading">Dasbor Admin</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola data master pondok, pantau aktivitas log, dan sesuaikan hilal kalender.</p>
        </div>
    </div>
 
    <!-- Success/Error Alerts -->
    <x-alert type="success" />
    <x-alert type="error" />
 
    <!-- Bento Header Stats (Grid 4 Kolom) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-emerald-50 rounded-xl text-emerald-800">
                <x-lucide-graduation-cap class="w-5 h-5" />
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Santri Aktif</p>
                <p class="text-base font-extrabold text-slate-800 leading-tight">{{ $santriAktifCount }}</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-rose-50 rounded-xl text-rose-800">
                <x-lucide-user class="w-5 h-5" />
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Santri Nonaktif</p>
                <p class="text-base font-extrabold text-slate-800 leading-tight">{{ $santriNonaktifCount }}</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-emerald-50 rounded-xl text-emerald-800">
                <x-lucide-school class="w-5 h-5" />
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kelas Aktif</p>
                <p class="text-base font-extrabold text-slate-800 leading-tight">{{ $kelasCount }}</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-emerald-50 rounded-xl text-emerald-800">
                <x-lucide-door-open class="w-5 h-5" />
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kamar Santri</p>
                <p class="text-base font-extrabold text-slate-800 leading-tight">{{ $kamarCount }}</p>
            </div>
        </div>
    </div>
 
    <!-- Bento Split Grid (60% Chart / 40% Hijri Hilal) -->
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-5">
        
        <!-- Chart Kehadiran (60% width = 6 cols) -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm lg:col-span-6 flex flex-col justify-between min-h-[300px]">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Simulasi Kehadiran Sesi Pagi vs Malam</h3>
                <p class="text-xs text-slate-400 mt-0.5">Grafik batang rekap persentase kehadiran 5 hari masehi terakhir.</p>
            </div>
            
            <div class="h-44 flex items-end justify-between px-2 pt-4 relative">
                <!-- Background lines -->
                <div class="absolute left-0 right-0 top-4 bottom-0 flex flex-col justify-between pointer-events-none">
                    <div class="border-b border-slate-100 w-full h-0"></div>
                    <div class="border-b border-slate-100 w-full h-0"></div>
                    <div class="border-b border-slate-100 w-full h-0"></div>
                </div>
 
                <!-- Bar Loop -->
                @foreach($graphData as $g)
                    <div class="flex flex-col items-center space-y-2 z-10 w-14">
                        <div class="flex space-x-1 items-end h-32">
                            <!-- Sesi Pagi Bar -->
                            <div class="w-3.5 bg-emerald-600 rounded-t transition-all duration-500" style="height: {{ $g['pagi'] }}%" title="Pagi: {{ $g['pagi'] }}%"></div>
                            <!-- Sesi Malam Bar -->
                            <div class="w-3.5 bg-amber-500 rounded-t transition-all duration-500" style="height: {{ $g['malam'] }}%" title="Malam: {{ $g['malam'] }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-slate-500 capitalize whitespace-nowrap">{{ substr($g['day_name'], 0, 5) }}</span>
                    </div>
                @endforeach
            </div>
 
            <div class="mt-4 flex items-center justify-center space-x-6 text-xs font-bold border-t border-slate-100 pt-3">
                <span class="flex items-center"><span class="w-2 h-2 bg-emerald-600 rounded-sm mr-1.5"></span> Sesi Pagi (05:00 - 08:00)</span>
                <span class="flex items-center"><span class="w-2 h-2 bg-amber-500 rounded-sm mr-1.5"></span> Sesi Malam (18:00 - 22:00)</span>
            </div>
        </div>
 
        <!-- Kalender Hijriah & Koreksi Hilal (40% width = 4 cols) -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm lg:col-span-4 flex flex-col justify-between min-h-[300px]">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5 mb-3">
                    <h3 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <x-lucide-calendar class="w-3.5 h-3.5" /> Kalender Hijriah
                    </h3>
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 text-xs font-black rounded-full border border-emerald-100 uppercase tracking-wider">Umm Al-Qura</span>
                </div>
 
                <div class="text-center py-4 bg-slate-50 rounded-xl border border-slate-100 shadow-inner">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest leading-none">Hari Ini (Hijriah)</p>
                    <h4 class="text-base font-extrabold text-slate-800 mt-2 tracking-tight">{{ $formattedHijriDate }} H</h4>
                    <p class="text-xs text-slate-400 mt-1 font-medium">(Masehi: {{ date('d-F-Y') }})</p>
                </div>
            </div>
 
            <!-- Koreksi Hilal Incremental -->
            <div class="pt-3 border-t border-slate-100">
                <label class="text-xs font-bold text-slate-400 block mb-2 uppercase tracking-wider">Koreksi Hilal (Offset Hari)</label>
                <div class="flex items-center justify-between bg-slate-50 rounded-lg p-1.5 border border-slate-100">
                    <button wire:click="adjustHilal(-1)" class="w-7 h-7 flex items-center justify-center bg-white border border-slate-200 rounded-md hover:bg-slate-100 text-slate-700 shadow-sm active:scale-95 transition font-extrabold text-xs cursor-pointer">
                        -
                    </button>
                    <span class="text-xs font-bold text-slate-800">{{ $hilalOffset > 0 ? '+' : '' }}{{ $hilalOffset }} Hari (Offset)</span>
                    <button wire:click="adjustHilal(1)" class="w-7 h-7 flex items-center justify-center bg-white border border-slate-200 rounded-md hover:bg-slate-100 text-slate-700 shadow-sm active:scale-95 transition font-extrabold text-xs cursor-pointer">
                        +
                    </button>
                </div>
                <p class="text-xs text-slate-450 mt-2 italic leading-tight">
                    *Hanya mempengaruhi konversi tanggal secara prospektif, data historis tetap aman.
                </p>
            </div>
        </div>
    </div>
 
    <!-- Removed Audit Trail & Pintasan Admin as requested -->
 
    <!-- ========================================== -->
    <!-- MODALS                                     -->
    <!-- ========================================== -->
 
    <!-- Modal Kenaikan Kelas Massal -->
    <x-modal :show="$isKenaikanModalOpen" title="Proses Kenaikan Kelas Massal" subtitle="Pindahkan rombongan santri ke kelas baru secara serentak." maxWidth="max-w-xl" closeAction="closeKenaikanModal">
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kelas Asal</label>
                    <select wire:model.live="kelasAsalId" class="w-full border border-slate-200 rounded-lg p-2 text-xs font-bold text-slate-700 cursor-pointer focus:outline-none focus:border-emerald-600">
                        <option value="0">Pilih Kelas Asal</option>
                        @foreach($kelases as $kls)
                            <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kelas Tujuan Kenaikan</label>
                    <select wire:model="kelasTujuanId" class="w-full border border-slate-200 rounded-lg p-2 text-xs font-bold text-slate-700 cursor-pointer focus:outline-none focus:border-emerald-600">
                        <option value="0">Pilih Kelas Tujuan</option>
                        @foreach($kelases as $kls)
                            <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                        @endforeach
                        <option value="-1">Lulus (Arsipkan Alumni)</option>
                    </select>
                </div>
            </div>
            @if($kelasAsalId > 0)
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Pilih Santri yang Naik Kelas</label>
                    <div class="border border-slate-200/80 rounded-xl overflow-hidden max-h-56 overflow-y-auto scrollbar-none bg-slate-50/50 p-2">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-white border-b border-slate-200 text-slate-500 font-bold uppercase text-xs tracking-wider">
                                <tr>
                                    <th class="py-2 px-3 w-10 text-center">
                                        <input type="checkbox" 
                                               onclick="let boxs=document.querySelectorAll('.chk-stud'); boxs.forEach(b => b.checked = this.checked); @this.set('selectedSantriIds', this.checked ? Array.from(boxs).map(b => b.value) : [])" 
                                               class="rounded text-emerald-800 focus:ring-emerald-700 w-3.5 h-3.5">
                                    </th>
                                    <th class="py-2 px-3">Nama Lengkap</th>
                                    <th class="py-2 px-3">Kamar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-650 bg-white">
                                @forelse($this->getKenaikanStudents() as $student)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-2 px-3 text-center">
                                            <input type="checkbox" 
                                                   wire:model="selectedSantriIds" 
                                                   value="{{ $student->id }}" 
                                                   class="chk-stud rounded text-emerald-800 focus:ring-emerald-700 w-3.5 h-3.5">
                                        </td>
                                        <td class="py-2 px-3 font-bold text-slate-800">{{ $student->nama_lengkap }}</td>
                                        <td class="py-2 px-3 text-slate-500">{{ $student->kamar ? $student->kamar->nama_kamar : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-6 text-center text-slate-400 italic">Tidak ada santri aktif di kelas ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="p-8 text-center bg-slate-50 border border-slate-200 border-dashed rounded-xl text-xs text-slate-400 italic">
                    Silakan pilih Kelas Asal terlebih dahulu.
                </div>
            @endif
        </div>
        <x-slot:footer>
            <span class="text-xs font-bold text-slate-500">Terpilih: {{ count($selectedSantriIds) }} santri</span>
            <div class="flex items-center space-x-2">
                <button wire:click="closeKenaikanModal" class="py-2 px-4 border border-slate-200 text-slate-600 hover:bg-slate-100 rounded-lg text-xs font-semibold cursor-pointer">
                    Batal
                </button>
                <button wire:click="triggerKenaikanProcess" wire:loading.attr="disabled" wire:target="triggerKenaikanProcess" class="py-2 px-5 bg-emerald-800 hover:bg-emerald-950 text-white rounded-lg text-xs font-bold shadow-md cursor-pointer disabled:opacity-50">
                    Proses Kenaikan Kelas
                </button>
            </div>
        </x-slot:footer>
    </x-modal>
 
    <!-- Konfirmasi Kenaikan Kelas -->
    <x-confirm-dialog
        :show="$isConfirmModalOpen"
        title="Konfirmasi Kenaikan Kelas"
        :message="$confirmMessage"
        confirmText="Ya, Pindahkan"
        cancelText="Kembali"
        confirmAction="confirmKenaikanMassal"
        cancelAction="closeConfirmModal" />

    <!-- Modal Ekspor Presensi -->
    <form wire:submit.prevent="exportPresensi">
        <x-modal :show="$isExportPresensiModalOpen" title="Ekspor Laporan Kehadiran" subtitle="Unduh data kehadiran bulanan kelas dalam format Excel (.xlsx)" maxWidth="max-w-md" closeAction="closeExportPresensiModal">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pilih Kelas</label>
                    <select wire:model="exportKelasId" class="w-full border border-slate-200 rounded-xl p-3 text-xs font-bold text-slate-700 cursor-pointer focus:outline-none focus:border-emerald-600" required>
                        @foreach($kelases as $kls)
                            <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                        @endforeach
                    </select>
                    @error('exportKelasId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Bulan Hijriah</label>
                        <select wire:model="exportBulanHijri" class="w-full border border-slate-200 rounded-xl p-3 text-xs font-bold text-slate-700 cursor-pointer focus:outline-none focus:border-emerald-600" required>
                            <option value="1">1. Muharram</option>
                            <option value="2">2. Safar</option>
                            <option value="3">3. Rabi'ul Awwal</option>
                            <option value="4">4. Rabi'ul Akhir</option>
                            <option value="5">5. Jumadal Ula</option>
                            <option value="6">6. Jumadal Akhirah</option>
                            <option value="7">7. Rajab</option>
                            <option value="8">8. Sya'ban</option>
                            <option value="9">9. Ramadhan</option>
                            <option value="10">10. Syawwal</option>
                            <option value="11">11. Dzulqa'dah</option>
                            <option value="12">12. Dzulhijjah</option>
                        </select>
                        @error('exportBulanHijri') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Tahun Hijriah</label>
                        <input type="number" wire:model="exportTahunHijri" class="w-full border border-slate-200 rounded-xl p-3 text-xs font-bold text-slate-700 focus:outline-none focus:border-emerald-600" required>
                        @error('exportTahunHijri') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <x-slot:footer>
                <div></div>
                <div class="flex items-center space-x-2">
                    <button type="button" wire:click="closeExportPresensiModal" class="py-2.5 px-4 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-xs font-semibold cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="exportPresensi" class="py-2.5 px-5 bg-emerald-800 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md cursor-pointer flex items-center gap-1.5 disabled:opacity-50">
                        <x-lucide-download class="w-4 h-4" /> <span>Download Excel</span>
                    </button>
                </div>
            </x-slot:footer>
        </x-modal>
    </form>
</div>
