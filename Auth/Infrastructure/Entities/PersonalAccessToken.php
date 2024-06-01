<?php

declare(strict_types=1);

namespace Auth\Infrastructure\Entities;

use Carbon\Carbon;
use Shared\Enum\UserType;
use Shared\Enum\TokenMorph;
use Shared\Utils\ValueObjects\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Auth\Domain\Models\Entities\IPersonalAccessToken;

/**
 * @property        int                             $id
 * @property        string                          $tokenable_type
 * @property        string                          $tokenable_id
 * @property        string                          $name
 * @property        string                          $token
 * @property        null|array                      $abilities
 * @property        null|\Illuminate\Support\Carbon $last_used_at
 * @property        null|\Illuminate\Support\Carbon $expires_at
 * @property        null|\Illuminate\Support\Carbon $created_at
 * @property        null|\Illuminate\Support\Carbon $updated_at
 * @property        \Eloquent|Model                 $tokenable
 * @method   static Builder|PersonalAccessToken     newModelQuery()
 * @method   static Builder|PersonalAccessToken     newQuery()
 * @method   static Builder|PersonalAccessToken     query()
 * @method   static Builder|PersonalAccessToken     whereAbilities($value)
 * @method   static Builder|PersonalAccessToken     whereCreatedAt($value)
 * @method   static Builder|PersonalAccessToken     whereExpiresAt($value)
 * @method   static Builder|PersonalAccessToken     whereId($value)
 * @method   static Builder|PersonalAccessToken     whereLastUsedAt($value)
 * @method   static Builder|PersonalAccessToken     whereName($value)
 * @method   static Builder|PersonalAccessToken     whereToken($value)
 * @method   static Builder|PersonalAccessToken     whereTokenableId($value)
 * @method   static Builder|PersonalAccessToken     whereTokenableType($value)
 * @method   static Builder|PersonalAccessToken     whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PersonalAccessToken extends \Laravel\Sanctum\PersonalAccessToken implements IPersonalAccessToken
{
    public static function morphToUserType(TokenMorph $morph): UserType
    {
        return UserType::USER;
    }

    public static function userTypeToMorph(UserType $type): TokenMorph
    {
        return TokenMorph::USER;
    }

    public function getKey(): string
    {
        return (string) parent::getKey();
    }

    public function getTokenableId(): Uuid
    {
        return Uuid::fromString($this->tokenable_id);
    }

    public function getRefreshExpiresAt(): ?Carbon
    {
        return $this->expires_at;
    }

    public function getUserType(): UserType
    {
        return self::morphToUserType(TokenMorph::from($this->tokenable_type));
    }
}
