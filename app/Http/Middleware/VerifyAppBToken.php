<?php

namespace App\Http\Middleware;

use Closure;

class VerifyAppBToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken(); // Ambil dari header Authorization

        if ($token !== config('services.app_payroll.token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
