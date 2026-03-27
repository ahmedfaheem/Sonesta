<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $roles = collect($roles)
            ->flatMap(static fn (string $role) => explode('|', $role))
            ->filter()
            ->values()
            ->all();

        if (! $user) {
            throw new HttpException(Response::HTTP_FORBIDDEN);
        }

        if ($roles !== [] && ! $user->hasAnyRole($roles)) {
            throw new HttpException(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
