<?php

declare(strict_types=1);

namespace Shared\Http\Request;

use Shared\User\IUser;
use Shared\User\IUserFacade;
use Shared\Utils\ValueObjects\UserId;
use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    public function current($guard = null): IUser
    {
        $user = $this->user($guard);

        /** @var IUserFacade $user_facade */
        $user_facade = app(IUserFacade::class);

        return $user_facade->findById(new UserId($user->id));
    }
}
