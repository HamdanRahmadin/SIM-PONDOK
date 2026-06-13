<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen Data Santri</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola biodata, status, dan registrasi santri pondok.</p>
        </div>
        <button wire:click="create" class="bg-emerald-800 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow-md shadow-emerald-800/10 transition duration-200 flex items-center gap-1.5 self-start cursor-pointer">
            <span>➕</span> <span>Santri Baru</span>
        </button>
    </div>

    <!-- Filters Panel -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center gap-4">
        <!-- Search bar -->
        <div class="w-full md:flex-1 relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
            <input wire:model.live="search" type="text" placeholder="Cari nama santri..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/10 transition duration-200">
        </div>

        <!-- Status Filter -->
        <div class="w-full md:w-48">
            <select wire:model.live="filterStatus" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-medium focus:outline-none focus:border-emerald-600">
                <option value="semua">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
                <option value="lulus">Lulus (Alumni)</option>
            </select>
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
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">Tempat, Tanggal Lahir</th>
                        <th class="px-6 py-4">Alamat</th>
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4 text-center">Status</th>
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
                                {{ $santri->tempat_lahir }}, {{ $santri->tanggal_lahir->format('d-m-Y') }}
                            </td>
                            <td class="px-6 py-4 text-slate-500 max-w-xs truncate" title="{{ $santri->alamat }}">
                                {{ $santri->alamat }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-semibold text-xs border border-slate-200">
                                    {{ $santri->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($santri->status === 'aktif')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">Aktif</span>
                                @elseif($santri->status === 'nonaktif')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">Nonaktif</span>
                                @elseif($santri->status === 'lulus')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800 border border-blue-200" title="Arsip Alumni - Read-only">Lulus</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    @if($santri->status === 'lulus')
                                        <!-- Read-only indicator -->
                                        <span class="text-xs text-slate-400 font-medium italic">Read-Only</span>
                                    @else
                                        <button wire:click="edit({{ $santri->id }})" class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 hover:text-amber-700 transition duration-200 cursor-pointer" title="Ubah Data">
                                            ✏️
                                        </button>
                                        <button onclick="confirm('Apakah Anda yakin ingin menghapus data santri ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $santri->id }})" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-700 transition duration-200 cursor-pointer" title="Hapus Data">
                                            🗑️
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400 italic">
                                Tidak ditemukan data santri yang cocok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $santris->links() }}
        </div>
    </div>

    <!-- Modal Form Box -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden border border-slate-100">
                <!-- Modal Header -->
                <div class="bg-gradient-to-br from-emerald-800 to-emerald-900 px-6 py-4 text-white flex items-center justify-between">
                    <h3 class="font-bold text-lg">{{ $isEdit ? 'Ubah Data Santri' : 'Tambah Santri Baru' }}</h3>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white text-xl cursor-pointer">&times;</button>
                </div>

                <!-- Modal Body Form -->
                <form wire:submit.prevent="save" class="p-6 space-y-4">
                    @csrf
                    
                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                        <input wire:model="nama_lengkap" type="text" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600" required>
                        @error('nama_lengkap') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- TTL Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Tempat Lahir</label>
                            <input wire:model="tempat_lahir" type="text" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600" required>
                            @error('tempat_lahir') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Lahir</label>
                            <input wire:model="tanggal_lahir" type="date" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600" required>
                            @error('tanggal_lahir') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Lengkap</label>
                        <textarea wire:model="alamat" rows="2" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600" required></textarea>
                        @error('alamat') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kelas & Status Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Kelas</label>
                            <select wire:model="kelas_id" class="w-full mt-1 text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-emerald-600">
                                @foreach($kelases as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                            @error('kelas_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Status Santri</label>
                            <select wire:model="status" class="w-full mt-1 text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-emerald-600">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                                <option value="lulus">Lulus (Arsip Alumni)</option>
                            </select>
                            @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
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
