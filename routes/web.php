<?php

use Illuminate\Support\Facades\Route;
use App\Models\Barang;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;

/*
|--------------------------------------------------------------------------
| Public Routes (Halaman Depan)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Ambil 2 barang dengan stok terbanyak
    $stok_terbanyak = Barang::orderBy('stok', 'desc')->take(2)->get();

    // Ambil 1 barang dengan stok paling sedikit (untuk indikator menipis)
    $stok_terendah = Barang::orderBy('stok', 'asc')->first();

    // Hitung total jenis barang untuk statistik
    $total_jenis = Barang::count();

    // Pastikan nama file blade kamu benar (welcome atau home)
    return view('home', compact('stok_terbanyak', 'stok_terendah', 'total_jenis'));
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Harus Login)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // 1. DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tambahkan rute PROFIL di sini agar link di dashboard tidak error
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. BARANG (Lengkap: Index, Store, Update, Destroy)
    Route::get('barang/datatable', [BarangController::class, 'datatable'])->name('barang.datatable');
    Route::resource('barang', BarangController::class);

    // 3. BARANG MASUK (Hanya Admin)
    Route::get('barang_masuk/datatable', [BarangMasukController::class, 'datatable'])->name('barang_masuk.datatable')->middleware('admin');
    Route::resource('barang_masuk', BarangMasukController::class)->middleware('admin');

    // 4. BARANG KELUAR (User mengajukan, Admin approve/reject)
    Route::get('barang_keluar/datatable', [BarangKeluarController::class, 'datatable'])->name('barang_keluar.datatable');
    Route::resource('barang_keluar', BarangKeluarController::class);

    // 6. ROLE MANAJEMEN (Hanya Admin)
    Route::get('roles/datatable', [\App\Http\Controllers\RoleController::class, 'datatable'])
        ->name('roles.datatable')
        ->middleware('can:view,App\Models\Role');

    Route::resource('roles', \App\Http\Controllers\RoleController::class)
        ->middlewareFor(['index'], 'can:view,App\Models\Role')
        ->middlewareFor(['show'], 'can:view,role')
        ->middlewareFor(['create', 'store'], 'can:create,App\Models\Role')
        ->middlewareFor(['edit', 'update'], 'can:update,role')
        ->middlewareFor(['destroy'], 'can:delete,role');

    // 7. USER MANAJEMEN (Hanya Admin)
    Route::get('users/datatable', [\App\Http\Controllers\UserController::class, 'datatable'])
        ->name('users.datatable')
        ->middleware('can:view,App\Models\User');
    Route::patch('users/{user}/status', [\App\Http\Controllers\UserController::class, 'updateStatus'])
        ->name('users.status')
        ->middleware('can:update,user');

    Route::resource('users', \App\Http\Controllers\UserController::class)
        ->middlewareFor(['index'], 'can:view,App\Models\User')
        ->middlewareFor(['show'], 'can:view,user')
        ->middlewareFor(['create', 'store'], 'can:create,App\Models\User')
        ->middlewareFor(['edit', 'update'], 'can:update,user')
        ->middlewareFor(['destroy'], 'can:delete,user');

    // 8. KHUSUS ADMIN (Approve & Reject)
    Route::middleware('admin')->group(function () {
        Route::post('/barang_keluar/{id}/approve', [BarangKeluarController::class, 'approve'])
            ->name('barang_keluar.approve');

        Route::post('/barang_keluar/{id}/reject', [BarangKeluarController::class, 'reject'])
            ->name('barang_keluar.reject');
    });
});

require __DIR__ . '/auth.php';
