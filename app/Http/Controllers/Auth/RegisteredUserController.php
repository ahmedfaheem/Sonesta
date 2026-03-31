<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Rinvex\Country\Country;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'countries' => Cache::remember('countries', 86400, function () {
                return collect(countries())
                    ->map(fn (array|Country $country) => [
                        'name' => is_array($country) ? data_get($country, 'name.common', data_get($country, 'name')) : $country->getName(),
                    ])
                    ->filter(fn (array $country) => filled($country['name']))
                    ->sortBy('name')
                    ->values();
            }),
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
