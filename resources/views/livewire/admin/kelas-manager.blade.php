<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen Kelas & Kenaikan Massal</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data kelas dan jalankan fungsi kenaikan kelas massal akhir tahun ajaran.</p>
        </div>
        <button wire:click="openModal" class="bg-emerald-800 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow-md shadow-emerald-800/10 transition duration-200 flex items-center gap-1.5 self-start cursor-pointer">
            <span>➕</span> <span>Kelas Baru</span>
        </button>
    </div>

    <!-- Two Column Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Class List Column -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4 lg:col-span-1">
            <h2 class="font-bold text-slate-800 text-lg">Daftar Kelas</h2>
            <div class="overflow-hidden border border-slate-100 rounded-xl">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th class="px-4 py-3">Nama Kelas</th>
                            <th class="px-4 py-3 text-center">Jumlah Santri</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($kelases as $kelas)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-semibold text-slate-800">
                                    {{ $kelas->nama_kelas }}
                                </td>
                                <td class="px-4 py-3 text-center text-slate-600">
                                    {{ $kelas->santris_count }} Santri
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        <button wire:click="editKelas({{ $kelas->id }})" class="p-1 rounded text-amber-600 hover:bg-amber-50 cursor-pointer" title="Ubah Nama">
                                            ✏️
                                        </button>
                                        <button onclick="confirm('Apakah Anda yakin ingin menghapus kelas ini?') || event.stopImmediatePropagation()" wire:click="deleteKelas({{ $kelas->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 cursor-pointer" title="Hapus Kelas">
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Batch Promotion Column -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm lg:col-span-2 space-y-6 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="border-b border-slate-100 pb-4">
                    <h2 class="font-bold text-slate-800 text-lg">Kenaikan Kelas Massal</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Pindahkan rombongan belajar santri dari kelas lama ke kelas baru secara massal.</p>
                </div>

                <!-- Source Class Filter -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Kelas Asal (Sumber)</label>
                        <select wire:model.live="promoSourceKelasId" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-medium focus:outline-none focus:border-emerald-600">
                            @foreach($kelases as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Kelas Baru (Tujuan)</label>
                        <select wire:model="targetKelasId" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-medium focus:outline-none focus:border-emerald-600">
                            @foreach($kelases as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Students Checklist Area -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <span>Pilih Santri (Checklist)</span>
                        <span>{{ count($selectedSantriIds) }} terpilih</span>
                    </div>
                    <div class="border border-slate-200/80 rounded-xl max-h-60 overflow-y-auto divide-y divide-slate-100 p-2 bg-slate-50/50">
                        @forelse($promotionSantris as $santri)
                            <label class="flex items-center space-x-3 px-4 py-2.5 hover:bg-white rounded-lg transition cursor-pointer select-none">
                                <input type="checkbox" wire:model="selectedSantriIds" value="{{ $santri->id }}" class="rounded text-emerald-700 focus:ring-emerald-500 border-slate-300 w-4 h-4">
                                <span class="text-sm font-semibold text-slate-700">{{ $santri->nama_lengkap }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-slate-600 font-medium capitalize">{{ $santri->status }}</span>
                            </label>
                        @empty
                            <p class="p-6 text-center text-xs text-slate-400 italic">Tidak ada santri yang dapat dipromosikan di kelas asal.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Promotion Trigger -->
            <button onclick="confirm('Apakah Anda yakin ingin memproses kenaikan kelas massal untuk santri yang dipilih?') || event.stopImmediatePropagation()" wire:click="promoteMassal" 
                    class="w-full mt-6 bg-emerald-800 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-800/10 transition duration-200 flex items-center justify-center gap-2 cursor-pointer">
                <span>🚀</span> <span>Jalankan Batch Kenaikan Kelas</span>
            </button>
        </div>
    </div>

    <!-- Class CRUD Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden border border-slate-100">
                <!-- Modal Header -->
                <div class="bg-gradient-to-br from-emerald-800 to-emerald-900 px-6 py-4 text-white flex items-center justify-between">
                    <h3 class="font-bold text-lg">{{ $isEditMode ? 'Ubah Nama Kelas' : 'Tambah Kelas Baru' }}</h3>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white text-xl cursor-pointer">&times;</button>
                </div>

                <!-- Modal Body Form -->
                <form wire:submit.prevent="saveKelas" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Kelas</label>
                        <input wire:model="nama_kelas" type="text" placeholder="Contoh: Kelas E" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600" required>
                        @error('nama_kelas') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
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
