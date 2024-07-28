<?php

declare(strict_types=1);

namespace Shared\Http\Request;

use UseCases\User\Find;
use UseCases\Contracts\User\IUser;
use Shared\Utils\ValueObjects\Uuid;
use Shared\Auth\ExternalAuthenticable;
use Illuminate\Foundation\Http\FormRequest;
use Mjakubowski\FirebaseAuth\FirebaseAuthenticable;

class Request extends FormRequest
{
    public function current($guard = null): IUser
    {
        $user = $this->user($guard);

        if (!$user) {
            abort(404);
        }

        /** @var Find $find */
        $find = app(Find::class);

        if ($user instanceof FirebaseAuthenticable) {
            return $find->findByExternal(
                ExternalAuthenticable::fromFirebase($user)
            );
        }

        return $find->findById(Uuid::fromString($user->id));
    }
}
