<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DiagnosaAdminController;
use App\Http\Controllers\Admin\GejalaController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PenyakitController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DiagnosaController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/tentang', [LandingController::class, 'tentang'])->name('tentang');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| User Routes (Setelah Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('landing')->with('success', 'Selamat datang, ' . $user->name . '!');
    })->name('home');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Diagnosa Routes
    Route::prefix('diagnosa')->name('diagnosa.')->group(function () {
        Route::get('/', [DiagnosaController::class, 'index'])->name('index');
        Route::post('/', [DiagnosaController::class, 'store'])->name('store');
        Route::get('/hasil/{id}', [DiagnosaController::class, 'hasil'])->name('hasil');
        Route::get('/riwayat', [DiagnosaController::class, 'riwayat'])->name('riwayat');
        Route::get('/statistik', [DiagnosaController::class, 'statistik'])->name('statistik');
        Route::get('/export-pdf/{id}', [DiagnosaController::class, 'exportPDF'])->name('export.pdf');
        Route::get('/detail/{id}', [DiagnosaController::class, 'show'])->name('show');
        Route::delete('/{id}', [DiagnosaController::class, 'destroy'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Management Data
    Route::resource('penyakit', PenyakitController::class);
    Route::resource('gejala', GejalaController::class);

    // Management Diagnosa
    Route::get('/diagnosa', [DiagnosaAdminController::class, 'index'])->name('diagnosa.index');
    Route::get('/diagnosa/{id}', [DiagnosaAdminController::class, 'show'])->name('diagnosa.show');
    Route::delete('/diagnosa/{id}', [DiagnosaAdminController::class, 'destroy'])->name('diagnosa.destroy');

    // Laporan Routes
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/cetak', [LaporanController::class, 'cetak'])->name('cetak');
        Route::get('/detail', [LaporanController::class, 'detail'])->name('detail');
        Route::get('/performa', [LaporanController::class, 'statistikPerforma'])->name('performa');
        Route::get('/export-excel', [LaporanController::class, 'exportExcel'])->name('export-excel');
    });

    // Statistik
    Route::get('/statistik', [AdminController::class, 'statistik'])->name('statistik');

    // User Management
    Route::get('/pengguna', [AdminController::class, 'kelolaPengguna'])->name('pengguna.index');
    Route::put('/pengguna/{id}/status', [AdminController::class, 'updateStatusPengguna'])->name('pengguna.update-status');
    Route::delete('/pengguna/{id}', [AdminController::class, 'hapusPengguna'])->name('pengguna.destroy');

    // Aturan Management
    // Route::get('/aturan', [AdminController::class, 'kelolaAturan'])->name('aturan.index');
    // Route::post('/aturan', [AdminController::class, 'simpanAturan'])->name('aturan.store');
    // Route::put('/aturan/{id}', [AdminController::class, 'updateAturan'])->name('aturan.update');
    // Route::delete('/aturan/{id}', [AdminController::class, 'hapusAturan'])->name('aturan.destroy');
        // Aturan Routes
     Route::get('/aturan', [AdminController::class, 'kelolaAturan'])->name('kelola-aturan');
    Route::get('/aturan/create', [AdminController::class, 'createAturan'])->name('aturan.create');
    Route::post('/aturan/simpan', [AdminController::class, 'simpanAturan'])->name('simpan-aturan');
    Route::get('/aturan/{id}/edit', [AdminController::class, 'editAturan'])->name('aturan.edit');
    Route::put('/aturan/update/{id}', [AdminController::class, 'updateAturan'])->name('update-aturan');
    Route::delete('/aturan/hapus/{id}', [AdminController::class, 'hapusAturan'])->name('hapus-aturan');
});



/*
|--------------------------------------------------------------------------
| Fallback Route
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return redirect()->route('landing');
});
