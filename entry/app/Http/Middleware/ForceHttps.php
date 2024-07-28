<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ForceHttps
{
    public function handle(Request $request, \Closure $next)
    {
        if (!in_array(app()->environment(), ['local', 'testing'], true)) {
            URL::forceScheme('https');
        }

        return $next($request);
    }
}
