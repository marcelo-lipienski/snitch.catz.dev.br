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
        if (app()->environment('local')) {
            return $next($request);
        }

        $username = env('HORIZON_USER');
        $password = env('HORIZON_PASSWORD');

        if (empty($username) || empty($password)) {
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
