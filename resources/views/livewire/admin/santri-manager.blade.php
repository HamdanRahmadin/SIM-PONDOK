<div class="space-y-6" x-data="{ confirmDeleteId: null }">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen Data Santri</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola biodata, status, penempatan kamar & kelas, serta registrasi santri pondok.</p>
        </div>
        <button wire:click="create" class="bg-emerald-800 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow-md shadow-emerald-800/10 transition-all duration-200 active:scale-95 flex items-center gap-2 self-start cursor-pointer">
            <x-lucide-plus-circle class="w-5 h-5" /> <span>Santri Baru</span>
        </button>
    </div>

    <!-- Filters Panel -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center gap-4">
        <!-- Search bar -->
        <div class="w-full md:flex-1 relative">
            <x-lucide-search class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
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

    <!-- Toast Notification -->
    <div class="fixed top-4 right-4 z-[60] flex flex-col gap-2 pointer-events-none">
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" 
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-8"
                 class="bg-white border border-emerald-200 text-emerald-800 pl-4 pr-6 py-3 rounded-2xl shadow-lg text-sm flex items-center gap-3 pointer-events-auto ring-1 ring-black/5">
                <x-lucide-check-circle class="w-5 h-5 text-emerald-500" />
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" 
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-8"
                 class="bg-white border border-red-200 text-red-800 pl-4 pr-6 py-3 rounded-2xl shadow-lg text-sm flex items-center gap-3 pointer-events-auto ring-1 ring-black/5">
                <x-lucide-alert-circle class="w-5 h-5 text-red-500" />
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-bold tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">TTL</th>
                        <th class="px-6 py-4">Kamar</th>
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($santris as $index => $santri)
                        <tr class="hover:bg-slate-50/80 transition-colors duration-200 group">
                            <td class="px-6 py-4 text-slate-500 font-medium">
                                {{ ($santris->currentPage() - 1) * $santris->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                {{ $santri->nama_lengkap }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $santri->tempat_lahir ?? '-' }}, {{ $santri->tanggal_lahir ? $santri->tanggal_lahir->format('d-m-Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 font-semibold text-xs border border-emerald-100">
                                    {{ $santri->kamar->nama_kamar ?? 'Tanpa Kamar' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-semibold text-xs border border-slate-200">
                                    {{ $santri->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-status-badge :status="$santri->status" />
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    @if($santri->status === 'lulus')
                                        <!-- Read-only indicator -->
                                        <span class="text-xs text-slate-400 font-medium italic">Read-Only</span>
                                    @else
                                        <button wire:click="edit({{ $santri->id }})" class="p-1.5 rounded-lg text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-colors duration-200 active:scale-95 cursor-pointer" title="Ubah Data">
                                            <x-lucide-edit class="w-4 h-4" />
                                        </button>
                                        <button @click.prevent="confirmDeleteId = {{ $santri->id }}" class="p-1.5 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-200 active:scale-95 cursor-pointer" title="Hapus Data">
                                            <x-lucide-trash-2 class="w-4 h-4" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4 border border-slate-100 shadow-sm shadow-slate-100/50">
                                        <x-lucide-folder-search class="w-8 h-8 text-slate-300" />
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-700 mb-1">Data Santri Kosong</h3>
                                    <p class="text-xs text-slate-500 max-w-[250px] mb-4">Tidak ditemukan data santri yang sesuai dengan kriteria pencarian.</p>
                                    @if($search || $filterStatus !== 'semua' || $filterKelasId != 0)
                                        <button wire:click="$set('search', ''); $set('filterStatus', 'semua'); $set('filterKelasId', 0);" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-xs text-emerald-700 hover:bg-emerald-100 font-semibold flex items-center gap-1.5 transition-colors cursor-pointer active:scale-95">
                                            <x-lucide-rotate-ccw class="w-3 h-3" /> Reset Filter
                                        </button>
                                    @endif
                                </div>
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
    <form wire:submit.prevent="save">
        <x-modal :show="$isOpen" :title="$isEdit ? 'Ubah Data Santri' : 'Tambah Santri Baru'" subtitle="Kelola data diri santri" maxWidth="max-w-2xl" closeAction="closeModal">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                    <input wire:model="nama_lengkap" type="text" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600" required>
                    @error('nama_lengkap') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Tempat Lahir</label>
                        <input wire:model="tempat_lahir" type="text" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600">
                        @error('tempat_lahir') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Lahir</label>
                        <input wire:model="tanggal_lahir" type="date" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600">
                        @error('tanggal_lahir') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Lengkap</label>
                    <textarea wire:model="alamat" rows="2" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600"></textarea>
                    @error('alamat') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Kamar</label>
                        <select wire:model="kamar_id" class="w-full mt-1 text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-emerald-600">
                            <option value="">Tanpa Kamar</option>
                            @foreach($kamars as $kamar)
                                <option value="{{ $kamar->id }}">{{ $kamar->nama_kamar }}</option>
                            @endforeach
                        </select>
                        @error('kamar_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Kelas</label>
                        <select wire:model="kelas_id" class="w-full mt-1 text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-emerald-600">
                            <option value="">Tanpa Kelas</option>
                            @foreach($kelases as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                        @error('kelas_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Masuk</label>
                        <input wire:model="tanggal_masuk" type="date" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600">
                        @error('tanggal_masuk') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
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
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan</label>
                    <textarea wire:model="catatan" rows="2" placeholder="Catatan khusus tentang santri..." class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600"></textarea>
                    @error('catatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
            <x-slot:footer>
                <div></div>
                <div class="flex items-center space-x-3">
                    <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-sm font-semibold transition cursor-pointer">Batal</button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save" class="px-5 py-2.5 rounded-xl bg-emerald-800 hover:bg-emerald-700 text-white text-sm font-semibold transition shadow-md shadow-emerald-800/10 cursor-pointer disabled:opacity-50">
                        <span wire:loading.remove wire:target="save">Simpan</span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2"><x-lucide-loader-circle class="w-4 h-4 animate-spin" /> Menyimpan...</span>
                    </button>
                </div>
            </x-slot:footer>
        </x-modal>
    </form>

    <!-- Confirm Delete -->
    <x-confirm-dialog
        x-show="confirmDeleteId !== null"
        x-cloak
        title="Konfirmasi Hapus"
        message="Apakah Anda yakin ingin menghapus data santri ini? Tindakan ini tidak dapat dibatalkan."
        confirmText="Ya, Hapus"
        cancelText="Batal">
        <x-slot:confirm>
            <button @click="$wire.call('delete', confirmDeleteId); confirmDeleteId = null" class="py-2 px-5 bg-red-700 hover:bg-red-800 text-white rounded-lg text-xs font-bold transition shadow-md cursor-pointer" type="button">
                Ya, Hapus
            </button>
        </x-slot:confirm>
        <x-slot:cancel>
            <button @click="confirmDeleteId = null" class="py-2 px-4 border border-slate-200 text-slate-600 hover:bg-slate-100 rounded-lg text-xs font-semibold cursor-pointer transition" type="button">
                Batal
            </button>
        </x-slot:cancel>
    </x-confirm-dialog>
</div>

