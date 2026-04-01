<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ManagerController;
use App\Http\Controllers\Admin\ReceptionistController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Client\ReservationController as ClientReservationController;
use App\Http\Controllers\Manager\ClientController as ManagerClientController;
use App\Http\Controllers\Manager\FloorController;
use App\Http\Controllers\Manager\ReceptionistController as ManagerReceptionistController;
use App\Http\Controllers\Manager\RoomController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Receptionist\ClientController as ReceptionistClientController;
use App\Http\Controllers\Receptionist\DashboardController as ReceptionistDashboardController;
use App\Http\Controllers\Receptionist\ReservationController as ReceptionistReservationController;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

Route::get('/', function (Request $request) {
    $rooms = collect();

    if (Schema::hasTable('rooms')) {
        try {
            $rooms = Room::query()
                ->latest()
                ->limit(6)
                ->get(['id', 'number', 'capacity', 'price']);
        } catch (QueryException) {
            $rooms = collect();
        }
    }

    return Inertia::render('Welcome', [
        'canLogin' => ! $request->user() && Route::has('login'),
        'canRegister' => ! $request->user() && Route::has('register'),
        'rooms' => $rooms
            ->map(fn (Room $room) => [
                'id' => $room->id,
                'number' => $room->number,
                'capacity' => $room->capacity,
                'price' => number_format($room->price / 100, 2, '.', ''),
            ]),
    ]);
});

Route::get('/dashboard', function (Request $request) {
    $user = $request->user();

    if (! $user) {
        return redirect()->route('login');
    }

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('manager')) {
        return redirect()->route('manager.dashboard');
    }

    if ($user->hasRole('receptionist')) {
        return redirect()->route('receptionist.dashboard');
    }

    $clientProfile = $user->clientProfile;

    if ($user->hasRole('client') && ! ($clientProfile && $clientProfile->is_approved)) {
        return redirect()->route('pending-approval');
    }

    if ($user->hasRole('client')) {
        return redirect()->route('client.rooms.index');
    }

    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update.patch');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
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
        Route::get('clients/export', [ClientController::class, 'export'])->name('clients.export');

        Route::get('reservations', [AdminReservationController::class, 'index'])
            ->name('reservations.index');
        Route::delete('reservations/{reservation}', [AdminReservationController::class, 'destroy'])
            ->name('reservations.destroy');

        Route::resource('managers', ManagerController::class)
            ->parameters(['managers' => 'user']);
        Route::patch('managers/{user}/ban', [ManagerController::class, 'toggleBan'])
            ->name('managers.ban');
        Route::resource('receptionists', ReceptionistController::class)
            ->parameters(['receptionists' => 'user']);
        Route::patch('receptionists/{user}/ban', [ReceptionistController::class, 'toggleBan'])
            ->name('receptionists.ban');
        Route::resource('clients', ClientController::class)
            ->parameters(['clients' => 'user']);
        Route::resource('floors', FloorController::class)->except('show');
        Route::resource('rooms', RoomController::class)->except('show');
    });
});

Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/manager/dashboard', function () {
        return inertia('Manager/Dashboard');
    })->name('manager.dashboard');
});

Route::middleware(['auth', 'role:receptionist'])->group(function () {
    Route::get('/receptionist/dashboard', ReceptionistDashboardController::class)
        ->name('receptionist.dashboard');

    Route::prefix('receptionist')->name('receptionist.')->group(function () {
        Route::get('clients/pending', [ReceptionistClientController::class, 'pending'])
            ->middleware('can:viewPendingClients,'.User::class)
            ->name('clients.pending');
        Route::patch('clients/{user}/approve', [ReceptionistClientController::class, 'approve'])
            ->middleware('can:approveClient,user')
            ->name('clients.approve');
        Route::get('clients/my-approved', [ReceptionistClientController::class, 'approved'])
            ->middleware('can:viewApprovedClients,'.User::class)
            ->name('clients.approved');
        Route::get('reservations', [ReceptionistReservationController::class, 'index'])
            ->name('reservations.index');
    });
});

Route::middleware(['auth', 'role:admin|manager'])->group(function () {
    Route::prefix('manager')->name('manager.')->group(function () {
        Route::resource('floors', FloorController::class)->except('show');
        Route::resource('rooms', RoomController::class)->except('show');
        Route::resource('receptionists', ManagerReceptionistController::class)
            ->middleware('can:viewAnyReceptionists,'.User::class)
            ->parameters(['receptionists' => 'user']);
        Route::patch('receptionists/{user}/ban', [ManagerReceptionistController::class, 'toggleBan'])
            ->name('receptionists.ban');
        Route::resource('clients', ManagerClientController::class)
            ->middleware('can:viewAnyClients,'.User::class)
            ->parameters(['clients' => 'user']);
    });
});

Route::middleware(['auth', 'role:client'])->group(function () {
    Route::prefix('client')->name('client.')->group(function () {
        Route::get('rooms/available', [ClientReservationController::class, 'availableRooms'])
            ->name('rooms.index');
        Route::get('reservations', [ClientReservationController::class, 'index'])
            ->name('reservations.index');
        Route::delete('reservations/{reservation}', [ClientReservationController::class, 'destroy'])
            ->name('reservations.destroy');
    });

    Route::get('reservations/rooms/{room}', [ClientReservationController::class, 'create'])
        ->name('client.reservations.create');
    Route::post('reservations/rooms/{room}/checkout', [ClientReservationController::class, 'checkout'])
        ->name('client.reservations.checkout');
    Route::get('reservations/success', [ClientReservationController::class, 'success'])
        ->name('client.reservations.success');
    Route::get('reservations/cancel/{room?}', [ClientReservationController::class, 'cancel'])
        ->name('client.reservations.cancel');
});

require __DIR__.'/auth.php';
