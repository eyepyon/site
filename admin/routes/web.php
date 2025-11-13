<?php

use App\Http\Controllers\AdminListingController;
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// ログインページ
Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [AdminLoginController::class, 'login']);

// 認証が必要なルート
Route::middleware(['auth:admin'])->group(function () {
    // ダッシュボード
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // ログアウト
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
    
    // ユーザー管理
    Route::prefix('users')->name('admin.users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
    });
    
    // 出品管理
    Route::prefix('listings')->name('admin.listings.')->group(function () {
        Route::get('/', [AdminListingController::class, 'index'])->name('index');
        Route::get('/{listing}', [AdminListingController::class, 'show'])->name('show');
        Route::patch('/{listing}/status', [AdminListingController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{listing}', [AdminListingController::class, 'destroy'])->name('destroy');
    });
    
    // 取引管理
    Route::prefix('transactions')->name('admin.transactions.')->group(function () {
        Route::get('/', [AdminTransactionController::class, 'index'])->name('index');
        Route::get('/{transaction}', [AdminTransactionController::class, 'show'])->name('show');
        Route::patch('/{transaction}/status', [AdminTransactionController::class, 'updateStatus'])->name('update-status');
    });
});
