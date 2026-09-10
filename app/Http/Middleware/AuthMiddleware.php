<?php

namespace App\Http\Middleware;

use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    use ResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return $this->errorResponse(null, 401, 'Unauthenticated');
        }

        return $next($request);
    }
}
