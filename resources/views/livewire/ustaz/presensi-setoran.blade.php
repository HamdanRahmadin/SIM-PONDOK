<div>
    {{-- Sticky Header / Context Bar --}}
    <div class="sticky top-14 md:top-0 z-30 bg-background/95 backdrop-blur py-3 -mx-4 md:-mx-6 px-4 md:px-6 shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] border-b border-surface-variant/50">
        <div class="flex items-center justify-between mb-3">
            <div class="flex flex-col">
                <span class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Tanggal</span>
                <span class="font-semibold text-lg text-on-surface">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <select wire:model.live="sesi" class="bg-surface-container hover:bg-surface-container-high px-3 py-2 rounded-lg border border-outline-variant/50 text-sm font-semibold text-primary transition-colors focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="pagi">Pagi</option>
                    <option value="malam">Malam</option>
                </select>
                <select wire:model.live="kelas_id" class="bg-surface-container hover:bg-surface-container-high px-3 py-2 rounded-lg border border-outline-variant/50 text-sm font-semibold text-primary transition-colors focus:outline-none focus:ring-1 focus:ring-primary">
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button wire:click="markAllHadir" type="button" @disabled($isLocked) class="w-full bg-surface-container-low border border-primary/20 text-primary hover:bg-surface-container hover:border-primary/30 font-semibold rounded-lg h-11 flex items-center justify-center gap-2 transition-all active:scale-[0.98] shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
            <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' 1;">done_all</span>
            Tandai Semua Hadir
        </button>
    </div>

    {{-- Lock Notice --}}
    @if($isLocked)
    <div class="flex items-start gap-3 bg-error-container/50 border border-error/30 text-on-error-container rounded-xl p-4 text-sm">
        <span class="material-symbols-outlined shrink-0 text-error">lock</span>
        <p>Form presensi terkunci untuk sesi ini sesuai jadwal operasional atau hari libur massal.</p>
    </div>
    @endif

    {{-- Success Flash --}}
    @if(session()->has('success'))
    <div class="flex items-center gap-2 bg-primary-container/30 border border-primary/20 text-primary px-4 py-3 rounded-xl text-sm">
        <span class="material-symbols-outlined text-lg">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- Student List --}}
    <div class="flex flex-col gap-2 mt-2" id="student-list">
        @forelse($santris as $santri)
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-xl p-3 flex flex-col gap-3 shadow-level-1 card-transition" wire:key="santri-{{ $santri->id }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-primary font-semibold text-lg">
                        {{ strtoupper(substr($santri->nama_lengkap, 0, 1)) }}
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold text-sm text-on-surface">{{ $santri->nama_lengkap }}</span>
                        <span class="text-xs text-on-surface-variant">NIS: {{ $santri->nis ?? '-' }}</span>
                    </div>
                </div>
                <button type="button"
                        x-data
                        @click="$el.closest('.card-transition').querySelector('.note-field').classList.toggle('hidden')"
                        class="w-11 h-11 flex items-center justify-center text-on-surface-variant hover:bg-surface-variant/50 rounded-full transition-colors active:scale-95">
                    <span class="material-symbols-outlined text-xl">edit_note</span>
                </button>
            </div>

            {{-- Segmented Control --}}
            <div class="flex items-center w-full bg-surface-container-low p-1 rounded-lg border border-outline-variant/30">
                <div class="flex-1 text-center">
                    <input class="hidden segmented-radio radio-hadir" type="radio" wire:model="presensiData.{{ $santri->id }}.status" id="hadir-{{ $santri->id }}" name="status-{{ $santri->id }}" value="hadir" @disabled($isLocked)>
                    <label class="block w-full py-2 px-1 text-sm font-semibold rounded-md cursor-pointer transition-all border border-transparent text-on-surface-variant hover:bg-surface-variant/50" for="hadir-{{ $santri->id }}">Hadir</label>
                </div>
                <div class="flex-1 text-center">
                    <input class="hidden segmented-radio radio-izin" type="radio" wire:model="presensiData.{{ $santri->id }}.status" id="izin-{{ $santri->id }}" name="status-{{ $santri->id }}" value="izin_sakit" @disabled($isLocked)>
                    <label class="block w-full py-2 px-1 text-sm font-semibold rounded-md cursor-pointer transition-all border border-transparent text-on-surface-variant hover:bg-surface-variant/50" for="izin-{{ $santri->id }}">Izin</label>
                </div>
                <div class="flex-1 text-center">
                    <input class="hidden segmented-radio radio-alfa" type="radio" wire:model="presensiData.{{ $santri->id }}.status" id="alfa-{{ $santri->id }}" name="status-{{ $santri->id }}" value="alfa" @disabled($isLocked)>
                    <label class="block w-full py-2 px-1 text-sm font-semibold rounded-md cursor-pointer transition-all border border-transparent text-on-surface-variant hover:bg-surface-variant/50" for="alfa-{{ $santri->id }}">Alfa</label>
                </div>
            </div>

            {{-- Note Field (Hidden by default) --}}
            <div class="note-field hidden w-full pt-1">
                <input type="text" wire:model="presensiData.{{ $santri->id }}.catatan" placeholder="Tambah catatan (opsional)..." class="w-full bg-surface border border-outline-variant/50 text-sm rounded-lg px-3 py-2 focus:ring-1 focus:ring-primary focus:border-primary transition-all" @disabled($isLocked)>
            </div>
        </div>
        @empty
        <div class="text-center py-12 bg-surface-container-lowest rounded-xl border border-outline-variant/20">
            <span class="material-symbols-outlined text-4xl text-on-surface-variant/50">group_off</span>
            <p class="text-on-surface-variant text-sm mt-2">Tidak ada santri aktif di kelas ini.</p>
        </div>
        @endforelse
    </div>

    {{-- Spacer for fixed bottom bar --}}
    <div class="h-24"></div>

    {{-- Fixed Bottom Action Bar --}}
    <nav class="fixed bottom-0 left-0 md:left-72 w-full md:w-[calc(100%-18rem)] z-50 rounded-t-xl bg-surface/95 backdrop-blur shadow-[0_-4px_16px_rgba(0,0,0,0.08)] border-t border-outline-variant/20 pb-safe px-4 pt-3 pb-3">
        <div class="max-w-[800px] mx-auto flex items-center justify-between gap-4">
            <div class="flex flex-col items-center justify-center px-2">
                <span class="material-symbols-outlined text-primary text-xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                <span class="text-xs font-semibold text-primary mt-1">{{ collect($presensiData)->where('status', 'hadir')->count() }} / {{ count($santris) }} Selesai</span>
            </div>
            <button wire:click="savePresensi" type="button" @disabled($isLocked) class="flex-1 text-white h-12 rounded-xl flex items-center justify-center gap-2 font-semibold text-base transition-transform active:scale-[0.98] shadow-md disabled:opacity-50 disabled:cursor-not-allowed" style="background-color: #149459;">
                <span class="material-symbols-outlined">save</span>
                Simpan Presensi
            </button>
        </div>
    </nav>
</div>
