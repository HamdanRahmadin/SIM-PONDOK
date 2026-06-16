{{-- Dashboard Ustaz - Matches contoh UI: dashboard_sim_pondok/code.html --}}
<div class="space-y-4">

{{-- Hero Card --}}
<section class="bg-hero-gradient rounded-xl p-6 text-on-primary shadow-level-1 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl transform translate-x-8 -translate-y-8"></div>
    <div class="relative z-10">
        <h2 class="text-xl font-bold md:text-3xl mb-1">Assalamu'alaikum Ustaz</h2>
        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 text-white/90 text-sm">
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                {{ $formattedHijriDate }} H
            </span>
            <span class="hidden sm:inline opacity-50">•</span>
            <span>{{ \Carbon\Carbon::parse($currentDate)->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>
</section>

{{-- Quick Actions (Bento Grid) --}}
<section class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <a href="{{ route('ustaz.presensi_setoran') }}" class="bg-surface-container-lowest rounded-xl p-6 shadow-level-1 hover:bg-surface-container-low transition-colors duration-200 text-left border border-outline-variant/20 group flex flex-col justify-between h-32 relative overflow-hidden active:scale-[0.98] card-transition">
        <div class="absolute right-[-20px] bottom-[-20px] opacity-5 transform group-hover:scale-110 transition-transform duration-500">
            <span class="material-symbols-outlined text-[100px]">menu_book</span>
        </div>
        <div class="w-10 h-10 rounded-full bg-primary-container/20 text-primary flex items-center justify-center mb-4">
            <span class="material-symbols-outlined fill">menu_book</span>
        </div>
        <h3 class="font-semibold text-lg text-on-surface">Presensi Setoran</h3>
    </a>
    <a href="{{ route('ustaz.presensi_halaqoh') }}" class="bg-surface-container-lowest rounded-xl p-6 shadow-level-1 hover:bg-surface-container-low transition-colors duration-200 text-left border border-outline-variant/20 group flex flex-col justify-between h-32 relative overflow-hidden active:scale-[0.98] card-transition">
        <div class="absolute right-[-20px] bottom-[-20px] opacity-5 transform group-hover:scale-110 transition-transform duration-500">
            <span class="material-symbols-outlined text-[100px]">groups</span>
        </div>
        <div class="w-10 h-10 rounded-full bg-secondary-container/50 text-secondary flex items-center justify-center mb-4">
            <span class="material-symbols-outlined fill">groups</span>
        </div>
        <h3 class="font-semibold text-lg text-on-surface">Presensi Halaqoh</h3>
    </a>
</section>

{{-- Data Overview --}}
<section class="grid grid-cols-1 md:grid-cols-3 gap-4">
    {{-- Attendance Progress Card (Dual Session) --}}
    <div class="md:col-span-2 bg-surface-container-lowest rounded-xl p-6 shadow-level-1 border border-outline-variant/20">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="font-semibold text-lg text-on-surface mb-1">Presensi Hari Ini</h3>
                <p class="text-sm text-on-surface-variant">Total Santri Aktif: <span class="font-bold text-primary">{{ $totalActiveSantri }}</span></p>
            </div>
            <div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-primary">
                <span class="material-symbols-outlined">trending_up</span>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Sesi Pagi --}}
            <div class="space-y-2">
                <div class="flex justify-between items-center text-sm">
                    <span class="font-semibold text-on-surface flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base {{ $pagiIsFilled ? 'text-primary fill' : 'text-on-surface-variant' }}">light_mode</span>
                        Sesi Pagi
                    </span>
                    <span class="text-xs font-semibold {{ $pagiIsFilled ? 'text-primary' : 'text-on-surface-variant' }}">
                        {{ $pagiFilledCount }} / {{ $totalActiveSantri }} ({{ $pagiPercent }}%)
                    </span>
                </div>
                <div class="w-full bg-surface-variant/40 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full progress-bar-animated transition-all duration-500" style="background-color: #149459; width: {{ $pagiPercent }}%"></div>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="font-medium {{ $pagiIsFilled ? 'text-primary' : 'text-on-surface-variant/70' }}">
                        {{ $pagiIsFilled ? 'Sudah Diinput' : 'Belum Diinput' }}
                    </span>
                    <span class="text-on-surface-variant/50">Target: 07:30 WIB</span>
                </div>
            </div>

            {{-- Sesi Malam --}}
            <div class="space-y-2">
                <div class="flex justify-between items-center text-sm">
                    <span class="font-semibold text-on-surface flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base {{ $malamIsFilled ? 'text-primary fill' : 'text-on-surface-variant' }}">dark_mode</span>
                        Sesi Malam
                    </span>
                    <span class="text-xs font-semibold {{ $malamIsFilled ? 'text-primary' : 'text-on-surface-variant' }}">
                        {{ $malamFilledCount }} / {{ $totalActiveSantri }} ({{ $malamPercent }}%)
                    </span>
                </div>
                <div class="w-full bg-surface-variant/40 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full progress-bar-animated transition-all duration-500" style="background-color: #149459; width: {{ $malamPercent }}%"></div>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="font-medium {{ $malamIsFilled ? 'text-primary' : 'text-on-surface-variant/70' }}">
                        {{ $malamIsFilled ? 'Sudah Diinput' : 'Belum Diinput' }}
                    </span>
                    <span class="text-on-surface-variant/50">Target: 19:30 WIB</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Halaqoh Status Card --}}
    <div class="bg-surface-container-lowest rounded-xl p-6 shadow-level-1 border border-outline-variant/20 flex flex-col justify-center">
        <h3 class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-4">Status Halaqoh</h3>
        @if($isHalaqohDay)
            @if($isHalaqohFilled)
                <div class="flex items-center gap-3 text-primary">
                    <span class="material-symbols-outlined fill text-4xl">check_circle</span>
                    <div>
                        <p class="font-bold text-on-surface">Sudah Diisi</p>
                        <p class="text-xs text-on-surface-variant">Hari ini</p>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-4xl" style="color: #D97706;">warning</span>
                    <div>
                        <p class="font-bold text-on-surface">Belum Diisi</p>
                        <a href="{{ route('ustaz.presensi_halaqoh') }}" class="text-xs underline font-semibold text-primary">Isi Sekarang</a>
                    </div>
                </div>
            @endif
        @else
            <div class="flex items-center gap-3 text-on-surface-variant">
                <span class="material-symbols-outlined text-4xl">calendar_month</span>
                <div>
                    <p class="font-bold">Bukan Hari Ini</p>
                    <p class="text-xs text-on-surface-variant">Halaqoh setiap hari Rabu</p>
                </div>
            </div>
        @endif
    </div>
</section>

{{-- Recent Activity (Timeline) --}}
<section class="mb-8">
    <h3 class="font-semibold text-lg text-on-surface mb-4">Aktivitas Terakhir</h3>
    <div class="bg-surface-container-lowest rounded-xl shadow-level-1 border border-outline-variant/20 p-2">
        @forelse($recentActivities as $act)
        <div class="flex items-start p-4 hover:bg-surface-container-low transition-colors duration-200 rounded-lg group">
            <div class="relative mr-4 mt-1 flex flex-col items-center">
                @if($act['type'] === 'setoran')
                    <div class="w-8 h-8 rounded-full bg-primary-container/20 text-primary flex items-center justify-center z-10">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    </div>
                @else
                    <div class="w-8 h-8 rounded-full bg-secondary-container/50 text-secondary flex items-center justify-center z-10">
                        <span class="material-symbols-outlined text-[16px]">groups</span>
                    </div>
                @endif
                @if(!$loop->last)
                    <div class="w-px bg-outline-variant/30 absolute top-8 bottom-[-24px]"></div>
                @endif
            </div>
            <div class="flex-1">
                <div class="flex justify-between items-start mb-1">
                    <h4 class="font-semibold text-on-surface text-base">{{ $act['title'] }}</h4>
                    <span class="text-xs text-on-surface-variant">{{ $act['date'] }}</span>
                </div>
                <p class="text-sm text-primary font-medium mb-2">{{ $act['desc'] }}</p>
                <a href="{{ $act['type'] === 'setoran' ? route('ustaz.riwayat_presensi') : route('ustaz.riwayat_presensi') }}" class="text-xs text-primary hover:underline inline-flex items-center gap-1">
                    Lihat Detail <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                </a>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-on-surface-variant text-sm">
            Belum ada aktivitas yang tercatat.
        </div>
        @endforelse
    </div>
</section>
</div>
