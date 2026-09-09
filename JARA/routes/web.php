<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CollaboratorController;
use Illuminate\Support\Facades\Route;

// ============================================
// AUTHENTICATION ROUTES - GALANG
// FR-01, FR-02, FR-03
// ============================================

Route::middleware('guest')->group(function () {
    // Register
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ============================================
// PROJECT ROUTES - AL
// ============================================

Route::middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class);
});

// ============================================
// TASK ROUTES - GALANG
// FR-14 s/d FR-20
// ============================================

Route::middleware('auth')->prefix('projects/{project}')->group(function () {
    Route::resource('tasks', TaskController::class)->except(['index']);
});

// ============================================
// COLLABORATOR ROUTES - ABHI
// ============================================

Route::middleware('auth')->post('/projects/{project}/collaborators', [CollaboratorController::class, 'store'])
    ->name('collaborators.store');

// ============================================
// ADMIN ROUTES - DANIEL
// ============================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', AdminUserController::class);
});

// ============================================
// REDIRECT ROOT
// ============================================

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('projects.index');
    }
    return redirect()->route('login');
})->name('home');