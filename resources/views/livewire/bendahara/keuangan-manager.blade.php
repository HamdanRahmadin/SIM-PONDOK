<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Manajemen Lembar Keuangan</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola tagihan tahunan, catat angsuran cicilan, dan perbarui status pembayaran.</p>
    </div>

    <!-- Filters Panel -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center gap-4">
        <!-- Search -->
        <div class="w-full md:flex-1 relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
            <input wire:model.live="search" type="text" placeholder="Cari nama santri..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/10 transition duration-200">
        </div>

        <!-- Class Filter -->
        <div class="w-full md:w-48">
            <select wire:model.live="filterKelasId" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-medium focus:outline-none focus:border-emerald-600">
                <option value="0">Semua Kelas</option>
                @foreach($kelases as $kelas)
                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                @endforeach
            </select>
        </div>

        <!-- Year Filter -->
        <div class="w-full md:w-36">
            <select wire:model.live="selectedYear" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-medium focus:outline-none focus:border-emerald-600">
                <option value="1447">1447 H</option>
                <option value="1448">1448 H</option>
                <option value="1449">1449 H</option>
            </select>
        </div>
    </div>

    <!-- Santri List Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4 text-center">Status Santri</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($santris as $index => $santri)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 text-slate-500 font-medium">
                                {{ ($santris->currentPage() - 1) * $santris->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                {{ $santri->nama_lengkap }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $santri->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($santri->status === 'aktif')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase">Aktif</span>
                                @elseif($santri->status === 'nonaktif')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200 uppercase">Nonaktif</span>
                                @elseif($santri->status === 'lulus')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200 uppercase">Lulus</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="selectSantri({{ $santri->id }})" 
                                        class="px-4 py-1.5 bg-emerald-800 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-800/10 transition cursor-pointer">
                                    Kelola Keuangan
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 italic font-medium">
                                Tidak ditemukan data santri yang cocok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $santris->links() }}
        </div>
    </div>

    <!-- Keuangan Detail Modal -->
    @if($isModalOpen && $selectedSantri)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-xl w-full max-w-2xl overflow-hidden border border-slate-100 flex flex-col max-h-[90vh]">
                <!-- Modal Header -->
                <div class="bg-gradient-to-br from-emerald-800 to-emerald-950 px-6 py-5 text-white flex items-center justify-between shadow-md">
                    <div>
                        <h3 class="font-bold text-lg">Lembar Keuangan Santri</h3>
                        <p class="text-xs text-emerald-300 font-semibold mt-0.5 capitalize">
                            {{ $selectedSantri->nama_lengkap }} (Kelas: {{ $selectedSantri->kelas->nama_kelas ?? '-' }}, Status: {{ $selectedSantri->status }}) | TA: {{ $selectedYear }} H
                        </p>
                    </div>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white text-2xl cursor-pointer">&times;</button>
                </div>

                <!-- Modal Body: Scrollable list of payments -->
                <div class="p-6 overflow-y-auto space-y-4 flex-1">
                    
                    @if($selectedSantri->status === 'lulus')
                        <!-- Read-only status info -->
                        <div class="bg-blue-50 border border-blue-100 p-3.5 rounded-2xl text-blue-900 text-xs font-semibold">
                            ℹ️ <strong>Arsip Alumni (Lulus)</strong>: Lembar tagihan santri ini bersifat Read-Only (hanya baca). Status pembayaran tidak dapat diubah kembali.
                        </div>
                    @endif

                    @if($selectedSantri->status === 'nonaktif')
                        <!-- Nonactive status info -->
                        <div class="bg-red-50 border border-red-100 p-3.5 rounded-2xl text-red-900 text-xs font-semibold">
                            ⚠️ <strong>Santri Nonaktif</strong>: Sisa tagihan berstatus "Belum Bayar" telah dibekukan. Jika santri di-set Aktif kembali, lembar tagihan akan diaktifkan ulang.
                        </div>
                    @endif

                    <!-- Billings Checklist List -->
                    <div class="divide-y divide-slate-100 border border-slate-200/80 rounded-2xl overflow-hidden bg-slate-50/50 p-2">
                        @forelse($billings as $bill)
                            @php
                                $isSem2Locked = ($bill->kategori === 'syahriah_semester_2' && !$this->isSemester2Open());
                            @endphp
                            <div class="p-4 hover:bg-white rounded-xl transition space-y-2">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <!-- Category Name & Status Badge -->
                                    <div class="space-y-1">
                                        <span class="font-bold text-slate-800 text-sm capitalize">
                                            {{ str_replace('_', ' ', $bill->kategori) }}
                                        </span>
                                        @if($isSem2Locked)
                                            <span class="block text-[10px] text-red-700 font-bold">🔒 Terkunci (Belum paruh kedua tahun ajaran)</span>
                                        @endif
                                        @if($bill->status === 'dicicil')
                                            <div class="text-xs text-amber-800 font-semibold mt-0.5">
                                                💵 Masuk: Rp {{ number_format($bill->nominal_bayar, 0, ',', '.') }} 
                                                @if($bill->catatan)
                                                    <span class="text-slate-500 font-normal italic">({{ $bill->catatan }})</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Status Badges -->
                                    <div class="flex items-center space-x-2 shrink-0">
                                        @if($bill->status === 'lunas')
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wide">LUNAS</span>
                                        @elseif($bill->status === 'dicicil')
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200 uppercase tracking-wide">DICICIL</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-red-50 text-red-650 border border-red-100 uppercase tracking-wide">BELUM BAYAR</span>
                                        @endif

                                        <!-- Actions (Only if student is not Graduated/Lulus) -->
                                        @if($selectedSantri->status !== 'lulus')
                                            <div class="flex items-center space-x-1.5 pl-2 border-l border-slate-200">
                                                @if(!$isSem2Locked)
                                                    <button wire:click="markAsLunas({{ $bill->id }})" class="px-2.5 py-1 bg-emerald-700 hover:bg-emerald-600 text-white rounded-lg text-xs font-bold transition cursor-pointer" title="Set Lunas">
                                                        ✓ Lunas
                                                    </button>
                                                    <button wire:click="openInstallmentForm({{ $bill->id }})" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-400 text-white rounded-lg text-xs font-bold transition cursor-pointer" title="Set Cicil">
                                                        ✎ Cicil
                                                    </button>
                                                @endif
                                                <button wire:click="resetBill({{ $bill->id }})" class="px-2 py-1 bg-slate-200 hover:bg-red-700 hover:text-white text-slate-600 rounded-lg text-xs font-bold transition cursor-pointer" title="Reset ke Belum Bayar">
                                                    Reset
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Inline Installment Form Overlay -->
                                @if($editBillingId === $bill->id)
                                    <div class="p-3.5 bg-amber-50/80 border border-amber-100 rounded-xl space-y-3 mt-2">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-bold text-amber-900 uppercase tracking-wider mb-1">Nominal Angsuran (Masuk)</label>
                                                <input type="number" wire:model="installmentAmount" class="w-full text-xs px-3 py-2 bg-white border border-amber-200 rounded-lg focus:outline-none focus:border-amber-600" required>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-amber-900 uppercase tracking-wider mb-1">Catatan Khusus (Opsional)</label>
                                                <input type="text" wire:model="installmentNote" placeholder="Contoh: Santri pindahan..." class="w-full text-xs px-3 py-2 bg-white border border-amber-200 rounded-lg focus:outline-none focus:border-amber-600">
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-end space-x-2 pt-2">
                                            <button type="button" wire:click="closeInstallmentForm" class="px-3 py-1.5 rounded-lg border border-amber-200 hover:bg-white text-amber-800 text-xs font-bold cursor-pointer">Batal</button>
                                            <button type="button" wire:click="saveInstallment" class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold cursor-pointer">Simpan Cicilan</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="p-6 text-center text-xs text-slate-400 italic">Tidak ada lembar tagihan untuk tahun ajaran aktif ini.</p>
                        @endforelse
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200/80 flex items-center justify-end">
                    <button wire:click="closeModal" class="px-5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-350 text-slate-700 text-xs font-bold shadow-sm cursor-pointer">
                        Tutup Lembar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
