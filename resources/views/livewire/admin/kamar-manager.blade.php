<div class="space-y-6" x-data="{ confirmDeleteId: null }">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen Kamar</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data kamar asrama santri.</p>
        </div>
        <button wire:click="openModal" class="bg-emerald-800 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow-md shadow-emerald-800/10 transition duration-200 flex items-center gap-1.5 self-start cursor-pointer">
            <x-lucide-plus-circle class="w-5 h-5" /> <span>Kamar Baru</span>
        </button>
    </div>

    <!-- Flash Message Notification -->
    <x-alert type="success" />
    <x-alert type="error" />

    <!-- List Kamar -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
        <h2 class="font-bold text-slate-800 text-lg">Daftar Kamar</h2>
        <div class="overflow-x-auto border border-slate-100 rounded-xl">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-bold tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-4 py-3 w-1/4">Nama Kamar</th>
                        <th class="px-4 py-3 w-1/3">Keterangan</th>
                        <th class="px-4 py-3 text-center w-1/4">Jumlah Santri</th>
                        <th class="px-4 py-3 text-center w-1/4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kamars as $kamar)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3 font-semibold text-slate-800">
                                {{ $kamar->nama_kamar }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 whitespace-normal">
                                {{ $kamar->keterangan ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-slate-600">
                                {{ $kamar->santris_count }} Santri
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <button wire:click="editKamar({{ $kamar->id }})" class="p-2 rounded text-amber-600 hover:bg-amber-50 cursor-pointer" title="Ubah Kamar">
                                        <x-lucide-pencil class="w-4 h-4" />
                                    </button>
                                    <button @click.prevent="confirmDeleteId = {{ $kamar->id }}" class="p-2 rounded text-red-600 hover:bg-red-50 cursor-pointer" title="Hapus Kamar">
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                                Belum ada data kamar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah/Edit Kamar -->
    <x-modal :show="$isModalOpen" :title="$isEditMode ? 'Ubah Data Kamar' : 'Tambah Kamar Baru'" subtitle="Kelola data kamar asrama" maxWidth="max-w-sm" closeAction="closeModal">
        <form wire:submit.prevent="saveKamar" class="p-6 space-y-4">
            <!-- Nama Kamar -->
            <div class="space-y-1.5">
                <label for="nama_kamar" class="block text-sm font-semibold text-slate-700">Nama Kamar <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <x-lucide-home class="w-5 h-5" />
                    </div>
                    <input type="text" id="nama_kamar" wire:model.defer="nama_kamar" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors" placeholder="Cth: Abu Bakar">
                </div>
                @error('nama_kamar') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <!-- Keterangan -->
            <div class="space-y-1.5">
                <label for="keterangan" class="block text-sm font-semibold text-slate-700">Keterangan (Opsional)</label>
                <textarea id="keterangan" wire:model.defer="keterangan" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors" placeholder="Keterangan tambahan tentang kamar ini..."></textarea>
                @error('keterangan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="pt-2 flex justify-end gap-3">
                <button type="button" wire:click="closeModal" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="bg-emerald-800 hover:bg-emerald-700 text-white px-5 py-2.5 text-sm font-semibold rounded-xl shadow-md shadow-emerald-800/10 transition flex items-center gap-2 cursor-pointer">
                    <x-lucide-save class="w-4 h-4" /> Simpan Data
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Konfirmasi Hapus Alpine -->
    <x-confirm-dialog
        x-show="confirmDeleteId !== null"
        title="Hapus Kamar?"
        message="Apakah Anda yakin ingin menghapus kamar ini? Tindakan ini tidak dapat dibatalkan."
        confirmText="Ya, Hapus Kamar"
        cancelText="Batal"
        @confirm="if(confirmDeleteId) { $wire.deleteKamar(confirmDeleteId).then(() => { confirmDeleteId = null; }) }"
        @cancel="confirmDeleteId = null"
    />
</div>
