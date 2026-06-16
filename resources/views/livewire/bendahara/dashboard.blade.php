<div class="space-y-6" wire:poll.30s>
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">Dasbor Bendahara</h1>
        </div>
        <div class="flex items-center space-x-1.5 bg-emerald-50 text-emerald-800 px-3.5 py-2 rounded-xl border border-emerald-100 shadow-sm shrink-0">
            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
            <span class="text-xs font-bold uppercase tracking-wider">Polling Aktif (30s)</span>
        </div>
    </div>
 
    <!-- Alert Box Container for outstanding nonactive student debts -->
    @if(!empty($alerts))
        <div class="space-y-3">
            @foreach($alerts as $alert)
                <div class="p-4 rounded-2xl flex items-start gap-3 border text-xs font-semibold bg-rose-50 text-rose-950 border-rose-100 shadow-sm">
                    <x-lucide-alert-triangle class="w-5 h-5 mt-0.5 shrink-0 text-rose-800" />
                    <div>
                        <p class="font-bold text-rose-950">Tunggakan Santri Nonaktif Terdeteksi</p>
                        <p class="mt-0.5 opacity-90 leading-relaxed font-medium text-slate-650">{{ $alert['message'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
 
    <!-- Financial Overview Bento Grid (4 Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Pemasukan Terkumpul -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex flex-col justify-between min-h-[140px]">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pemasukan Terkumpul</span>
                    <h3 class="font-black text-slate-800 text-xl mt-1.5">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
                </div>
                <div class="p-2.5 bg-emerald-50 text-emerald-800 rounded-xl">
                    <x-lucide-trending-up class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-[10px] font-bold text-slate-400 mb-1">
                    <span>PERSENTASE TARGET</span>
                    <span>{{ $persenPemasukan }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-emerald-600 h-2 rounded-full transition-all duration-500" style="width: {{ $persenPemasukan }}%"></div>
                </div>
            </div>
        </div>

        <!-- Card 2: Sisa Tunggakan Aktif -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex flex-col justify-between min-h-[140px]">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Sisa Tunggakan Aktif</span>
                    <h3 class="font-black text-slate-800 text-xl mt-1.5 text-amber-800">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h3>
                </div>
                <div class="p-2.5 bg-amber-50 text-amber-800 rounded-xl">
                    <x-lucide-receipt class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-[10px] font-bold text-slate-400">
                <span>DARI SANTRI AKTIF</span>
                <span class="text-amber-800">{{ 100 - $persenPemasukan }}% BELUM TERCAPAI</span>
            </div>
        </div>

        <!-- Card 3: Total Target Keuangan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex flex-col justify-between min-h-[140px]">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Proyeksi Target</span>
                    <h3 class="font-black text-slate-800 text-xl mt-1.5 text-slate-700">Rp {{ number_format($totalTarget, 0, ',', '.') }}</h3>
                </div>
                <div class="p-2.5 bg-slate-50 text-slate-500 rounded-xl">
                    <x-lucide-target class="w-5 h-5" />
                </div>
            </div>
        </div>

        <!-- Card 4: Status Pembayaran Santri -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex flex-col justify-between min-h-[140px]">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Status Pembayaran Santri</span>
                    <div class="flex items-baseline gap-2 mt-1.5">
                        <span class="text-xs font-bold text-slate-400">Lunas:</span>
                        <span class="font-black text-emerald-600 text-lg">{{ $santriLunasCount }}</span>
                        <span class="text-slate-300">|</span>
                        <span class="text-xs font-bold text-slate-400">Belum:</span>
                        <span class="font-black text-amber-600 text-lg">{{ $santriBelumLunasCount }}</span>
                    </div>
                </div>
                <div class="p-2.5 bg-blue-50 text-blue-800 rounded-xl">
                    <x-lucide-users class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-[10px] font-bold text-slate-400 mb-1">
                    <span>PERSENTASE KELUNASAN</span>
                    <span>{{ $persenSantriLunas }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-emerald-600 h-2 rounded-full transition-all duration-500" style="width: {{ $persenSantriLunas }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bento Split Grid (60% Kategori / 40% Pintasan) -->
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-5">
        <!-- 60% Width: Category Breakdown -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm lg:col-span-6 space-y-4">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Progres Pengumpulan Per Kategori Biaya</h3>
                <p class="text-xs text-slate-400 mt-0.5">Visualisasi rasio terkumpul dibanding sisa piutang untuk santri aktif.</p>
            </div>
            
            <div class="space-y-4">
                <!-- Daftar Ulang Category -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-end text-xs">
                        <span class="font-extrabold text-slate-700">Daftar Ulang</span>
                        <span class="font-bold text-slate-500">
                            Rp {{ number_format($categoryMetrics['daftar_ulang']['terkumpul'], 0, ',', '.') }} / 
                            <span class="text-slate-400">Rp {{ number_format($categoryMetrics['daftar_ulang']['target'], 0, ',', '.') }}</span> 
                            ({{ $categoryMetrics['daftar_ulang']['persen'] }}%)
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $categoryMetrics['daftar_ulang']['persen'] }}%"></div>
                    </div>
                    <div class="flex gap-4 text-[10px] font-bold text-slate-400">
                        <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Lunas: {{ $categoryMetrics['daftar_ulang']['lunas'] }}</span>
                        <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Belum/Cicil: {{ $categoryMetrics['daftar_ulang']['belum'] }}</span>
                        @if($categoryMetrics['daftar_ulang']['pulang'] > 0)
                            <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Pulang: {{ $categoryMetrics['daftar_ulang']['pulang'] }}</span>
                        @endif
                    </div>
                </div>

                <!-- Syahriah Category -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-end text-xs">
                        <span class="font-extrabold text-slate-700">Syahriah (Sem 1 & 2)</span>
                        <span class="font-bold text-slate-500">
                            Rp {{ number_format($categoryMetrics['syahriah']['terkumpul'], 0, ',', '.') }} / 
                            <span class="text-slate-400">Rp {{ number_format($categoryMetrics['syahriah']['target'], 0, ',', '.') }}</span> 
                            ({{ $categoryMetrics['syahriah']['persen'] }}%)
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $categoryMetrics['syahriah']['persen'] }}%"></div>
                    </div>
                    <div class="flex gap-4 text-[10px] font-bold text-slate-400">
                        <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Lunas: {{ $categoryMetrics['syahriah']['lunas'] }}</span>
                        <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Belum/Cicil: {{ $categoryMetrics['syahriah']['belum'] }}</span>
                        @if($categoryMetrics['syahriah']['pulang'] > 0)
                            <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Pulang: {{ $categoryMetrics['syahriah']['pulang'] }}</span>
                        @endif
                    </div>
                </div>

                <!-- Majeg Makan Category -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-end text-xs">
                        <span class="font-extrabold text-slate-700">Majeg Makan (Bulanan)</span>
                        <span class="font-bold text-slate-500">
                            Rp {{ number_format($categoryMetrics['majeg_makan']['terkumpul'], 0, ',', '.') }} / 
                            <span class="text-slate-400">Rp {{ number_format($categoryMetrics['majeg_makan']['target'], 0, ',', '.') }}</span> 
                            ({{ $categoryMetrics['majeg_makan']['persen'] }}%)
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $categoryMetrics['majeg_makan']['persen'] }}%"></div>
                    </div>
                    <div class="flex gap-4 text-[10px] font-bold text-slate-400">
                        <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Lunas: {{ $categoryMetrics['majeg_makan']['lunas'] }}</span>
                        <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Belum/Cicil: {{ $categoryMetrics['majeg_makan']['belum'] }}</span>
                        @if($categoryMetrics['majeg_makan']['pulang'] > 0)
                            <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Pulang: {{ $categoryMetrics['majeg_makan']['pulang'] }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 40% Width: Actions & Rules Panel -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm lg:col-span-4 flex flex-col justify-between min-h-[280px]">
            <div>
                <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-1.5">
                    <x-lucide-zap class="w-4 h-4 text-emerald-800" /> Pintasan & Aturan
                </h3>
                <div class="grid grid-cols-1 gap-2.5">
                    <!-- Kelola Keuangan Shortcut -->
                    <a href="{{ route('bendahara.keuangan') }}" class="flex items-center space-x-3 bg-slate-50 hover:bg-slate-100/80 border border-slate-200/50 p-3 rounded-xl text-left transition cursor-pointer">
                        <span class="p-2 bg-emerald-50 text-emerald-800 rounded-lg">
                            <x-lucide-wallet class="w-4 h-4" />
                        </span>
                        <div>
                            <h4 class="text-xs font-bold text-slate-700">Lembar Manajemen Keuangan</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">Catat cicilan pembayaran santri, sesuaikan nominal tagihan, dan tandai status pulang.</p>
                        </div>
                    </a>

                    <!-- Direct Excel Export Shortcut -->
                    <button wire:click="export" class="flex items-center space-x-3 bg-slate-50 hover:bg-slate-100/80 border border-slate-200/50 p-3 rounded-xl text-left transition cursor-pointer w-full">
                        <span class="p-2 bg-emerald-50 text-emerald-800 rounded-lg">
                            <x-lucide-download class="w-4 h-4" />
                        </span>
                        <div>
                            <h4 class="text-xs font-bold text-slate-700">Ekspor Laporan Excel</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">Unduh data rincian kas, tagihan terbayar, dan sisa piutang santri aktif tahun ini.</p>
                        </div>
                    </button>
                </div>
            </div>
            
            <div class="pt-3 border-t border-slate-100 mt-3 flex items-center gap-2 text-[10px] text-slate-400 font-semibold italic leading-tight">
                <x-lucide-info class="w-4 h-4 shrink-0 text-slate-400" />
                <span>Status nonaktif otomatis menghentikan tagihan berjalan santri (ditandai Pulang).</span>
            </div>
        </div>
    </div>
 
    <!-- Main grid layout: Recent Transactions -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden max-w-3xl">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Riwayat Pembayaran</h3>
                <p class="text-xs text-slate-400 mt-0.5">Catatan riwayat pembayaran masuk dari santri secara real-time.</p>
            </div>
            <a href="{{ route('bendahara.keuangan') }}" class="text-xs font-bold text-emerald-800 hover:text-emerald-950 transition inline-flex items-center gap-1">
                Kelola Semua Keuangan <x-lucide-arrow-right class="w-3.5 h-3.5" />
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-bold tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-6 py-3.5">Waktu Transaksi</th>
                        <th class="px-6 py-3.5">Nama Santri</th>
                        <th class="px-6 py-3.5">Kategori Tagihan</th>
                        <th class="px-6 py-3.5 text-right">Nominal Masuk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-3.5 text-slate-500 font-mono text-xs whitespace-nowrap">
                                {{ date('d-m-Y H:i', strtotime($tx->created_at)) }}
                            </td>
                            <td class="px-6 py-3.5 font-bold text-slate-800">
                                {{ $tx->tagihan->santri->nama_lengkap }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-600 capitalize whitespace-nowrap">
                                {{ str_replace('_', ' ', $tx->tagihan->kategori) }}
                                @if($tx->tagihan->bulan_hijri)
                                    @php
                                        $chronologicalMonths = [11 => 1, 12 => 2, 1 => 3, 2 => 4, 3 => 5, 4 => 6, 5 => 7, 6 => 8, 7 => 9, 8 => 10, 9 => 11];
                                        $monthIndex = $chronologicalMonths[$tx->tagihan->bulan_hijri] ?? $tx->tagihan->bulan_hijri;
                                        $monthName = \App\Helpers\HijriHelper::getMonthName($tx->tagihan->bulan_hijri);
                                    @endphp
                                    (Bulan {{ $monthIndex }} - {{ $monthName }})
                                 @endif
                            </td>
                            <td class="px-6 py-3.5 text-right font-bold text-emerald-800 whitespace-nowrap">
                                Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic text-xs">
                                Belum ada aktivitas pembayaran tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
