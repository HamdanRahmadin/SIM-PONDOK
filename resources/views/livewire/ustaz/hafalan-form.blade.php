<div class="max-w-md mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-2">
        <a href="{{ route('ustaz.dashboard') }}" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-600 transition">
            ⬅️
        </a>
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Capaian Hafalan Bulanan</h1>
            <p class="text-xs text-slate-400">Pencatatan hafalan santri di akhir bulan Hijriah.</p>
        </div>
    </div>

    <!-- Active Month Indicator -->
    <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl flex items-center justify-between text-emerald-950">
        <div>
            <span class="text-xs font-semibold text-emerald-800 uppercase tracking-wider block">Bulan Pengisian Aktif</span>
            <span class="text-sm font-bold text-emerald-950">{{ $monthName }} {{ $currentYear }} H</span>
        </div>
        <span class="text-2xl">📖</span>
    </div>

    <!-- Filters Box -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pilih Kelas</label>
        <select wire:model.live="selectedKelasId" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-semibold focus:outline-none focus:border-emerald-600">
            <option value="0" disabled>Pilih Kelas</option>
            @foreach($kelases as $kelas)
                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
            @endforeach
        </select>
    </div>

    <!-- Santri Rows List -->
    @if($selectedKelasId > 0)
        <div class="space-y-4">
            @forelse($santris as $santri)
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                    <!-- Name -->
                    <div>
                        <h3 class="font-extrabold text-slate-850 text-sm leading-tight">{{ $santri->nama_lengkap }}</h3>
                    </div>

                    <!-- History Timeline Widget (Vertical Tracking) -->
                    <div class="space-y-2.5">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Riwayat Hafalan Sebelumnya</span>
                        
                        @if(!empty($histories[$santri->id]))
                            <div class="border-l-2 border-emerald-100 pl-3.5 ml-1 space-y-3">
                                @foreach($histories[$santri->id] as $history)
                                    <div class="relative">
                                        <!-- Timeline dot -->
                                        <div class="absolute -left-[20px] top-1 w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white"></div>
                                        <p class="text-[10px] font-bold text-emerald-800 tracking-wide uppercase">{{ $history['label'] }}</p>
                                        <p class="text-xs text-slate-600 font-medium mt-0.5">{{ $history['text'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic bg-slate-50 p-2.5 rounded-xl border border-slate-200/30">Belum ada riwayat hafalan bulan sebelumnya.</p>
                        @endif
                    </div>

                    <!-- Current Month Input Form -->
                    <div class="pt-4 border-t border-slate-100 space-y-2">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Input Hafalan Bulan Ini ({{ $monthName }})</label>
                        <div class="flex flex-col space-y-2">
                            <textarea wire:model="inputs.{{ $santri->id }}" rows="2" placeholder="Masukkan capaian hafalan (contoh: Surah An-Naba' ayat 1-40)..." 
                                      class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-emerald-600 transition"></textarea>
                            <button wire:click="saveHafalan({{ $santri->id }})" 
                                    class="w-full bg-emerald-800 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-xl text-xs transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer">
                                💾 Simpan Hafalan Bulan Ini
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center p-8 bg-white border border-slate-200/80 rounded-2xl text-slate-400 italic text-xs">
                    Tidak ada santri aktif di kelas ini.
                </div>
            @endforelse
        </div>
    @else
        <div class="text-center p-8 bg-white border border-slate-200/80 rounded-2xl text-slate-400 italic text-xs">
            Pilih kelas terlebih dahulu untuk mengisi capaian hafalan.
        </div>
    @endif
</div>
