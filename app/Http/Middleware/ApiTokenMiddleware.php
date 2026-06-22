<?php

namespace App\Http\Middleware;

use Closure;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    { {
            $token = $request->header(
                'X-API-KEY'
            );

            if (
                !$token ||
                $token !== config(
                    'bridgingjadwal.token'
                )
            ) {

                return response()->json([

                    'success' => false,
                    'message' => 'Unauthorized'

                ], 401);
            }

            return $next($request);
        }
    }
}
