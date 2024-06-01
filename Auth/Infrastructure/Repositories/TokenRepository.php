<?php

declare(strict_types=1);

namespace Auth\Infrastructure\Repositories;

use Carbon\Carbon;
use Shared\Enum\UserType;
use UseCases\Contracts\User\IUser;
use Auth\Domain\Repositories\ITokenRepository;
use Auth\Domain\Models\Entities\IPersonalAccessToken;
use Auth\Infrastructure\Entities\PersonalAccessToken;

readonly class TokenRepository implements ITokenRepository
{
    public function __construct(
        private readonly PersonalAccessToken $token
    ) {}

    public function findToken(string $token): ?IPersonalAccessToken
    {
        return $this->token::findToken($token);
    }

    public function createUserToken(IUser $user, string $plain_text_token): IPersonalAccessToken
    {
        /* @var PersonalAccessToken */
        return $this->token->newQuery()->forceCreate([
            'tokenable_id' => (string) $user->getId(),
            'tokenable_type' => PersonalAccessToken::userTypeToMorph(UserType::USER),
            'name' => $user->getEmail() . '.' . Carbon::now()->timestamp,
            'token' => hash('sha256', $plain_text_token),
            'abilities' => ['*'],
            'expires_at' => null,
        ]);
    }

    public function removeToken(string $token): void
    {
        $token = $this->token::findToken($token);
        $token?->delete();
    }
}
