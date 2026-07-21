<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MerchantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - AeronPay Merchant Onboarding & Gift Card SaaS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Role Switcher
Route::post('/switch-role', [AuthController::class, 'switchRole'])->name('switch.role');

// Super Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/onboard', [AdminController::class, 'onboardMerchant'])->name('admin.onboard');
    Route::post('/validate-otp', [AdminController::class, 'validateMerchantOtp'])->name('admin.validate_otp');
    Route::post('/wallet/add', [AdminController::class, 'addWalletBalance'])->name('admin.wallet.add');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
});

// Merchant Reseller Routes
Route::prefix('merchant')->group(function () {
    Route::get('/', [MerchantController::class, 'index'])->name('merchant.dashboard');
    Route::post('/switch', [MerchantController::class, 'switchMerchant'])->name('merchant.switch');
    Route::post('/purchase', [MerchantController::class, 'purchase'])->name('merchant.purchase');
    Route::get('/receipt/{id}', [MerchantController::class, 'getReceipt'])->name('merchant.receipt');
});
