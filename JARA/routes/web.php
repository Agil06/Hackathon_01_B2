<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('projects.index')
        : redirect()->route('login');
})->name('home');

// Authentication Routes (galang: FR-01 - FR-03)
if (class_exists(AuthController::class)) {
    Route::middleware('guest')->group(function () {
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
} else {
    Route::get('/login', fn () => 'Login page (galang)')->name('login');
    Route::post('/logout', fn () => redirect()->route('login'))->name('logout');
}

// Project Management Routes (al: FR-07 - FR-11)
Route::middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class);

    // Collaboration Routes (abhi: FR-12 - FR-13)
    if (class_exists(CollaboratorController::class)) {
        Route::post('/projects/{project}/collaborators', [CollaboratorController::class, 'store'])
            ->name('projects.collaborators.store');
    }

    // Task Management Routes (galang: FR-14 - FR-20)
    if (class_exists(TaskController::class)) {
        Route::prefix('projects/{project}')->name('projects.tasks.')->group(function () {
            Route::get('/tasks/create', [TaskController::class, 'create'])->name('create');
            Route::post('/tasks', [TaskController::class, 'store'])->name('store');
            Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('show');
            Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('edit');
            Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('update');
            Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('destroy');
        });
    }
});

// Admin User Management Routes (daniel: FR-04 - FR-06)
if (class_exists(AdminUserController::class)) {
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
}
