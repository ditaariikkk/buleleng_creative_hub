<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // <-- DITAMBAHKAN

// Import semua controller yang Anda gunakan
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserPageController; // <-- DITAMBAHKAN

// Admin Controllers
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\MentorController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\Admin\LmsController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\LandingPageController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// Rute Autentikasi (login, register, dll.)
Auth::routes();

// --- Rute yang Memerlukan Login ---
Route::middleware(['auth'])->group(function () {

    // Dashboard Utama (ditangani HomeController)
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Edit Profil (untuk complete profile setelah register)
    Route::get('/profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [UserProfileController::class, 'update'])->name('profile.update');

    // Aksi Mentor (pilih/hapus)
    Route::post('/mentor/choose/{mentor}', [HomeController::class, 'chooseMentor'])->name('mentor.choose');
    Route::delete('/mentor/remove', [HomeController::class, 'removeMentor'])->name('mentor.remove');

    // === GRUP RUTE ADMIN ===
    // Anda bisa tambahkan middleware Gate di sini: ->middleware('can:is_admin')
    Route::prefix('admin')->name('admin.')->group(function () {

        // Dashboard Admin (alias untuk /home)
        Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

        // Daftar Peserta (User)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Daftar Admin (dipindahkan ke dalam prefix)
        // URL: /admin/ (index), /admin/ (store), /admin/{admin}/edit (edit), dll.
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::get('/{admin}/edit', [AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{admin}', [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{admin}', [AdminUserController::class, 'destroy'])->name('destroy');

        // CRUD Mentor (Resource)
        Route::resource('mentors', MentorController::class);

        // CRUD Venue (Resource) - Duplikat dihapus
        Route::resource('venues', VenueController::class);

        // CRUD Events (Manual)
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

        // CRUD LMS (Manual)
        Route::get('/lms', [LmsController::class, 'index'])->name('lms.index');
        Route::post('/lms', [LmsController::class, 'store'])->name('lms.store');
        Route::get('/lms/{lmsContent}/edit', [LmsController::class, 'edit'])->name('lms.edit');
        Route::put('/lms/{lmsContent}', [LmsController::class, 'update'])->name('lms.update');
        Route::delete('/lms/{lmsContent}', [LmsController::class, 'destroy'])->name('lms.destroy');
        Route::get('/lms/{lmsContent}', [LmsController::class, 'show'])->name('lms.show');

        // CRUD Products (Manual)
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

        // CRUD News (Manual, via ProductController)
        Route::get('/news', [ProductController::class, 'newsIndex'])->name('news.index');
        Route::post('/news', [ProductController::class, 'newsStore'])->name('news.store');
        Route::get('/news/{news}/edit', [ProductController::class, 'newsEdit'])->name('news.edit');
        Route::put('/news/{news}', [ProductController::class, 'newsUpdate'])->name('news.update');
        Route::delete('/news/{news}', [ProductController::class, 'newsDestroy'])->name('news.destroy');
        Route::get('/news/{news}', [ProductController::class, 'newsShow'])->name('news.show');
    });

    // === GRUP RUTE USER ===
    // Rute ini diperlukan oleh config sidebar 'user'
    // Anda bisa tambahkan middleware Gate di sini: ->middleware('can:is_user')
    Route::prefix('user')->name('user.')->group(function () {

        Route::get('/profile', [UserPageController::class, 'profileIndex'])->name('profile.index');

        Route::patch('/profile/account', [UserPageController::class, 'updateAccount'])->name('profile.updateAccount');
        Route::patch('/profile/details', [UserPageController::class, 'updateDetails'])->name('profile.updateDetails');
        Route::patch('/profile/interests', [UserPageController::class, 'updateInterests'])->name('profile.updateInterests');


        Route::get('/mentors', [UserPageController::class, 'mentorsIndex'])->name('mentors.index');
        Route::get('/mentors/{mentor}', [UserPageController::class, 'mentorShow'])->name('mentors.show');

        Route::get('/events', [UserPageController::class, 'eventsIndex'])->name('events.index');
        Route::get('/events/{event}', [UserPageController::class, 'eventShow'])->name('events.show');

        Route::get('/lms', [UserPageController::class, 'lmsIndex'])->name('lms.index');
        Route::get('/lms/{lmsContent}', [UserPageController::class, 'lmsShow'])->name('lms.show');

        Route::get('/products', [UserPageController::class, 'productsIndex'])->name('products.index');
        Route::get('/products/{product}', [UserPageController::class, 'productShow'])->name('products.show');

        Route::get('/news', [UserPageController::class, 'newsIndex'])->name('news.index');
        Route::get('/news/{news}', [UserPageController::class, 'newsShow'])->name('news.show');

        Route::get('/venues', [UserPageController::class, 'venuesIndex'])->name('venues.index');
        Route::get('/venues/{venue}', [UserPageController::class, 'venueShow'])->name('venues.show');
    });

});

