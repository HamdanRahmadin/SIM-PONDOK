<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\SantriManager;
use App\Livewire\Admin\KelasManager;
use App\Livewire\Admin\KalenderManager;
use App\Livewire\Ustaz\Dashboard as UstazDashboard;
use App\Livewire\Ustaz\PresensiForm;
use App\Livewire\Ustaz\HafalanForm;
use App\Livewire\Bendahara\Dashboard as BendaharaDashboard;
use App\Livewire\Bendahara\KeuanganManager;
use Illuminate\Support\Facades\Auth;

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
    if (!Auth::check()) {
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
        Route::get('/kalender', KalenderManager::class)->name('admin.kalender');
    });

    // Ustaz Routes
    Route::middleware(['role:ustaz'])->prefix('ustaz')->group(function () {
        Route::get('/dashboard', UstazDashboard::class)->name('ustaz.dashboard');
        Route::get('/presensi', PresensiForm::class)->name('ustaz.presensi');
        Route::get('/hafalan', HafalanForm::class)->name('ustaz.hafalan');
    });

    // Bendahara Routes
    Route::middleware(['role:bendahara'])->prefix('bendahara')->group(function () {
        Route::get('/dashboard', BendaharaDashboard::class)->name('bendahara.dashboard');
        Route::get('/keuangan', KeuanganManager::class)->name('bendahara.keuangan');
    });
    
});
