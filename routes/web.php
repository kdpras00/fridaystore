<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Authenticated
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:admin,kasir,owner')
        ->name('dashboard');

    // Admin only
    Route::middleware('role:admin')->group(function () {
        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');

        // Kategori
        Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
        Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
        Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

        // Produk
        Route::resource('produk', ProdukController::class)->except(['show']);

        // Stok (admin manage)
        Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
        Route::post('/stok/restock', [StokController::class, 'restock'])->name('stok.restock');
        Route::get('/stok/riwayat', [StokController::class, 'riwayat'])->name('stok.riwayat');
    });

    // Admin + Owner: Laporan
    Route::middleware('role:admin,owner')->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/produk', [LaporanController::class, 'produk'])->name('laporan.produk');
        Route::get('/laporan/kasir', [LaporanController::class, 'kasir'])->name('laporan.kasir');
        Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
    });

    // Owner: Stok (read only)
    Route::middleware('role:owner')->group(function () {
        Route::get('/owner/stok', [StokController::class, 'ownerIndex'])->name('owner.stok');
    });

    // Kasir
    Route::middleware('role:kasir')->group(function () {
        Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
        Route::post('/kasir/transaksi', [KasirController::class, 'store'])->name('kasir.store');
        Route::get('/kasir/struk/{transaksi}', [KasirController::class, 'struk'])->name('kasir.struk');
    });
});
