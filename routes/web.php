<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ManagerController;
use App\Http\Controllers\Admin\ReceptionistController;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/pending-approval', function () {
        return Inertia::render('Auth/PendingApproval');
    })->name('pending-approval');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'managers' => User::role('manager')->count(),
                'receptionists' => User::role('receptionist')->count(),
                'clients' => User::role('client')->count(),
            ],
        ]);
    })->name('admin.dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('managers', ManagerController::class)
            ->parameters(['managers' => 'user']);
        Route::resource('receptionists', ReceptionistController::class)
            ->parameters(['receptionists' => 'user']);
        Route::resource('clients', ClientController::class)
            ->parameters(['clients' => 'user']);
    });
});

Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/manager/dashboard', function () {
        return inertia('Manager/Dashboard');
    })->name('manager.dashboard');
});

Route::middleware(['auth', 'role:admin|manager'])->group(function () {
    // shared access
});

require __DIR__.'/auth.php';
