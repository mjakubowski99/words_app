<?php

declare(strict_types=1);

namespace Shared\Http\Request;

use App\Models\User;
use Shared\User\IUser;
use Shared\User\IUserFacade;
use Shared\Utils\ValueObjects\UserId;
use Illuminate\Foundation\Http\FormRequest;
use Shared\Utils\Auth\ExternalAuthenticable;
use Mjakubowski\FirebaseAuth\FirebaseAuthenticable;

class Request extends FormRequest
{
    public function current($guard = null): IUser
    {
        $mocke = \Mockery::mock(IUser::class);
        $mocke->shouldReceive('getId')->andReturn(new UserId('9cf45e8c-b319-4fd9-84e0-e82431f56baa'));
        return $mocke;
        $user = $this->user($guard);

        if (!$user) {
            abort(404);
        }

        /** @var IUserFacade $user_facade */
        $user_facade = app(IUserFacade::class);

        if ($user instanceof FirebaseAuthenticable) {
            $authenticable = ExternalAuthenticable::fromFirebase($user);

            return $user_facade->findByExternal(
                $authenticable->getProviderId(),
                $authenticable->getProviderType()
            );
        }

        return $user_facade->findById(new UserId($user->id));
    }
}
