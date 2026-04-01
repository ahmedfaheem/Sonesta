<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIfBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->is_approved === false) {
            Auth::logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been banned.',
            ]);
        }

        return $next($request);
    }
}
