<?php

declare(strict_types=1);

namespace User\Infrastructure\Http\Request;

use Shared\Auth\Authenticable;
use Illuminate\Foundation\Http\FormRequest;
use UseCases\Contracts\Auth\IAuthenticable;

class GetUserRequest extends FormRequest
{
    public function authenticable(): IAuthenticable
    {
        return Authenticable::fromFirebase(
            $this->user('firebase')
        );
    }
}
