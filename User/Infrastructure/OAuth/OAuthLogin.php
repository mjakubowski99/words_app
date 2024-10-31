<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth;

use Shared\Enum\Platform;
use Psr\Log\LoggerInterface;
use Shared\Enum\UserProvider;
use User\Domain\Contracts\IOAuthUser;
use User\Domain\Contracts\IOAuthLogin;
use Shared\Exceptions\UnauthorizedException;

class OAuthLogin implements IOAuthLogin
{
    public function __construct(
        private OAuthLoginStrategyFactory $factory,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws UnauthorizedException
     */
    public function login(UserProvider $provider, string $access_token, Platform $platform): IOAuthUser
    {
        $login_strategy = $this->factory->make($provider, $platform);

        try {
            return $login_strategy->userFromToken($access_token);
        } catch (\Throwable $exception) {
            if (config('auth.debug_oauth_login')) {
                $this->logger->debug('Failed to verify access token', [
                    'message' => $exception->getMessage(),
                    'token' => $access_token,
                ]);
            }

            throw new UnauthorizedException();
        }
    }
}
