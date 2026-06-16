<div>
    {{-- Summary Bento Grid --}}
    <section class="grid grid-cols-2 gap-2 mb-2">
        {{-- Header Card (Full Width) --}}
        <div class="bg-surface-container-lowest p-4 rounded-xl shadow-level-1 flex flex-col gap-1 col-span-2 border border-outline-variant/20">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Presensi Halaqoh</span>
                    <p class="font-bold text-2xl text-primary mt-1">Semua Santri</p>
                </div>
                <span class="bg-primary-container/20 text-primary text-xs font-semibold px-2 py-1 rounded-md">
                    {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d M Y') }}
                </span>
            </div>
            <div class="flex items-end gap-2 mt-2">
                <span class="text-4xl font-bold text-primary">{{ count($santris) }}</span>
                <span class="text-sm text-on-surface-variant mb-1">Total Santri Aktif</span>
            </div>
        </div>

        {{-- Stats: Hadir --}}
        <div class="bg-surface-container-lowest p-3 rounded-xl shadow-level-1 flex flex-col items-center justify-center border-l-4 border-l-primary">
            <span class="text-4xl font-bold text-primary leading-none">{{ collect($presensiData)->where('status', 'hadir')->count() }}</span>
            <span class="text-xs font-semibold text-on-surface-variant mt-1">Hadir</span>
        </div>

        {{-- Stats: Izin + Alfa --}}
        <div class="flex flex-col gap-2">
            <div class="bg-surface-container-lowest p-2 rounded-xl shadow-level-1 flex items-center justify-between border-l-4 border-l-secondary flex-1">
                <span class="text-xs font-semibold text-on-surface-variant">Izin</span>
                <span class="text-xl font-bold text-secondary">{{ collect($presensiData)->where('status', 'izin_sakit')->count() }}</span>
            </div>
            <div class="bg-surface-container-lowest p-2 rounded-xl shadow-level-1 flex items-center justify-between border-l-4 border-l-error flex-1">
                <span class="text-xs font-semibold text-on-surface-variant">Alfa</span>
                <span class="text-xl font-bold text-error">{{ collect($presensiData)->where('status', 'alfa')->count() }}</span>
            </div>
        </div>
    </section>

    {{-- Sticky Bulk Action & Search --}}
    <div class="sticky top-14 md:top-0 z-30 bg-surface/95 backdrop-blur-sm py-2 flex flex-col gap-2 -mx-4 md:-mx-6 px-4 md:px-6 border-b border-outline-variant/20 shadow-sm">
        @if($isLocked)
        <div class="flex items-center gap-2 bg-error-container/50 border border-error/30 text-on-error-container rounded-lg p-3 text-sm">
            <span class="material-symbols-outlined shrink-0">lock</span>
            <p>Presensi Halaqoh hanya bisa diisi setiap hari <strong>Rabu</strong>.</p>
        </div>
        @else
        <button wire:click="markAllHadir" type="button" class="w-full bg-primary-container text-on-primary-container font-semibold text-lg h-12 rounded-lg flex items-center justify-center gap-2 active:scale-[0.98] transition-transform shadow-level-1">
            <span class="material-symbols-outlined">done_all</span>
            Tandai Semua Hadir
        </button>
        @endif

        <div class="relative w-full">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            <input type="text" placeholder="Cari nama santri..." class="w-full h-11 pl-10 pr-4 bg-surface-container-lowest border border-outline-variant/40 rounded-lg text-sm text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors shadow-sm placeholder:text-on-surface-variant/60">
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session()->has('success'))
    <div class="flex items-center gap-2 bg-primary-container/30 border border-primary/20 text-primary px-4 py-3 rounded-xl text-sm">
        <span class="material-symbols-outlined text-lg">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- Student List --}}
    <section class="flex flex-col gap-2 mt-2" id="student-list">
        @forelse($santris as $santri)
        <div class="bg-surface-container-lowest p-3 rounded-xl shadow-level-1 flex flex-col gap-3 border border-outline-variant/10 card-transition" wire:key="halaqoh-{{ $santri->id }}">
            {{-- Student Info --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-primary font-semibold text-lg border border-outline-variant/20">
                    {{ strtoupper(substr($santri->nama_lengkap, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-base text-on-surface truncate">{{ $santri->nama_lengkap }}</h3>

                </div>
            </div>

            {{-- Segmented Control --}}
            <div class="flex gap-2 w-full">
                <div class="flex-1 relative">
                    <input class="peer sr-only segmented-radio radio-hadir" type="radio"
                           wire:model="presensiData.{{ $santri->id }}.status"
                           id="hadir_h{{ $santri->id }}" name="status_h{{ $santri->id }}"
                           value="hadir" @disabled($isLocked)>
                    <label class="flex items-center justify-center w-full h-10 rounded-lg border border-outline-variant/40 bg-surface text-on-surface-variant text-xs font-semibold cursor-pointer hover:bg-surface-variant/30 active:scale-95 transition-all"
                           for="hadir_h{{ $santri->id }}">Hadir</label>
                </div>
                <div class="flex-1 relative">
                    <input class="peer sr-only segmented-radio radio-izin" type="radio"
                           wire:model="presensiData.{{ $santri->id }}.status"
                           id="izin_h{{ $santri->id }}" name="status_h{{ $santri->id }}"
                           value="izin_sakit" @disabled($isLocked)>
                    <label class="flex items-center justify-center w-full h-10 rounded-lg border border-outline-variant/40 bg-surface text-on-surface-variant text-xs font-semibold cursor-pointer hover:bg-surface-variant/30 active:scale-95 transition-all"
                           for="izin_h{{ $santri->id }}">Izin</label>
                </div>
                <div class="flex-1 relative">
                    <input class="peer sr-only segmented-radio radio-alfa" type="radio"
                           wire:model="presensiData.{{ $santri->id }}.status"
                           id="alfa_h{{ $santri->id }}" name="status_h{{ $santri->id }}"
                           value="alfa" @disabled($isLocked)>
                    <label class="flex items-center justify-center w-full h-10 rounded-lg border border-outline-variant/40 bg-surface text-on-surface-variant text-xs font-semibold cursor-pointer hover:bg-surface-variant/30 active:scale-95 transition-all"
                           for="alfa_h{{ $santri->id }}">Alfa</label>
                </div>
            </div>

            {{-- Optional Note --}}
            <input type="text"
                   wire:model="presensiData.{{ $santri->id }}.catatan"
                   placeholder="Catatan (opsional)..."
                   class="w-full bg-surface border border-outline-variant/50 text-sm rounded-lg px-3 py-2 focus:ring-1 focus:ring-primary focus:border-primary transition-all placeholder:text-on-surface-variant/50"
                   @disabled($isLocked)>
        </div>
        @empty
        <div class="text-center py-12 bg-surface-container-lowest rounded-xl border border-outline-variant/20">
            <span class="material-symbols-outlined text-4xl text-on-surface-variant/50">group_off</span>
            <p class="text-on-surface-variant text-sm mt-2">Tidak ada santri aktif yang terdaftar.</p>
        </div>
        @endforelse
    </section>

    <div class="h-24"></div>

    {{-- Fixed Bottom Action Bar --}}
    <div class="fixed bottom-0 left-0 md:left-72 w-full md:w-[calc(100%-18rem)] bg-surface flex gap-3 p-4 pb-safe shadow-[0_-4px_16px_rgba(0,0,0,0.05)] border-t border-outline-variant/10 z-50 rounded-t-xl">
        <div class="flex flex-col items-center justify-center text-on-surface-variant h-12 px-3 min-w-[80px] border-r border-outline-variant/20">
            <span class="text-xs font-semibold">{{ collect($presensiData)->where('status', 'hadir')->count() }} / {{ count($santris) }}</span>
            <span class="text-[10px] opacity-80">Hadir</span>
        </div>
        <button wire:click="savePresensi" type="button" @disabled($isLocked)
                class="flex-1 flex items-center justify-center text-on-primary h-12 rounded-lg font-semibold text-base gap-2 hover:opacity-90 active:scale-[0.98] transition-all shadow-[0_4px_12px_rgba(0,106,61,0.2)] disabled:opacity-50 disabled:cursor-not-allowed"
                style="background-color: #006a3d;">
            <span class="material-symbols-outlined">save</span>
            Simpan Presensi
        </button>
    </div>
</div>
