<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && in_array($request->user()->status, ['banned', 'suspended'])) {
            return response()->json([
                'message' => 'Akun Anda telah diblokir. Hubungi admin untuk informasi lebih lanjut.',
            ], 403);
        }

        return $next($request);
    }
}