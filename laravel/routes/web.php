<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// 公開ページ（認証不要）
Route::get('/', [ListingController::class, 'index'])->name('home');
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');

// 認証関連
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 認証が必要なページ
Route::middleware(['auth'])->group(function () {
    // 出品詳細（認証後のみ閲覧可能）
    Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');
    
    // 出品管理
    Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
    Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{listing}', [ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');
    
    // 決済
    Route::get('/listings/{listing}/checkout', [PaymentController::class, 'checkout'])
        ->name('listings.checkout');
    Route::post('/listings/{listing}/payment', [PaymentController::class, 'process'])
        ->name('listings.payment');
    Route::get('/setup-intent', [PaymentController::class, 'setupIntent'])
        ->name('setup-intent');
    
    // 取引管理
    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions.index');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])
        ->name('transactions.show');
    Route::post('/transactions/{transaction}/release', [TransactionController::class, 'release'])
        ->name('transactions.release');
});
