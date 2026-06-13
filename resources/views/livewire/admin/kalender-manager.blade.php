<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Kalender Hijriah & Pengecualian</h1>
        <p class="text-sm text-slate-500 mt-1">Konfigurasi koreksi hilal global dan atur libur pondok.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Hilal Correction Panel -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6 lg:col-span-1">
            <h2 class="font-bold text-slate-800 text-lg">Koreksi Hilal Global</h2>
            
            <!-- Date Display Card -->
            <div class="bg-gradient-to-br from-emerald-800 to-emerald-950 p-6 rounded-2xl text-white text-center relative overflow-hidden shadow-lg shadow-emerald-900/10">
                <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px]"></div>
                
                <div class="relative z-10 space-y-2">
                    <span class="text-3xl">🌙</span>
                    <p class="text-xs text-emerald-300 font-semibold uppercase tracking-wider">Tanggal Sistem Terkoreksi</p>
                    <h3 class="text-xl font-bold tracking-wide mt-1">
                        {{ $currentHijri['formatted'] }}
                    </h3>
                    <div class="inline-block px-3 py-1 bg-white/10 rounded-full text-xs font-semibold mt-2">
                        Koreksi: {{ $correction >= 0 ? '+' : '' }}{{ $correction }} Hari
                    </div>
                </div>
            </div>

            <!-- Mutation Buttons -->
            <div class="space-y-3">
                <p class="text-xs text-slate-400 font-medium">Lakukan mutasi global (+/-) 1 hari untuk menyesuaikan dengan rukyatul hilal setempat:</p>
                <div class="grid grid-cols-2 gap-3">
                    <button wire:click="incrementCorrection" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold py-3 rounded-xl transition duration-200 flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>➕</span> <span>+1 Hari</span>
                    </button>
                    <button wire:click="decrementCorrection" class="bg-red-50 hover:bg-red-100 text-red-800 border border-red-200 font-bold py-3 rounded-xl transition duration-200 flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>➖</span> <span>-1 Hari</span>
                    </button>
                </div>
                <button wire:click="resetCorrection" class="w-full bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 font-semibold py-2.5 rounded-xl text-xs transition duration-200 cursor-pointer">
                    🔄 Reset Koreksi (Kembali ke 0)
                </button>
            </div>

            <!-- Automatic Exceptions Read-Only Info -->
            <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 space-y-3">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">💡 Pengecualian Sistem Otomatis</h3>
                <ul class="text-xs text-slate-500 space-y-2">
                    <li class="flex items-start gap-1.5">
                        <span>🔒</span>
                        <span><strong>Malam Jum'at:</strong> Pembuatan presensi baru dikunci otomatis pada Kamis Sesi Malam.</span>
                    </li>
                    <li class="flex items-start gap-1.5">
                        <span>🔒</span>
                        <span><strong>Hari Jum'at:</strong> Pembuatan presensi baru dikunci otomatis pada Jum'at Sesi Pagi.</span>
                    </li>
                    <li class="flex items-start gap-1.5">
                        <span>🔒</span>
                        <span><strong>Bulan Syawal:</strong> Ditetapkan sebagai libur global. Presensi dan tagihan keuangan dibekukan.</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right: Manual Exceptions (Libur Massal) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm lg:col-span-2 space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="font-bold text-slate-800 text-lg">Pengecualian Manual (Libur Massal)</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Daftarkan masa liburan santri untuk menonaktifkan form presensi.</p>
                    </div>
                    <button wire:click="openModal" class="bg-emerald-800 hover:bg-emerald-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition duration-200 cursor-pointer">
                        ➕ Tambah Libur
                    </button>
                </div>

                <div class="overflow-hidden border border-slate-100 rounded-xl">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th class="px-4 py-3">Nama Libur / Acara</th>
                                <th class="px-4 py-3">Mulai Tanggal</th>
                                <th class="px-4 py-3">Selesai Tanggal</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($liburs as $libur)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-4 py-3 font-semibold text-slate-800">
                                        {{ $libur->nama_libur }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $libur->start_date->format('d-m-Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $libur->end_date->format('d-m-Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center space-x-1.5">
                                            <button wire:click="editLibur({{ $libur->id }})" class="p-1 rounded text-amber-600 hover:bg-amber-50 cursor-pointer" title="Ubah">
                                                ✏️
                                            </button>
                                            <button onclick="confirm('Apakah Anda yakin ingin menghapus libur massal ini?') || event.stopImmediatePropagation()" wire:click="deleteLibur({{ $libur->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 cursor-pointer" title="Hapus">
                                                🗑️
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-400 italic">Belum ada pengecualian libur massal yang terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Libur Massal CRUD Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden border border-slate-100">
                <!-- Modal Header -->
                <div class="bg-gradient-to-br from-emerald-800 to-emerald-900 px-6 py-4 text-white flex items-center justify-between">
                    <h3 class="font-bold text-lg">{{ $isEditMode ? 'Ubah Libur Massal' : 'Tambah Libur Massal' }}</h3>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white text-xl cursor-pointer">&times;</button>
                </div>

                <!-- Modal Body Form -->
                <form wire:submit.prevent="saveLibur" class="p-6 space-y-4">
                    @csrf
                    <!-- Nama Libur -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Libur / Acara</label>
                        <input wire:model="nama_libur" type="text" placeholder="Contoh: Libur Idul Adha" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600" required>
                        @error('nama_libur') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Date range -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Mulai Tanggal</label>
                        <input wire:model="start_date" type="date" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600" required>
                        @error('start_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Selesai Tanggal</label>
                        <input wire:model="end_date" type="date" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600" required>
                        @error('end_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-sm font-semibold transition cursor-pointer">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-800 hover:bg-emerald-700 text-white text-sm font-semibold transition shadow-md shadow-emerald-800/10 cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
