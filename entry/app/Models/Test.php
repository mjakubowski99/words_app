<?php

declare(strict_types=1);

namespace App\Models;

use Auth\Domain\Services\LoginService;
use Illuminate\Http\Request;

class Test
{
    public function __construct(
        private Request $request
    ) {}

    public function app()
    {
        return $this->request->all();
    }
}
