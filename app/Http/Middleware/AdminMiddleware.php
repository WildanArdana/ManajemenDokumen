<?php

namespace App\Http\Middleware; // Pastikan namespace ini benar

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware // Pastikan nama kelas ini benar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user sudah login DAN memiliki peran 'admin'
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // Jika tidak memenuhi syarat, lemparkan error 403
        abort(403, 'Tindakan ini tidak diizinkan.');
    }
}