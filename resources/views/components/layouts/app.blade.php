<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIM-PONDOK' }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via Vite/Laravel asset bundling) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 flex flex-col md:flex-row" x-data="{ mobileSidebarOpen: false }">

    @auth
        <!-- Backdrop for mobile sidebar -->
        <div x-show="mobileSidebarOpen" 
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 md:hidden"
             @click="mobileSidebarOpen = false"
             style="display: none;"></div>

        <!-- Mobile Header Bar -->
        <div class="bg-emerald-950 text-white px-6 py-4 flex items-center justify-between md:hidden border-b border-emerald-900 shadow-sm shrink-0">
            <div class="flex items-center space-x-3">
                <button @click="mobileSidebarOpen = true" class="text-emerald-100 hover:text-white focus:outline-none p-1.5 rounded-lg hover:bg-emerald-900 transition duration-200 cursor-pointer" aria-label="Buka Menu">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <div class="flex items-center space-x-2">
                    <span class="text-xl">🕌</span>
                    <span class="font-bold text-base leading-none tracking-wide text-white">SIM-PONDOK</span>
                </div>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <aside :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-emerald-950 text-emerald-100 flex flex-col border-r border-emerald-900 shrink-0 transform transition-transform duration-300 ease-in-out md:static md:translate-x-0 md:z-auto">
            <!-- Sidebar Header / Logo -->
            <div class="p-6 border-b border-emerald-900 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">🕌</span>
                    <div>
                        <h1 class="font-bold text-lg leading-tight tracking-wide text-white">SIM-PONDOK</h1>
                        <span class="text-xs text-emerald-400 font-medium">Sistem Informasi</span>
                    </div>
                </div>
                <!-- Close Button (Mobile Only) -->
                <button @click="mobileSidebarOpen = false" class="md:hidden text-emerald-400 hover:text-white focus:outline-none p-1 rounded-md cursor-pointer transition-colors duration-200" aria-label="Tutup Menu">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Profile Info (Desktop) -->
            <div class="p-4 mx-4 my-3 bg-emerald-900/40 rounded-xl border border-emerald-800/50 hidden md:block">
                <p class="text-xs text-emerald-400 font-medium uppercase tracking-wider">Aktor Aktif</p>
                <h2 class="font-semibold text-white text-sm truncate mt-1">{{ auth()->user()->name }}</h2>
                <div class="flex items-center space-x-1.5 mt-1.5">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs text-emerald-300 font-medium capitalize">{{ auth()->user()->role }}</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-3 space-y-1 overflow-y-auto">
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-800 text-white font-semibold shadow-md shadow-emerald-950/50' : 'hover:bg-emerald-900 text-emerald-300 hover:text-emerald-100' }}">
                        <span>📊</span> <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.santri') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition duration-200 {{ request()->routeIs('admin.santri') ? 'bg-emerald-800 text-white font-semibold shadow-md shadow-emerald-950/50' : 'hover:bg-emerald-900 text-emerald-300 hover:text-emerald-100' }}">
                        <span>🎓</span> <span>Data Santri</span>
                    </a>
                    <a href="{{ route('admin.kelas') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition duration-200 {{ request()->routeIs('admin.kelas') ? 'bg-emerald-800 text-white font-semibold shadow-md shadow-emerald-950/50' : 'hover:bg-emerald-900 text-emerald-300 hover:text-emerald-100' }}">
                        <span>🏫</span> <span>Data Kelas</span>
                    </a>
                    <a href="{{ route('admin.kalender') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition duration-200 {{ request()->routeIs('admin.kalender') ? 'bg-emerald-800 text-white font-semibold shadow-md shadow-emerald-950/50' : 'hover:bg-emerald-900 text-emerald-300 hover:text-emerald-100' }}">
                        <span>📅</span> <span>Kalender & Hilal</span>
                    </a>
                @elseif(auth()->user()->role === 'ustaz')
                    <a href="{{ route('ustaz.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition duration-200 {{ request()->routeIs('ustaz.dashboard') ? 'bg-emerald-800 text-white font-semibold shadow-md shadow-emerald-950/50' : 'hover:bg-emerald-900 text-emerald-300 hover:text-emerald-100' }}">
                        <span>📱</span> <span>Dashboard Ustaz</span>
                    </a>
                    <a href="{{ route('ustaz.presensi') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition duration-200 {{ request()->routeIs('ustaz.presensi') ? 'bg-emerald-800 text-white font-semibold shadow-md shadow-emerald-950/50' : 'hover:bg-emerald-900 text-emerald-300 hover:text-emerald-100' }}">
                        <span>📝</span> <span>Presensi Harian</span>
                    </a>
                    <a href="{{ route('ustaz.hafalan') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition duration-200 {{ request()->routeIs('ustaz.hafalan') ? 'bg-emerald-800 text-white font-semibold shadow-md shadow-emerald-950/50' : 'hover:bg-emerald-900 text-emerald-300 hover:text-emerald-100' }}">
                        <span>📖</span> <span>Input Hafalan</span>
                    </a>
                @elseif(auth()->user()->role === 'bendahara')
                    <a href="{{ route('bendahara.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition duration-200 {{ request()->routeIs('bendahara.dashboard') ? 'bg-emerald-800 text-white font-semibold shadow-md shadow-emerald-950/50' : 'hover:bg-emerald-900 text-emerald-300 hover:text-emerald-100' }}">
                        <span>💸</span> <span>Dashboard Bendahara</span>
                    </a>
                    <a href="{{ route('bendahara.keuangan') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition duration-200 {{ request()->routeIs('bendahara.keuangan') ? 'bg-emerald-800 text-white font-semibold shadow-md shadow-emerald-950/50' : 'hover:bg-emerald-900 text-emerald-300 hover:text-emerald-100' }}">
                        <span>💳</span> <span>Kelola Keuangan</span>
                    </a>
                @endif
            </nav>

            <!-- Bottom Area / Logout Button -->
            <div class="p-4 border-t border-emerald-900 flex flex-col space-y-2">
                <!-- Mobile Logout/Profile indicator -->
                <div class="flex items-center justify-between md:hidden pb-2 mb-2 border-b border-emerald-900">
                    <div class="text-xs">
                        <p class="font-bold text-white">{{ auth()->user()->name }}</p>
                        <p class="text-emerald-400 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-2 bg-emerald-900 hover:bg-red-800 text-emerald-100 hover:text-white px-4 py-2.5 rounded-lg text-xs font-semibold tracking-wider uppercase transition duration-200 border border-emerald-800/80 hover:border-red-900 cursor-pointer">
                        <span>🚪</span> <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>
    @endauth

    <!-- Main Content Container -->
    <main class="flex-1 flex flex-col min-w-0">
        @auth
            <!-- Topbar Header -->
            <header class="bg-white border-b border-slate-200 px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
                @if(request()->routeIs('*dashboard'))
                <div>
                    <h2 class="text-sm font-medium text-slate-500">Selamat datang kembali,</h2>
                    <h1 class="text-xl font-bold text-slate-800 flex items-center gap-1.5 mt-0.5">
                        {{ auth()->user()->name }} 
                        <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-semibold capitalize">{{ auth()->user()->role }}</span>
                    </h1>
                </div>
                @endif
                <!-- Dynamic Hijri Clock Header Widget -->
                <div class="flex items-center space-x-3 bg-emerald-50/80 px-4 py-2.5 rounded-xl border border-emerald-100 shadow-sm shrink-0">
                    <span class="text-xl">📅</span>
                    <div class="text-left">
                        <p class="text-xs font-semibold text-emerald-800 uppercase tracking-wide">Hari Ini (Hijriah)</p>
                        <p class="text-sm font-bold text-emerald-950">
                            {{ \App\Helpers\HijriHelper::gregorianToHijri(date('Y-m-d'))['formatted'] }}
                        </p>
                    </div>
                </div>
            </header>
        @endauth

        <!-- Render View Component -->
        <div class="flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </div>
    </main>

</body>
</html>
