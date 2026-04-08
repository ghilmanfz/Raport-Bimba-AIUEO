<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    // ⚠️ BYPASS LOGIN — hanya untuk testing UI, hapus saat production
    $role = $request->input('role');

    return match($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'guru'  => redirect()->route('guru.dashboard'),
        'wali'  => redirect()->route('wali.dashboard'),
        default => back()->withErrors(['role' => 'Pilih peran terlebih dahulu.']),
    };
})->name('login.post');

Route::post('/logout', function () {
    return redirect()->route('login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/murid', function () {
        return view('admin.murid');
    })->name('murid');

    Route::get('/guru', function () {
        return view('admin.guru');
    })->name('guru');

    Route::get('/pengaturan', function () {
        return view('admin.pengaturan');
    })->name('pengaturan');
});

/*
|--------------------------------------------------------------------------
| Guru Routes
|--------------------------------------------------------------------------
*/
Route::prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', function () {
        return view('guru.dashboard');
    })->name('dashboard');

    Route::get('/nilai', function () {
        return view('guru.nilai');
    })->name('nilai');

    Route::get('/grafik', function () {
        return view('guru.grafik');
    })->name('grafik');

    Route::get('/rapor', function () {
        return view('guru.rapor');
    })->name('rapor');
});

/*
|--------------------------------------------------------------------------
| Wali Murid Routes
|--------------------------------------------------------------------------
*/
Route::prefix('wali')->name('wali.')->group(function () {
    Route::get('/dashboard', function () {
        return view('wali.dashboard');
    })->name('dashboard');

    Route::get('/rapor', function () {
        return view('wali.rapor');
    })->name('rapor');
});
