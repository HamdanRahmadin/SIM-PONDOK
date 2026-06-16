<div class="space-y-4">
    {{-- Page Title --}}
    <header class="mb-4 md:hidden">
        <h2 class="text-3xl font-bold text-on-background">Riwayat Presensi</h2>
        <p class="text-sm text-on-surface-variant mt-1">Ringkasan kehadiran kelas yang dipilih.</p>
    </header>

    {{-- Tab Switcher --}}
    <div class="flex border border-outline-variant/30 rounded-lg p-1 bg-surface-container-low">
        <button wire:click="switchTab('setoran')" class="flex-1 py-2 text-sm font-semibold rounded-md transition-colors {{ $activeTab === 'setoran' ? 'bg-surface-container-lowest shadow-sm text-primary' : 'text-on-surface-variant hover:text-on-surface' }}">
            Harian Setoran
        </button>
        <button wire:click="switchTab('halaqoh')" class="flex-1 py-2 text-sm font-semibold rounded-md transition-colors {{ $activeTab === 'halaqoh' ? 'bg-surface-container-lowest shadow-sm text-primary' : 'text-on-surface-variant hover:text-on-surface' }}">
            Mingguan Halaqoh
        </button>
    </div>

    {{-- KPI Bento Grid --}}
    <section class="bento-grid">
        <div class="col-span-6 md:col-span-3 glass-panel rounded-xl p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-on-surface-variant">Kehadiran</span>
                <span class="material-symbols-outlined text-brand-green text-lg">trending_up</span>
            </div>
            <div>
                <div class="text-3xl font-bold text-primary">
                    {{ $riwayatData->count() > 0 ? round(($riwayatData->where('status', 'hadir')->count() / $riwayatData->count()) * 100) : 0 }}%
                </div>
                <div class="text-xs font-semibold text-on-surface-variant mt-1">Tingkat kehadiran</div>
            </div>
        </div>
        <div class="col-span-6 md:col-span-3 glass-panel rounded-xl p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-on-surface-variant">Total Hadir</span>
                <div class="w-2 h-2 rounded-full bg-brand-green"></div>
            </div>
            <div>
                <div class="text-3xl font-bold text-on-background">{{ $riwayatData->where('status', 'hadir')->count() }}</div>
                <div class="text-xs font-semibold text-on-surface-variant mt-1">Santri</div>
            </div>
        </div>
        <div class="col-span-6 md:col-span-3 glass-panel rounded-xl p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-on-surface-variant">Total Izin</span>
                <div class="w-2 h-2 rounded-full bg-brand-yellow"></div>
            </div>
            <div>
                <div class="text-3xl font-bold text-on-background">{{ $riwayatData->where('status', 'izin_sakit')->count() }}</div>
                <div class="text-xs font-semibold text-on-surface-variant mt-1">Santri</div>
            </div>
        </div>
        <div class="col-span-6 md:col-span-3 glass-panel rounded-xl p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-on-surface-variant">Total Alfa</span>
                <div class="w-2 h-2 rounded-full bg-error"></div>
            </div>
            <div>
                <div class="text-3xl font-bold text-on-background">{{ $riwayatData->where('status', 'alfa')->count() }}</div>
                <div class="text-xs font-semibold text-error mt-1">Tidak hadir</div>
            </div>
        </div>
    </section>

    {{-- Filters --}}
    <section class="glass-panel rounded-xl p-4">
        <div class="grid grid-cols-2 gap-3 {{ $activeTab === 'setoran' ? 'md:grid-cols-3' : '' }}">
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1 uppercase tracking-wider">Pilih Kelas</label>
                <select wire:model.live="kelas_id" class="w-full rounded-lg border border-outline-variant/50 bg-surface text-sm text-on-surface focus:border-primary focus:ring-1 focus:ring-primary p-2 transition-colors">
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1 uppercase tracking-wider">Tanggal</label>
                <input type="date" wire:model.live="tanggal" class="w-full rounded-lg border border-outline-variant/50 bg-surface text-sm text-on-surface focus:border-primary focus:ring-1 focus:ring-primary p-2 transition-colors">
            </div>
            @if($activeTab === 'setoran')
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1 uppercase tracking-wider">Filter Sesi</label>
                <select wire:model.live="sesi" class="w-full rounded-lg border border-outline-variant/50 bg-surface text-sm text-on-surface focus:border-primary focus:ring-1 focus:ring-primary p-2 transition-colors">
                    <option value="semua">Semua Sesi</option>
                    <option value="pagi">Sesi Pagi</option>
                    <option value="malam">Sesi Malam</option>
                </select>
            </div>
            @endif
        </div>
    </section>

    {{-- Results List --}}
    <section class="glass-panel rounded-xl overflow-hidden">
        <div class="p-3 bg-surface-container/50 border-b border-outline-variant/20 flex justify-between items-center flex-wrap gap-2">
            <div>
                <h3 class="font-semibold text-on-background">Hasil</h3>
                <span class="text-xs font-semibold text-primary bg-primary-container/20 px-2 py-0.5 rounded-md inline-block mt-1">{{ $riwayatData->count() }} data</span>
            </div>
            @if($activeTab === 'halaqoh' && $riwayatData->count() > 0)
            <button wire:click="exportHalaqoh" class="flex items-center gap-1.5 text-xs font-semibold text-on-primary bg-primary px-3 py-2 rounded-lg hover:opacity-90 active:scale-95 transition-all shadow-sm">
                <span class="material-symbols-outlined text-lg">download</span>
                Download Laporan (Excel)
            </button>
            @endif
        </div>
        <ul class="divide-y divide-outline-variant/20">
            @forelse($riwayatData as $data)
            <li class="flex items-center justify-between p-4 hover:bg-surface-container-low transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-surface-variant flex items-center justify-center text-sm font-bold text-on-surface-variant">
                        {{ strtoupper(substr($data->santri->nama_lengkap ?? '-', 0, 1)) }}
                    </div>
                    <div>
                        <span class="font-semibold text-sm text-on-background block">{{ $data->santri->nama_lengkap ?? '-' }}</span>
                        @if($activeTab === 'setoran' && isset($data->sesi))
                            <span class="text-xs text-on-surface-variant capitalize">Sesi {{ $data->sesi }}</span>
                        @endif
                        @if($data->catatan)
                            <span class="text-xs text-on-surface-variant"> &bull; {{ $data->catatan }}</span>
                        @endif
                    </div>
                </div>
                @if($data->status === 'hadir')
                    <span class="text-[10px] font-bold uppercase tracking-wider bg-primary-container/30 text-primary px-2 py-1 rounded-full">Hadir</span>
                @elseif($data->status === 'izin_sakit')
                    <span class="text-[10px] font-bold uppercase tracking-wider bg-secondary-container text-on-secondary-container px-2 py-1 rounded-full">Izin</span>
                @else
                    <span class="text-[10px] font-bold uppercase tracking-wider bg-error-container text-on-error-container px-2 py-1 rounded-full">Alfa</span>
                @endif
            </li>
            @empty
            <li class="p-10 text-center">
                <span class="material-symbols-outlined text-4xl text-on-surface-variant/50">search_off</span>
                <p class="text-on-surface-variant text-sm mt-2">Tidak ada data pada tanggal tersebut.</p>
            </li>
            @endforelse
        </ul>
    </section>

    {{-- Archive Section --}}
    <section class="space-y-4 pb-8">
        <h3 class="font-semibold text-lg text-on-background">Arsip Bulanan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @php
                $months = collect();
                for ($i = 0; $i < 3; $i++) {
                    $months->push(\Carbon\Carbon::now()->subMonths($i));
                }
            @endphp
            @foreach($months as $month)
            <div wire:click="$set('tanggal', '{{ $month->format('Y-m-d') }}')" class="glass-panel p-4 rounded-xl flex items-center justify-between hover:bg-surface-container-low transition-colors cursor-pointer group">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">folder</span>
                    <span class="font-semibold text-sm text-on-background">{{ $month->translatedFormat('F Y') }}</span>
                </div>
                <span class="material-symbols-outlined text-sm text-on-surface-variant">chevron_right</span>
            </div>
            @endforeach
        </div>
    </section>
</div>
