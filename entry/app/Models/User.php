<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Flashcard\Domain\Models\Owner;
use Database\Factories\UserFactory;
use Shared\Utils\ValueObjects\UserId;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotificationCollection;

/**
 * @property        string                                                    $id
 * @property        string                                                    $name
 * @property        string                                                    $email
 * @property        null|Carbon                                               $email_verified_at
 * @property        mixed                                                     $password
 * @property        null|string                                               $remember_token
 * @property        null|Carbon                                               $created_at
 * @property        null|Carbon                                               $updated_at
 * @property        DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property        null|int                                                  $notifications_count
 * @property        Collection<int, PersonalAccessToken>                      $tokens
 * @property        null|int                                                  $tokens_count
 * @method   static UserFactory                                               factory($count = null, $state = [])
 * @method   static Builder|User                                              newModelQuery()
 * @method   static Builder|User                                              newQuery()
 * @method   static Builder|User                                              query()
 * @method   static Builder|User                                              whereCreatedAt($value)
 * @method   static Builder|User                                              whereEmail($value)
 * @method   static Builder|User                                              whereEmailVerifiedAt($value)
 * @method   static Builder|User                                              whereId($value)
 * @method   static Builder|User                                              whereName($value)
 * @method   static Builder|User                                              wherePassword($value)
 * @method   static Builder|User                                              whereRememberToken($value)
 * @method   static Builder|User                                              whereUpdatedAt($value)
 * @property        null|string                                               $provider_id
 * @property        null|string                                               $provider_type
 * @property        null|string                                               $picture
 * @method   static Builder|User                                              wherePicture($value)
 * @method   static Builder|User                                              whereProviderId($value)
 * @method   static Builder|User                                              whereProviderType($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasUuids;

    public static $deps = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getId(): UserId
    {
        return new UserId($this->id);
    }

    public function toOwner(): Owner
    {
        return Owner::fromUser($this->getId());
    }
}
