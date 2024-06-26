<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\Info(title="Words api", version="0.1")
 */
class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;
}
