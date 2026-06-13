<div class="max-w-md mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Dashboard Ustaz</h1>
        <p class="text-xs text-slate-400 mt-0.5">Operasional presensi dan setoran kelas asuhan.</p>
    </div>

    <!-- Live Progress Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
        <div>
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Absensi Hari Ini</h3>
            <h2 class="text-lg font-bold text-slate-800 capitalize mt-0.5">Sesi {{ $currentSesi }}</h2>
            <p class="text-[10px] text-slate-500 font-mono mt-0.5">
                {{ date('d-m-Y') }} | {{ \App\Helpers\HijriHelper::gregorianToHijri($currentDate)['formatted'] }}
            </p>
        </div>

        <!-- Progress Bar Meter -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between text-xs font-bold">
                <span class="text-slate-600">Progres Pengisian:</span>
                <span class="{{ $progressPercent === 100 ? 'text-emerald-800' : ($isLate ? 'text-red-700' : 'text-emerald-700') }}">
                    {{ $progressPercent }}% ({{ $filledCount }}/{{ $totalActive }})
                </span>
            </div>
            
            <div class="h-5 w-full bg-slate-100 rounded-xl overflow-hidden flex border border-slate-200/50 p-0.5">
                <div class="rounded-lg transition-all duration-500 flex items-center justify-end pr-2 text-[9px] font-bold text-white
                    {{ $progressPercent === 100 ? 'bg-emerald-600' : ($isLate ? 'bg-red-500 animate-pulse' : 'bg-emerald-700') }}
                " style="width: {{ $progressPercent }}%">
                    @if($progressPercent > 10) {{ $progressPercent }}% @endif
                </div>
            </div>
        </div>

        <!-- Warning / Status Note -->
        <div class="p-3.5 rounded-xl text-xs font-medium border
            {{ $progressPercent === 100 ? 'bg-emerald-50 text-emerald-800 border-emerald-100' : ($isLate ? 'bg-red-50 text-red-800 border-red-100' : 'bg-slate-50 text-slate-600 border-slate-200') }}
        ">
            @if($progressPercent === 100)
                🎉 <strong>Aman!</strong> Seluruh santri sudah terabsen untuk sesi ini.
            @elseif($isLate)
                ⚠️ <strong>Lewat Sesi!</strong> Waktu sesi normal telah habis. Harap lakukan <strong>Absen Susulan</strong> sesegera mungkin.
            @else
                📝 <strong>Sedang Berlangsung.</strong> Sesi absensi masih aktif. Lakukan pengisian data presensi kelas.
            @endif
        </div>
    </div>

    <!-- Quick Navigation (Mobile-First Touch Targets) -->
    <div class="grid grid-cols-2 gap-4">
        <a href="{{ route('ustaz.presensi') }}" class="bg-emerald-800 hover:bg-emerald-700 text-white p-4 rounded-2xl shadow-md border border-emerald-900 flex flex-col items-center justify-center text-center space-y-2 cursor-pointer transition duration-200">
            <span class="text-2xl">📝</span>
            <span class="text-xs font-bold uppercase tracking-wider">Presensi Harian</span>
        </a>
        <a href="{{ route('ustaz.hafalan') }}" class="bg-emerald-950 hover:bg-emerald-900 text-white p-4 rounded-2xl shadow-md border border-emerald-900 flex flex-col items-center justify-center text-center space-y-2 cursor-pointer transition duration-200">
            <span class="text-2xl">📖</span>
            <span class="text-xs font-bold uppercase tracking-wider">Hafalan Bulanan</span>
        </a>
    </div>

    <!-- Early Warning System (EWS) Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center space-x-1.5 border-b border-slate-100 pb-3">
            <span class="text-xl">⚠️</span>
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Early Warning System (EWS)</h3>
                <p class="text-[10px] text-slate-400">Santri yang tidak hadir (Alfa) 3 kali berturut-turut.</p>
            </div>
        </div>

        <div class="space-y-2.5">
            @forelse($ewsList as $item)
                <div class="p-3 bg-red-50/60 border border-red-100 rounded-xl flex items-start justify-between">
                    <div>
                        <h4 class="font-bold text-red-950 text-sm">{{ $item['santri']->nama_lengkap }}</h4>
                        <p class="text-[10px] text-red-800/80 font-medium mt-0.5">Kelas: {{ $item['santri']->kelas->nama_kelas }}</p>
                        <p class="text-[9px] text-slate-500 mt-1 font-mono">
                            Tanggal: {{ implode(', ', $item['latest_dates']) }}
                        </p>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-red-150 text-red-900 text-[9px] font-extrabold uppercase">3x ALFA</span>
                </div>
            @empty
                <div class="text-center p-4 text-xs text-slate-400 italic">
                    🎉 Alhamdulillah, seluruh santri aktif rajin hadir.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Monthly Memorization Widget -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center space-x-1.5 border-b border-slate-100 pb-3">
            <span class="text-xl">📖</span>
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Widget Hafalan Bulanan</h3>
                <p class="text-[10px] text-slate-400">Status input hafalan bulan Hijriah: {{ $hafalanStats['month_name'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 text-center">
            <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-xl">
                <span class="text-[10px] text-emerald-800 font-bold uppercase tracking-wider">Sudah Diinput</span>
                <p class="text-xl font-black text-emerald-950 mt-1">{{ $hafalanStats['sudah'] }}</p>
            </div>
            <div class="p-3 bg-amber-50 border border-amber-100 rounded-xl">
                <span class="text-[10px] text-amber-800 font-bold uppercase tracking-wider">Belum Diinput</span>
                <p class="text-xl font-black text-amber-950 mt-1">{{ $hafalanStats['belum'] }}</p>
            </div>
        </div>
    </div>
</div>
