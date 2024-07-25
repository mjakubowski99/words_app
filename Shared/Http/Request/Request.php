<?php

declare(strict_types=1);

namespace Shared\Http\Request;

use Shared\Auth\Authenticable;
use Illuminate\Foundation\Http\FormRequest;
use UseCases\Contracts\Auth\IAuthenticable;

class Request extends FormRequest
{
    /**
     * @throws \Exception
     */
    public function authenticable(): IAuthenticable
    {
        if ($this->user('firebase')) {
            return Authenticable::fromFirebase(
                $this->user('firebase')
            );
        }

        if ($this->user()) {
            throw new \Exception('Not supported auth driver');
        }

        abort(401);
    }
}
