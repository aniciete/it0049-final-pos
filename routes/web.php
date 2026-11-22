<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes — Bright-Market POS (Laravel MVC)
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');

    Route::post('/products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::resource('products', ProductController::class);

    Route::resource('customers', CustomerController::class);
    Route::resource('users', UserController::class);

    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Serve uploaded product images & avatars from Storage::disk('public') when public/storage symlink is unavailable
    Route::get('/media/{folder}/{filename}', function (string $folder, string $filename) {
        $safeName = basename($filename);
        $path = $folder.'/'.$safeName;

        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    })
        ->whereIn('folder', ['avatars', 'products'])
        ->where('filename', '[A-Za-z0-9._-]+')
        ->name('media.show');
});

require __DIR__.'/auth.php';
