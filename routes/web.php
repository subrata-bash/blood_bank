<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [FrontController::class, 'index'])->name('home');
Route::get('/about', [FrontController::class, 'about'])->name('about');

// User with Middleware
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/submit', [UserController::class, 'profileSubmit'])->name('profile.submit');
});

// User
Route::get('/login', [UserController::class, 'login'])->name('login');
Route::get('/register', [UserController::class, 'register'])->name('register');
Route::get('/register-verify/{token}/{email}', [UserController::class, 'registerVerify'])->name('register.verify');
Route::post('/register', [UserController::class, 'registerSubmit'])->name('register.submit');
Route::post('/login/submit', [UserController::class, 'loginSubmit'])->name('login.submit');
Route::get('/foget-password', [UserController::class, 'forgetPassword'])->name('forget.password');
Route::post('/forget-password', [UserController::class, 'forgetPasswordSubmit'])->name('forget.password.submit');
Route::get('/reset-password/{token}/{email}', [UserController::class, 'resetPassword'])->name('reset.password');
Route::post('/reset-password/{token}/{email}', [UserController::class, 'resetPasswordSubmit'])->name('reset.password.submit');
// Admin with Middleware
Route::middleware('admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('/admin/profile/submit', [AdminController::class, 'profileSubmit'])->name('admin.profile.submit');
});

// Admin
Route::get('/admin', function () {
    return redirect()->route('admin.login');
});
Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/login/submit', [AdminController::class, 'loginSubmit'])->name('admin.login.submit');
Route::get('/admin/foget-password', [AdminController::class, 'forgotPassword'])->name('admin.forget.password');
Route::post('/admin/forget-password', [AdminController::class, 'forgotPasswordSubmit'])->name('admin.forget.password.submit');
Route::get('/admin/reset-password/{token}/{email}', [AdminController::class, 'resetPassword'])->name('admin.reset.password');
Route::post('/admin/reset-password/{token}/{email}', [AdminController::class, 'resetPasswordSubmit'])->name('admin.reset.password.submit');
