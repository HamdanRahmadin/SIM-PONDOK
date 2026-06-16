<div class="w-full max-w-md">
    <!-- Outer Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-emerald-900/10 border border-slate-100 overflow-hidden">
        <!-- Brand/Header Accent -->
        <div class="bg-gradient-to-br from-emerald-800 to-emerald-950 px-8 py-10 text-center text-white relative">
            <!-- Islamic decorative badge background (pure CSS) -->
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
            
            <div class="relative z-10 flex flex-col items-center">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="w-16 h-16 rounded-2xl object-cover mb-3 border-2 border-white/20 shadow-md">
                <h2 class="text-2xl font-bold tracking-wide">RIBATHUL QUR'AN</h2>
                <p class="text-emerald-200 text-xs mt-1 font-medium tracking-wider uppercase font-sans">Sistem Informasi Manajemen</p>
            </div>
        </div>

        <!-- Form Body -->
        <form wire:submit="login" class="p-8 space-y-6">
            @csrf
            
            <!-- Email Field -->
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Alamat Email</label>
                <div class="mt-2 relative rounded-md shadow-sm">
                    <input wire:model="email" type="email" id="email" 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 transition duration-200" 
                           placeholder="nama@pondok.com" required>
                </div>
                @error('email')
                    <span class="text-red-600 text-xs mt-1.5 block font-medium inline-flex items-center gap-1"><x-lucide-alert-triangle class="w-3.5 h-3.5" /> {{ $message }}</span>
                @enderror
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Kata Sandi</label>
                <div class="mt-2 relative rounded-md shadow-sm">
                    <input wire:model="password" type="password" id="password" 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 transition duration-200" 
                           placeholder="••••••••" required>
                </div>
                @error('password')
                    <span class="text-red-600 text-xs mt-1.5 block font-medium inline-flex items-center gap-1"><x-lucide-alert-triangle class="w-3.5 h-3.5" /> {{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" wire:loading.attr="disabled" wire:target="login" 
                    class="w-full bg-emerald-800 hover:bg-emerald-700 text-white font-semibold py-3.5 rounded-xl shadow-lg shadow-emerald-800/20 hover:shadow-emerald-700/30 transition duration-250 transform active:scale-[0.98] flex items-center justify-center cursor-pointer disabled:opacity-50">
                <span wire:loading.remove wire:target="login">Masuk</span>
                <span wire:loading wire:target="login" class="flex items-center gap-2"><x-lucide-loader-circle class="w-4 h-4 animate-spin" /> Memproses...</span>
            </button>
        </form>
    </div>
</div>
