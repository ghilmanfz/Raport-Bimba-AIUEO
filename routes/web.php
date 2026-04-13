<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MuridController;
use App\Http\Controllers\Admin\GuruController as AdminGuruController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\NilaiController;
use App\Http\Controllers\Guru\GrafikController;
use App\Http\Controllers\Guru\RaporController as GuruRaporController;
use App\Http\Controllers\Wali\DashboardController as WaliDashboardController;
use App\Http\Controllers\Wali\RaporController as WaliRaporController;
use App\Http\Controllers\RaporDownloadController;

use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public route: QR code scan → auto download rapor PDF
Route::get('/rapor/download/{token}', [RaporDownloadController::class, 'download'])->name('rapor.download');

/*
|--------------------------------------------------------------------------
| Notification Routes (all authenticated users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/murid', [MuridController::class, 'index'])->name('murid');
    Route::post('/murid', [MuridController::class, 'store'])->name('murid.store');
    Route::put('/murid/{student}', [MuridController::class, 'update'])->name('murid.update');
    Route::delete('/murid/{student}', [MuridController::class, 'destroy'])->name('murid.destroy');

    Route::get('/guru', [AdminGuruController::class, 'index'])->name('guru');
    Route::get('/guru/export', [AdminGuruController::class, 'export'])->name('guru.export');
    Route::post('/guru/import', [AdminGuruController::class, 'import'])->name('guru.import');
    Route::post('/guru', [AdminGuruController::class, 'store'])->name('guru.store');
    Route::put('/guru/{teacher}', [AdminGuruController::class, 'update'])->name('guru.update');
    Route::delete('/guru/{teacher}', [AdminGuruController::class, 'destroy'])->name('guru.destroy');

    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
    Route::put('/pengaturan', [PengaturanController::class, 'updateSettings'])->name('pengaturan.update');
    Route::put('/pengaturan/password', [PengaturanController::class, 'updatePassword'])->name('pengaturan.password');
    Route::post('/pengaturan/kelas', [PengaturanController::class, 'storeClassroom'])->name('pengaturan.kelas.store');
});

/*
|--------------------------------------------------------------------------
| Guru Routes
|--------------------------------------------------------------------------
*/
Route::prefix('guru')->name('guru.')->middleware(['auth', 'role:guru'])->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');

    Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai');
    Route::post('/nilai', [NilaiController::class, 'store'])->name('nilai.store');

    Route::get('/grafik', [GrafikController::class, 'index'])->name('grafik');

    Route::get('/rapor', [GuruRaporController::class, 'index'])->name('rapor');
});

/*
|--------------------------------------------------------------------------
| Wali Murid Routes
|--------------------------------------------------------------------------
*/
Route::prefix('wali')->name('wali.')->middleware(['auth', 'role:wali'])->group(function () {
    Route::get('/dashboard', [WaliDashboardController::class, 'index'])->name('dashboard');
    Route::get('/rapor', [WaliRaporController::class, 'index'])->name('rapor');
});
