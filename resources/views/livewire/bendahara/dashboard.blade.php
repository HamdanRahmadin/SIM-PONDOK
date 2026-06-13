<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Dashboard Bendahara</h1>
        <p class="text-sm text-slate-500 mt-1">Status penerimaan keuangan, pencatatan cicilan, dan peringatan saldo.</p>
    </div>

    <!-- Alert Box Container -->
    @if(!empty($alerts))
        <div class="space-y-3">
            @foreach($alerts as $alert)
                <div class="p-4 rounded-2xl flex items-start gap-3 border text-xs font-semibold
                    {{ $alert['type'] === 'warning' ? 'bg-amber-50 text-amber-950 border-amber-200' : 'bg-blue-50 text-blue-950 border-blue-200' }}
                 shadow-sm">
                    <span class="text-lg">{{ $alert['type'] === 'warning' ? '⚠️' : 'ℹ️' }}</span>
                    <div>
                        <p class="font-bold">{{ $alert['type'] === 'warning' ? 'Peringatan Tunggakan Cicilan' : 'Informasi Santri Baru' }}</p>
                        <p class="mt-0.5 opacity-90 leading-relaxed">{{ $alert['message'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Metric 1: Daftar Ulang -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Kategori Pembayaran</span>
                <h3 class="font-extrabold text-slate-800 text-lg">Daftar Ulang</h3>
            </div>
            
            <div class="grid grid-cols-3 gap-2 text-center text-xs font-bold">
                <div class="p-3 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-100">
                    <span>Lunas</span>
                    <p class="text-lg font-black mt-1">{{ $metrics['daftar_ulang']['lunas'] }}</p>
                </div>
                <div class="p-3 bg-amber-50 text-amber-800 rounded-xl border border-amber-100">
                    <span>Dicicil</span>
                    <p class="text-lg font-black mt-1">{{ $metrics['daftar_ulang']['dicicil'] }}</p>
                </div>
                <div class="p-3 bg-slate-50 text-slate-500 rounded-xl border border-slate-200">
                    <span>Belum</span>
                    <p class="text-lg font-black mt-1">{{ $metrics['daftar_ulang']['belum_bayar'] }}</p>
                </div>
            </div>
        </div>

        <!-- Metric 2: Majeg Makan -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Kategori Pembayaran</span>
                <h3 class="font-extrabold text-slate-800 text-lg">Majeg Makan (Agregat)</h3>
            </div>

            <div class="grid grid-cols-3 gap-2 text-center text-xs font-bold">
                <div class="p-3 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-100">
                    <span>Lunas</span>
                    <p class="text-lg font-black mt-1">{{ $metrics['majeg_makan']['lunas'] }}</p>
                </div>
                <div class="p-3 bg-amber-50 text-amber-800 rounded-xl border border-amber-100">
                    <span>Dicicil</span>
                    <p class="text-lg font-black mt-1">{{ $metrics['majeg_makan']['dicicil'] }}</p>
                </div>
                <div class="p-3 bg-slate-50 text-slate-500 rounded-xl border border-slate-200">
                    <span>Belum</span>
                    <p class="text-lg font-black mt-1">{{ $metrics['majeg_makan']['belum_bayar'] }}</p>
                </div>
            </div>
        </div>

        <!-- Metric 3: Syahriah Sem 2 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Kategori Pembayaran</span>
                <h3 class="font-extrabold text-slate-800 text-lg">Syahriah Semester 2</h3>
            </div>

            <div class="grid grid-cols-3 gap-2 text-center text-xs font-bold">
                <div class="p-3 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-100">
                    <span>Lunas</span>
                    <p class="text-lg font-black mt-1">{{ $metrics['syahriah_sem_2']['lunas'] }}</p>
                </div>
                <div class="p-3 bg-amber-50 text-amber-800 rounded-xl border border-amber-100">
                    <span>Dicicil</span>
                    <p class="text-lg font-black mt-1">{{ $metrics['syahriah_sem_2']['dicicil'] }}</p>
                </div>
                <div class="p-3 bg-slate-50 text-slate-500 rounded-xl border border-slate-200">
                    <span>Belum</span>
                    <p class="text-lg font-black mt-1">{{ $metrics['syahriah_sem_2']['belum_bayar'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200/80 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-lg">Transaksi Terbaru (Terakhir di-update)</h3>
                <p class="text-xs text-slate-400 mt-0.5">Konfirmasi cepat untuk mencegah kesalahan entry/human error.</p>
            </div>
            <a href="{{ route('bendahara.keuangan') }}" class="text-xs font-bold text-emerald-800 hover:text-emerald-700 transition">
                Kelola Semua Tagihan ➡️
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-6 py-4">Waktu Update</th>
                        <th class="px-6 py-4">Nama Santri</th>
                        <th class="px-6 py-4">Kategori Tagihan</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4">Catatan Transaksi</th>
                        <th class="px-6 py-4 text-center">Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 text-slate-500 font-mono text-xs whitespace-nowrap">
                                {{ date('d-m-Y H:i:s', strtotime($tx->updated_at)) }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                {{ $tx->santri->nama_lengkap }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 capitalize">
                                {{ str_replace('_', ' ', $tx->kategori) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($tx->status === 'lunas')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase">Lunas</span>
                                @elseif($tx->status === 'dicicil')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 uppercase">Dicicil</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs italic">
                                {{ $tx->catatan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="confirmTransaction({{ $tx->id }})" 
                                        class="px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-100 rounded-lg text-xs font-bold transition cursor-pointer">
                                    ✓ Konfirmasi
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400 italic">
                                Belum ada aktivitas transaksi terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
