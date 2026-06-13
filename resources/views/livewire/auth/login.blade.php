<div class="w-full max-w-md">
    <!-- Outer Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-emerald-900/10 border border-slate-100 overflow-hidden">
        <!-- Brand/Header Accent -->
        <div class="bg-gradient-to-br from-emerald-800 to-emerald-950 px-8 py-10 text-center text-white relative">
            <!-- Islamic decorative badge background (pure CSS) -->
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
            
            <div class="relative z-10 flex flex-col items-center">
                <span class="text-4xl mb-3">🕌</span>
                <h2 class="text-2xl font-bold tracking-wide">SIM-PONDOK</h2>
                <p class="text-emerald-200 text-xs mt-1 font-medium tracking-wider uppercase">Sistem Informasi Manajemen Pondok</p>
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
                    <span class="text-red-600 text-xs mt-1.5 block font-medium">⚠️ {{ $message }}</span>
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
                    <span class="text-red-600 text-xs mt-1.5 block font-medium">⚠️ {{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-emerald-800 hover:bg-emerald-700 text-white font-semibold py-3.5 rounded-xl shadow-lg shadow-emerald-800/20 hover:shadow-emerald-700/30 transition duration-250 transform active:scale-[0.98] flex items-center justify-center space-x-2 cursor-pointer">
                <span>🔐</span> <span>Masuk Aplikasi</span>
            </button>
        </form>
    </div>

    <!-- Dev Info / Credentials Helper Card -->
    <div class="mt-6 bg-emerald-50 border border-emerald-100 rounded-2xl p-5 shadow-sm text-slate-700">
        <h3 class="text-xs font-bold text-emerald-950 flex items-center space-x-1.5 mb-3">
            <span>💡</span> <span>Akun Demo Untuk Uji Coba:</span>
        </h3>
        <div class="grid grid-cols-1 gap-2 text-xs">
            <div class="bg-white/80 p-2.5 rounded-lg border border-emerald-100 flex justify-between items-center">
                <div>
                    <span class="font-bold text-slate-800">Admin</span>: <code class="text-emerald-800 font-mono">admin@pondok.com</code>
                </div>
                <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">Password: password</span>
            </div>
            <div class="bg-white/80 p-2.5 rounded-lg border border-emerald-100 flex justify-between items-center">
                <div>
                    <span class="font-bold text-slate-800">Ustaz</span>: <code class="text-emerald-800 font-mono">ustaz@pondok.com</code>
                </div>
                <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">Password: password</span>
            </div>
            <div class="bg-white/80 p-2.5 rounded-lg border border-emerald-100 flex justify-between items-center">
                <div>
                    <span class="font-bold text-slate-800">Bendahara</span>: <code class="text-emerald-800 font-mono">bendahara@pondok.com</code>
                </div>
                <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">Password: password</span>
            </div>
        </div>
    </div>
</div>
