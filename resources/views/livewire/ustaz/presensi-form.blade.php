<div class="max-w-md mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-2">
        <a href="{{ route('ustaz.dashboard') }}" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-600 transition">
            ⬅️
        </a>
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Presensi Harian</h1>
        </div>
    </div>

    <!-- Filters Box -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <!-- Kelas Selection -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kelas</label>
                <select wire:model.live="selectedKelasId" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-2 font-semibold focus:outline-none focus:border-emerald-600 cursor-pointer">
                    <option value="0" disabled>Pilih Kelas</option>
                    @foreach($kelases as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Picker -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal</label>
                <input wire:model.live="selectedDate" type="date" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 font-semibold focus:outline-none focus:border-emerald-600 cursor-pointer">
            </div>
        </div>

        <!-- Session Toggle -->
        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sesi Presensi</label>
            <div class="grid grid-cols-2 gap-2 mt-1">
                <button wire:click="$set('selectedSesi', 'pagi')" 
                        class="py-2.5 text-xs font-bold rounded-xl border transition duration-150 cursor-pointer
                            {{ $selectedSesi === 'pagi' ? 'bg-emerald-800 text-white border-emerald-950 shadow-md shadow-emerald-950/20' : 'bg-slate-50 text-slate-600 border-slate-200' }}">
                    🌅 Pagi
                </button>
                <button wire:click="$set('selectedSesi', 'malam')" 
                        class="py-2.5 text-xs font-bold rounded-xl border transition duration-150 cursor-pointer
                            {{ $selectedSesi === 'malam' ? 'bg-emerald-800 text-white border-emerald-950 shadow-md shadow-emerald-950/20' : 'bg-slate-50 text-slate-600 border-slate-200' }}">
                    🌌 Malam
                </button>
            </div>
        </div>
    </div>

    <!-- Locked Alert Banner -->
    @if($isLocked)
        <div class="bg-red-50 border border-red-100 p-4 rounded-2xl flex items-start gap-2.5 text-red-900 text-xs font-medium">
            <span class="text-lg">🔒</span>
            <div>
                <p class="font-bold text-red-950">Form Presensi Terkunci</p>
                <p class="mt-0.5 opacity-85 leading-relaxed">{{ $lockReason }}</p>
            </div>
        </div>
    @endif

    <!-- Form Content -->
    @if($selectedKelasId > 0 && !$isLocked)
        <!-- Bulk Action Button -->
        <button wire:click="setAllPresent" 
                class="w-full bg-emerald-800 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-2xl shadow-md shadow-emerald-800/10 transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer">
            <span>✅</span> <span>Set Semua Hadir (Hadir Massal)</span>
        </button>

        <!-- Santri Rows List -->
        <div class="space-y-4">
            @forelse($santris as $santri)
                <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm space-y-2.5">
                    <!-- Name & Status -->
                    <div class="flex flex-col space-y-2">
                        <span class="font-bold text-slate-800 text-sm leading-tight">{{ $santri->nama_lengkap }}</span>
                        
                        <!-- Touchable Status Button Grid -->
                        <div class="grid grid-cols-4 gap-1.5">
                            <!-- Hadir -->
                            <button wire:click="changeStatus({{ $santri->id }}, 'hadir')" 
                                    class="py-2 rounded-lg text-[10px] font-extrabold uppercase border transition duration-100 cursor-pointer
                                        {{ ($statuses[$santri->id] ?? null) === 'hadir' ? 'bg-emerald-600 text-white border-emerald-700 font-black' : 'bg-slate-50 text-slate-500 border-slate-200' }}">
                                Hadir
                            </button>
                            <!-- Alfa -->
                            <button wire:click="changeStatus({{ $santri->id }}, 'alfa')" 
                                    class="py-2 rounded-lg text-[10px] font-extrabold uppercase border transition duration-100 cursor-pointer
                                        {{ ($statuses[$santri->id] ?? null) === 'alfa' ? 'bg-red-600 text-white border-red-700 font-black' : 'bg-slate-50 text-slate-500 border-slate-200' }}">
                                Alfa
                            </button>
                            <!-- Izin -->
                            <button wire:click="changeStatus({{ $santri->id }}, 'izin')" 
                                    class="py-2 rounded-lg text-[10px] font-extrabold uppercase border transition duration-100 cursor-pointer
                                        {{ ($statuses[$santri->id] ?? null) === 'izin' ? 'bg-amber-500 text-white border-amber-600 font-black' : 'bg-slate-50 text-slate-500 border-slate-200' }}">
                                Izin
                            </button>
                            <!-- Sakit -->
                            <button wire:click="changeStatus({{ $santri->id }}, 'sakit')" 
                                    class="py-2 rounded-lg text-[10px] font-extrabold uppercase border transition duration-100 cursor-pointer
                                        {{ ($statuses[$santri->id] ?? null) === 'sakit' ? 'bg-indigo-650 text-white border-indigo-700 font-black' : 'bg-slate-50 text-slate-500 border-slate-200' }}">
                                Sakit
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
    @elseif($selectedKelasId > 0 && $isLocked)
        <!-- Disabled Rows List when Locked -->
        <div class="space-y-4 opacity-65 pointer-events-none">
            @foreach($santris as $santri)
                <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-800 text-sm">{{ $santri->nama_lengkap }}</span>
                        <span class="text-xs px-2.5 py-0.5 rounded bg-slate-100 border text-slate-500 font-bold capitalize">
                            {{ $statuses[$santri->id] ?? 'Belum Absen' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center p-8 bg-white border border-slate-200/80 rounded-2xl text-slate-400 italic text-xs">
            Pilih kelas terlebih dahulu untuk mengisi presensi.
        </div>
    @endif
</div>
