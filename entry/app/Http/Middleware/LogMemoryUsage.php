<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogMemoryUsage
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
        // Uruchomienie pomiaru pamięci przed przetworzeniem żądania
        $startMemory = memory_get_usage();

        // Przetworzenie żądania
        $response = $next($request);

        // Po przetworzeniu żądania, obliczamy zużycie pamięci
        $endMemory = memory_get_usage();
        $memoryUsed = $endMemory - $startMemory;

        $memoryUsedKB = $memoryUsed / 1024; // W kilobajtach

        Log::info('Zużycie pamięci: ' . round($memoryUsedKB, 2) . ' KB');

        return $response;
    }
}
