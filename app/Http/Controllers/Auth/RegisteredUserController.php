<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\Client;
use App\Models\User;
use App\Services\CountryService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function __construct(private readonly CountryService $countryService)
    {
    }

    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'countries' => $this->countryService->all(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'national_id' => $validated['national_id'] ?? null,
            'password' => Hash::make($validated['password']),
            'avatar' => $request->hasFile('avatar')
                ? Storage::disk('public')->putFile('avatars', $request->file('avatar'))
                : null,
        ]);

        $user->assignRole('client');

        Client::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'avatar' => $user->avatar,
            'country' => $validated['country'],
            'gender' => $validated['gender'],
            'is_approved' => false,
        ]);

        event(new Registered($user));

        return redirect(route('login', absolute: false))
            ->with('status', 'Your account is pending approval');
    }
}
