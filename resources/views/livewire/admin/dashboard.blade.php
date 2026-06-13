<div class="space-y-6">
    <!-- Page Header Title -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Ringkasan Eksekutif</h1>
        <p class="text-sm text-slate-500 mt-1">Status operasional, kehadiran santri, dan laporan audit log SIM-PONDOK.</p>
    </div>

    <!-- Quick Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Card 1: Total Santri -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Santri Aktif</span>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">
                    {{ \App\Models\Santri::where('status', 'aktif')->count() }}
                </h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-xl text-emerald-800 border border-emerald-100 shadow-inner">
                🎓
            </div>
        </div>

        <!-- Card 2: Total Kelas -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kelas</span>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">
                    {{ \App\Models\Kelas::count() }}
                </h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-xl text-emerald-800 border border-emerald-100 shadow-inner">
                Kelas
            </div>
        </div>

        <!-- Card 3: Tahun Ajaran -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tahun Ajaran</span>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">
                    {{ \App\Models\Setting::getByKey('current_tahun_ajaran', '1447') }} H
                </h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-xl text-emerald-800 border border-emerald-100 shadow-inner">
                🕌
            </div>
        </div>

        <!-- Card 4: Hilal Correction -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Koreksi Hilal</span>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">
                    {{ (int) \App\Models\Setting::getByKey('hilal_correction', 0) > 0 ? '+' : '' }}{{ \App\Models\Setting::getByKey('hilal_correction', 0) }} Hari
                </h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-xl text-emerald-800 border border-emerald-100 shadow-inner">
                🌙
            </div>
        </div>
    </div>

    <!-- Graph & Export Side-by-Side -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kehadiran Chart (SVG Custom) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Statistik Presensi Bulan Ini</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Agregat kehadiran Sesi Pagi vs Malam (Bulan: {{ $monthNames[$selectedMonth] ?? '' }})</p>
                </div>
                <!-- Month Selection Filter inside chart card -->
                <select wire:model.live="selectedMonth" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 font-medium focus:outline-none focus:border-emerald-600">
                    @foreach($monthNames as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- SVG Visual Representation -->
            @php
                $pagiStats = $chartData['pagi'];
                $malamStats = $chartData['malam'];
                $totalPagi = array_sum($pagiStats);
                $totalMalam = array_sum($malamStats);
                
                $pctPagiHadir = $totalPagi > 0 ? round(($pagiStats['hadir'] / $totalPagi) * 100) : 0;
                $pctPagiAlfa = $totalPagi > 0 ? round(($pagiStats['alfa'] / $totalPagi) * 100) : 0;
                $pctPagiOther = $totalPagi > 0 ? round(($pagiStats['izin_sakit'] / $totalPagi) * 100) : 0;

                $pctMalamHadir = $totalMalam > 0 ? round(($malamStats['hadir'] / $totalMalam) * 100) : 0;
                $pctMalamAlfa = $totalMalam > 0 ? round(($malamStats['alfa'] / $totalMalam) * 100) : 0;
                $pctMalamOther = $totalMalam > 0 ? round(($malamStats['izin_sakit'] / $totalMalam) * 100) : 0;
            @endphp

            <div class="space-y-6">
                <!-- Bar 1: Sesi Pagi -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-slate-700 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Sesi Pagi (05:00 - 08:00)
                        </span>
                        <span class="text-slate-500 font-medium">{{ $totalPagi }} record terdaftar</span>
                    </div>
                    <div class="h-6 w-full bg-slate-100 rounded-full overflow-hidden flex shadow-inner">
                        @if($totalPagi > 0)
                            <div class="bg-emerald-600 text-white text-[10px] font-bold flex items-center justify-center transition-all duration-500" style="width: {{ $pctPagiHadir }}%">
                                {{ $pctPagiHadir > 10 ? "$pctPagiHadir% Hadir" : "" }}
                            </div>
                            <div class="bg-red-500 text-white text-[10px] font-bold flex items-center justify-center transition-all duration-500" style="width: {{ $pctPagiAlfa }}%">
                                {{ $pctPagiAlfa > 10 ? "$pctPagiAlfa% Alfa" : "" }}
                            </div>
                            <div class="bg-amber-500 text-white text-[10px] font-bold flex items-center justify-center transition-all duration-500" style="width: {{ $pctPagiOther }}%">
                                {{ $pctPagiOther > 10 ? "$pctPagiOther% Izin" : "" }}
                            </div>
                        @else
                            <div class="w-full flex items-center justify-center text-slate-400 text-xs font-medium italic">Tidak ada data presensi</div>
                        @endif
                    </div>
                </div>

                <!-- Bar 2: Sesi Malam -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-slate-700 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-950"></span> Sesi Malam (18:00 - 22:00)
                        </span>
                        <span class="text-slate-500 font-medium">{{ $totalMalam }} record terdaftar</span>
                    </div>
                    <div class="h-6 w-full bg-slate-100 rounded-full overflow-hidden flex shadow-inner">
                        @if($totalMalam > 0)
                            <div class="bg-emerald-600 text-white text-[10px] font-bold flex items-center justify-center transition-all duration-500" style="width: {{ $pctMalamHadir }}%">
                                {{ $pctMalamHadir > 10 ? "$pctMalamHadir% Hadir" : "" }}
                            </div>
                            <div class="bg-red-500 text-white text-[10px] font-bold flex items-center justify-center transition-all duration-500" style="width: {{ $pctMalamAlfa }}%">
                                {{ $pctMalamAlfa > 10 ? "$pctMalamAlfa% Alfa" : "" }}
                            </div>
                            <div class="bg-amber-500 text-white text-[10px] font-bold flex items-center justify-center transition-all duration-500" style="width: {{ $pctMalamOther }}%">
                                {{ $pctMalamOther > 10 ? "$pctMalamOther% Izin" : "" }}
                            </div>
                        @else
                            <div class="w-full flex items-center justify-center text-slate-400 text-xs font-medium italic">Tidak ada data presensi</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="flex flex-wrap gap-4 pt-3 border-t border-slate-100 text-xs font-medium">
                <span class="flex items-center gap-1.5 text-emerald-800"><span class="w-3.5 h-3.5 rounded bg-emerald-600"></span> Hadir</span>
                <span class="flex items-center gap-1.5 text-red-800"><span class="w-3.5 h-3.5 rounded bg-red-500"></span> Alfa</span>
                <span class="flex items-center gap-1.5 text-amber-800"><span class="w-3.5 h-3.5 rounded bg-amber-500"></span> Izin / Sakit</span>
            </div>
        </div>

        <!-- Export Laporan Panel -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="space-y-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Ekspor Laporan Bulanan</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Unduh data presensi & keuangan ke berkas Microsoft Excel.</p>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pilih Kelas</label>
                        <select wire:model="selectedKelasId" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-medium focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/10">
                            <option value="0">Semua Kelas</option>
                            @foreach($kelases as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pilih Bulan Hijriah</label>
                        <select wire:model="selectedMonth" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-medium focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/10">
                            @foreach($monthNames as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <button wire:click="exportXlsx" 
                    class="w-full mt-6 bg-emerald-800 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-md shadow-emerald-800/10 transition duration-200 flex items-center justify-center gap-2 cursor-pointer">
                <span>📊</span> <span>Unduh Berkas (.xlsx)</span>
            </button>
        </div>
    </div>

    <!-- Audit Trail / Logs -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200/80">
            <h3 class="font-bold text-slate-800 text-lg">Log Aktivitas Sistem (Audit Trail)</h3>
            <p class="text-xs text-slate-400 mt-0.5">Jejak aktivitas admin, ustaz, bendahara, dan otomatisasi cron jobs.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-6 py-3">Waktu (Solar)</th>
                        <th class="px-6 py-3">Nama Aktor</th>
                        <th class="px-6 py-3">Aksi</th>
                        <th class="px-6 py-3">Detail Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-3.5 text-slate-500 font-mono text-xs whitespace-nowrap">
                                {{ date('d-m-Y H:i:s', strtotime($log->created_at)) }}
                            </td>
                            <td class="px-6 py-3.5 font-semibold text-slate-800">
                                {{ $log->nama_aktor }}
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ str_contains($log->aksi, 'Ubah') || str_contains($log->aksi, 'Edit') ? 'bg-amber-100 text-amber-800 border border-amber-200' : '' }}
                                    {{ str_contains($log->aksi, 'Tambah') || str_contains($log->aksi, 'Inisiasi') ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : '' }}
                                    {{ str_contains($log->aksi, 'Hapus') || str_contains($log->aksi, 'Reset') ? 'bg-red-100 text-red-800 border border-red-200' : '' }}
                                    {{ !str_contains($log->aksi, 'Ubah') && !str_contains($log->aksi, 'Tambah') && !str_contains($log->aksi, 'Hapus') && !str_contains($log->aksi, 'Inisiasi') && !str_contains($log->aksi, 'Reset') ? 'bg-slate-100 text-slate-800 border border-slate-200' : '' }}
                                ">
                                    {{ $log->aksi }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-slate-600 max-w-xs truncate" title="{{ $log->details }}">
                                {{ $log->details }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic">
                                Belum ada log aktivitas yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
