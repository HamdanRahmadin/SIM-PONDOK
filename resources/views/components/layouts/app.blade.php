<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? "Ribathul Qur'an" }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#006a3d">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Ribathul Qur'an">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('SW registered.', reg))
                    .catch(err => console.log('SW registration failed.', err));
            });
        }
    </script>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col md:flex-row" x-data="{ mobileSidebarOpen: false }">

    @auth
    <!-- ============================================
         TOP APP BAR (Mobile) 
    ============================================= -->
    <header class="fixed top-0 w-full z-40 backdrop-blur-md bg-surface/90 shadow-sm flex items-center justify-between px-4 h-14 md:hidden">
        <button @click="mobileSidebarOpen = true" aria-label="Buka menu" class="text-primary p-2 -ml-2 rounded-full hover:bg-surface-variant/50 transition-colors duration-200 active:scale-95">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'ustaz' ? route('ustaz.dashboard') : route('bendahara.dashboard')) }}" class="font-bold text-lg text-primary tracking-tight">Ribathul Qur'an</a>
        <div class="w-8 h-8"></div>
    </header>

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="mobileSidebarOpen"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-inverse-surface/50 backdrop-blur-sm z-40 md:hidden"
         @click="mobileSidebarOpen = false"
         style="display: none;"></div>

    <!-- ============================================
         NAVIGATION DRAWER (Desktop always-visible + Mobile slide-in)
    ============================================= -->
    <aside :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-72 bg-surface flex flex-col p-4 shadow-lg border-r border-outline-variant/20 rounded-r-xl transform transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:z-auto md:shadow-none">

        <!-- Brand/Logo Header -->
        <div class="mb-8 pl-2 pr-2 pt-4 flex items-center gap-3">
            <img src="{{ asset('images/logo.jpg') }}" alt="Logo Ribathul Qur'an" class="w-12 h-12 rounded-lg object-cover border border-outline-variant/20 shadow-sm">
            <div class="flex-1 min-w-0">
                <h2 class="font-bold text-base text-primary leading-tight tracking-tight">Ribathul Qur'an</h2>
                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider mt-0.5">Sistem Pondok</p>
            </div>
            <!-- Close Button (Mobile Only) -->
            <button @click="mobileSidebarOpen = false" class="md:hidden text-on-surface-variant hover:text-on-surface p-1 rounded-full hover:bg-surface-variant/50 transition-colors" aria-label="Tutup Menu">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <!-- Navigation Links -->
        <ul class="space-y-1 flex-1 overflow-y-auto">
            @if(auth()->user()->role === 'admin')
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-full transition-all duration-200 font-semibold text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-variant' }}">
                        <span class="material-symbols-outlined {{ request()->routeIs('admin.dashboard') ? 'fill' : '' }}">dashboard</span>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.santri') }}" class="flex items-center gap-4 px-4 py-3 rounded-full transition-all duration-200 font-semibold text-sm {{ request()->routeIs('admin.santri') ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-variant' }}">
                        <span class="material-symbols-outlined">school</span>
                        Data Santri
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kelas') }}" class="flex items-center gap-4 px-4 py-3 rounded-full transition-all duration-200 font-semibold text-sm {{ request()->routeIs('admin.kelas') ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-variant' }}">
                        <span class="material-symbols-outlined">class</span>
                        Data Kelas
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kamar') }}" class="flex items-center gap-4 px-4 py-3 rounded-full transition-all duration-200 font-semibold text-sm {{ request()->routeIs('admin.kamar') ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-variant' }}">
                        <span class="material-symbols-outlined">meeting_room</span>
                        Data Kamar
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kalender') }}" class="flex items-center gap-4 px-4 py-3 rounded-full transition-all duration-200 font-semibold text-sm {{ request()->routeIs('admin.kalender') ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-variant' }}">
                        <span class="material-symbols-outlined">calendar_month</span>
                        Kalender & Hilal
                    </a>
                </li>
            @elseif(auth()->user()->role === 'ustaz')
                <li>
                    <a href="{{ route('ustaz.dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-full transition-all duration-200 font-semibold text-sm {{ request()->routeIs('ustaz.dashboard') ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-variant' }}">
                        <span class="material-symbols-outlined {{ request()->routeIs('ustaz.dashboard') ? 'fill' : '' }}">dashboard</span>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('ustaz.presensi_setoran') }}" class="flex items-center gap-4 px-4 py-3 rounded-full transition-all duration-200 font-semibold text-sm {{ request()->routeIs('ustaz.presensi_setoran') ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-variant' }}">
                        <span class="material-symbols-outlined {{ request()->routeIs('ustaz.presensi_setoran') ? 'fill' : '' }}">menu_book</span>
                        Presensi Setoran
                    </a>
                </li>
                <li>
                    <a href="{{ route('ustaz.presensi_halaqoh') }}" class="flex items-center gap-4 px-4 py-3 rounded-full transition-all duration-200 font-semibold text-sm {{ request()->routeIs('ustaz.presensi_halaqoh') ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-variant' }}">
                        <span class="material-symbols-outlined {{ request()->routeIs('ustaz.presensi_halaqoh') ? 'fill' : '' }}">groups</span>
                        Presensi Halaqoh
                    </a>
                </li>
                <li>
                    <a href="{{ route('ustaz.riwayat_presensi') }}" class="flex items-center gap-4 px-4 py-3 rounded-full transition-all duration-200 font-semibold text-sm {{ request()->routeIs('ustaz.riwayat_presensi') ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-variant' }}">
                        <span class="material-symbols-outlined {{ request()->routeIs('ustaz.riwayat_presensi') ? 'fill' : '' }}">history</span>
                        Riwayat Presensi
                    </a>
                </li>
            @elseif(auth()->user()->role === 'bendahara')
                <li>
                    <a href="{{ route('bendahara.dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-full transition-all duration-200 font-semibold text-sm {{ request()->routeIs('bendahara.dashboard') ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-variant' }}">
                        <span class="material-symbols-outlined {{ request()->routeIs('bendahara.dashboard') ? 'fill' : '' }}">dashboard</span>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('bendahara.keuangan') }}" class="flex items-center gap-4 px-4 py-3 rounded-full transition-all duration-200 font-semibold text-sm {{ request()->routeIs('bendahara.keuangan') ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-variant' }}">
                        <span class="material-symbols-outlined">payments</span>
                        Kelola Keuangan
                    </a>
                </li>
            @endif
        </ul>

        <!-- Bottom Section: Role Info & Logout -->
        <div class="pt-4 border-t border-outline-variant/30 space-y-3">
            {{-- User Role Info --}}
            <div class="px-4 py-2 bg-surface-variant/30 rounded-lg flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-lg">account_circle</span>
                <span class="text-sm font-semibold text-on-surface-variant capitalize">{{ auth()->user()->role }}</span>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-4 px-4 py-3 rounded-full text-on-surface-variant hover:bg-surface-variant transition-colors duration-200 font-semibold text-sm text-left">
                    <span class="material-symbols-outlined">logout</span>
                    Keluar
                </button>
            </form>
        </div>
    </aside>
    @endauth

    <!-- ============================================
         MAIN CONTENT + FOOTER WRAPPER
    ============================================= -->
    <div class="flex-1 flex flex-col min-h-screen md:min-h-0">
        <main class="flex-1 w-full max-w-7xl mx-auto pt-20 md:pt-8 px-4 md:px-6 space-y-4">
            @auth
            <!-- Topbar: Hijri Date Widget (Desktop) -->
            <div class="hidden md:flex items-center justify-between mb-2">
                <div class="flex items-center gap-2 bg-surface-container-lowest rounded-lg px-4 py-2 border border-outline-variant/20 shadow-level-1">
                    <span class="material-symbols-outlined text-primary text-lg">calendar_month</span>
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Hari Ini</p>
                        <p class="text-sm font-bold text-on-surface">
                            {{ app(\App\Services\HijriCalendarService::class)->today()['formatted'] }}
                        </p>
                    </div>
                </div>
            </div>
            @endauth

            {{ $slot }}
        </main>

        <footer class="w-full text-center py-4">
            <p class="text-xs text-on-surface-variant/60">
                &copy; {{ date('Y') }} Ribathul Qur'an. All rights reserved.
            </p>
        </footer>
    </div>

</body>
</html>
