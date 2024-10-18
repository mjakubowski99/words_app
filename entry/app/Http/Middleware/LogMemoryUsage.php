<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogMemoryUsage
{
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // Uruchomienie pomiaru pamięci przed przetworzeniem żądania
        $startMemory = memory_get_usage();
        $peakStart = memory_get_peak_usage();

        // Przetworzenie żądania
        $response = $next($request);

        // Po przetworzeniu żądania, obliczamy zużycie pamięci
        $endMemory = memory_get_usage();
        $peakEnd = memory_get_peak_usage();
        $memoryUsed = $endMemory - $startMemory;

        $memoryUsedKB = $memoryUsed / 1024; // W kilobajtach

        Log::info('Zużycie pamięci: ' . round($memoryUsedKB, 2) . ' KB');

        Log::info('Zużycie pamięci peak start: ' . round($peakStart / 1024, 2) . ' KB');
        Log::info('Zużycie pamięci peak end: ' . round($peakEnd / 1024, 2) . ' KB');

        return $response;
    }
}
