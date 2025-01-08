<?php

declare(strict_types=1);

namespace Tests\Smoke\User\Infrastructure\Http\Controllers\UserController\v1;

use Mockery\MockInterface;
use Shared\Enum\UserProvider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as SocialiteUser;

trait UserControllerTrait
{
    public function fakeSocialiteUser(): MockInterface|SocialiteUser
    {
        $user_mock = \Mockery::mock(SocialiteUser::class);

        $user_mock->allows([
            'getName' => 'name',
            'getAvatar' => 'avatar.png',
            'getId' => '1231223',
            'getEmail' => 'email@email.com',
            'getNickname' => 'nickname',
        ]);

        return $user_mock;
    }

    public function fakeOAuthLogin(SocialiteUser $user, UserProvider $user_provider): void
    {
        Socialite::shouldReceive('driver')
            ->with($user_provider->value)
            ->andReturnSelf();

        Socialite::shouldReceive('userFromToken')->andReturn($user);
    }
}
