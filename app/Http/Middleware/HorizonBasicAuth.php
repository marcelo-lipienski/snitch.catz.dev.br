<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HorizonBasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Illuminate\Support\Facades\Log::debug('Horizon Basic Auth Hit: ' . $request->fullUrl());

        if (app()->environment('local') && !config('horizon.auth.always_auth', false)) {
            return $next($request);
        }

        $username = config('horizon.auth.user');
        $password = config('horizon.auth.password');

        if (empty($username) || empty($password)) {
            \Illuminate\Support\Facades\Log::warning('Horizon Basic Auth: Credentials not configured.');
            return response('Horizon credentials not configured.', 500);
        }

        if ($request->getUser() !== $username || $request->getPassword() !== $password) {
            return response('Unauthorized.', 401, [
                'WWW-Authenticate' => 'Basic realm="Horizon Dashboard"',
            ]);
        }

        return $next($request);
    }
}
