<?php

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\KalenderManager;
use App\Livewire\Admin\KamarManager;
use App\Livewire\Admin\KelasManager;
use App\Livewire\Admin\SantriManager;
use App\Livewire\Auth\Login;
use App\Livewire\Bendahara\Dashboard as BendaharaDashboard;
use App\Livewire\Bendahara\KeuanganManager;
use App\Livewire\Ustaz\Dashboard as UstazDashboard;
use App\Livewire\Ustaz\PresensiHalaqoh;
use App\Livewire\Ustaz\PresensiSetoran;
use App\Livewire\Ustaz\RiwayatPresensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public Route
Route::get('/login', Login::class)->name('login');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

// Root redirection
Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }
    $role = Auth::user()->role;

    return redirect()->to("/{$role}/dashboard");
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('admin.dashboard');
        Route::get('/santri', SantriManager::class)->name('admin.santri');
        Route::get('/kelas', KelasManager::class)->name('admin.kelas');
        Route::get('/kamar', KamarManager::class)->name('admin.kamar');
        Route::get('/kalender', KalenderManager::class)->name('admin.kalender');
    });

    // Ustaz Routes
    Route::middleware(['role:ustaz'])->prefix('ustaz')->group(function () {
        Route::get('/dashboard', UstazDashboard::class)->name('ustaz.dashboard');
        Route::get('/presensi-setoran', PresensiSetoran::class)->name('ustaz.presensi_setoran');
        Route::get('/presensi-halaqoh', PresensiHalaqoh::class)->name('ustaz.presensi_halaqoh');
        Route::get('/riwayat-presensi', RiwayatPresensi::class)->name('ustaz.riwayat_presensi');
    });

    // Bendahara Routes
    Route::middleware(['role:bendahara'])->prefix('bendahara')->group(function () {
        Route::get('/dashboard', BendaharaDashboard::class)->name('bendahara.dashboard');
        Route::get('/keuangan', KeuanganManager::class)->name('bendahara.keuangan');
    });

});
