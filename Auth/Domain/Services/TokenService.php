<?php

declare(strict_types=1);

namespace Auth\Domain\Services;

use Shared\Utils\Str\IStr;
use Shared\Utils\Config\IConfig;
use UseCases\Contracts\User\IUser;
use Auth\Domain\Repositories\ITokenRepository;

readonly class TokenService
{
    public function __construct(
        private IConfig $config,
        private IStr $string,
        private ITokenRepository $repository,
    ) {}

    public function createUserToken(IUser $user): string
    {
        $plain_text_token = $this->generateTokenString();

        $token = $this->repository->createUserToken($user, $plain_text_token);

        return $token->getKey() . '|' . $plain_text_token;
    }

    public function removeToken(string $token): void
    {
        $this->repository->removeToken($token);
    }

    public function refreshUserToken(string $token): string
    {
        $token = $this->repository->findToken($token);

        if ($token === null) {
            throw new \Exception();
        }

        if ($token->getRefreshExpiresAt()->gte(now())) {
            throw new \Exception();
        }

        return '';
    }

    private function generateTokenString(): string
    {
        return sprintf(
            '%s%s%s',
            $this->config->get('sanctum.token_prefix') ?? '',
            $tokenEntropy = $this->string->random(40),
            hash('crc32b', $tokenEntropy)
        );
    }
}
