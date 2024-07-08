<?php

declare(strict_types=1);

namespace Shared\Http\Request;

use Illuminate\Foundation\Http\FormRequest;
use UseCases\Contracts\Auth\IAuthenticable;

class Request extends FormRequest
{
    public function authenticable(): IAuthenticable
    {
        return $this->user();
    }
}
