<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\IdentifyShop;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

$appHost = parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST);

// -----------------------------------------------------------------------------
// 1. Tenant / Shop Routes (Custom Domains & CNAME)
// -----------------------------------------------------------------------------
// Match ANY domain except localhost and 127.0.0.1
// This ensures that accessing the site via a custom domain hits the shop logic first.
Route::domain('{domain}')
    ->where(['domain' => '^(?!(localhost|127\.0\.0\.1)$).*$'])
    ->group(function () {
        Route::middleware([IdentifyShop::class])->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('shop.index');
            Route::get('/slots', [BookingController::class, 'slots'])->name('shop.slots');
            Route::post('/book', [BookingController::class, 'store'])->name('shop.book');
            Route::get('/kiosk', [App\Http\Controllers\KioskController::class, 'show'])->name('shop.kiosk');
        });
    });

// -----------------------------------------------------------------------------
// 2. Platform / Admin Routes (Catch-all / Localhost)
// -----------------------------------------------------------------------------
// These routes do NOT possess a domain constraint parameter, preventing the UrlGenerationException.
Route::get('/', function () {
    return view('welcome'); 
})->name('home');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Panel
Route::middleware(['auth', \App\Http\Middleware\CheckTrialExpiry::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/trial-expired', function() {
        return view('admin.trial_expired');
    })->name('trial_expired');
    
    // Shop Management
    Route::get('/shop', [AdminController::class, 'editShop'])->name('shop.edit');
    Route::put('/shop', [AdminController::class, 'updateShop'])->name('shop.update');
    Route::post('/shop', [AdminController::class, 'storeShop'])->name('shop.store');
    
    // Services
    Route::resource('services', AdminController::class); 

    // Availability
    Route::get('/availability', [App\Http\Controllers\AvailabilityController::class, 'index'])->name('availability.index');
    Route::put('/availability', [App\Http\Controllers\AvailabilityController::class, 'update'])->name('availability.update');

    // Customers
    Route::resource('customers', App\Http\Controllers\CustomerController::class)->only(['index', 'show']);

    // Appointments
    Route::resource('appointments', App\Http\Controllers\AppointmentController::class)->only(['index', 'update', 'destroy']);

    // POS / Quick Reservation
    Route::get('/pos', [App\Http\Controllers\PointOfSaleController::class, 'index'])->name('pos.index');
    Route::post('/pos', [App\Http\Controllers\PointOfSaleController::class, 'store'])->name('pos.store');

    // Stylists
    Route::resource('stylists', App\Http\Controllers\Admin\StylistController::class);
});

// Super Admin Routes
Route::prefix('super-admin')->name('super_admin.')->group(function () {
    Route::get('/login', [App\Http\Controllers\SuperAdminController::class, 'login'])->name('login');
    Route::post('/login', [App\Http\Controllers\SuperAdminController::class, 'authenticate'])->name('authenticate');
    Route::post('/logout', [App\Http\Controllers\SuperAdminController::class, 'logout'])->name('logout');
    
    Route::middleware([\App\Http\Middleware\SuperAdminAuth::class])->group(function () {
        Route::get('/', [App\Http\Controllers\SuperAdminController::class, 'index'])->name('index');
        Route::post('/users/{user}/update-expiry', [App\Http\Controllers\SuperAdminController::class, 'updateExpiry'])->name('update_expiry');
        Route::post('/users/{user}/change-password', [App\Http\Controllers\SuperAdminController::class, 'changePassword'])->name('change_password');
    });
});

// Direct Booking Link via Path (for testing or non-CNAME usage)
Route::get('/book/{slug}', [BookingController::class, 'index'])->name('booking.via_slug');
Route::get('/book/{slug}/slots', [BookingController::class, 'slots']);
Route::post('/book/{slug}', [BookingController::class, 'store']);
Route::get('/book/{slug}/kiosk', [App\Http\Controllers\KioskController::class, 'show'])->name('booking.kiosk');
