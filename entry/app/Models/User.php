<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Flashcard\Domain\Models\Owner;
use Shared\Utils\ValueObjects\UserId;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;

/**
 * Temporary to fix phpstan bug after upgrade to Laravel 12.
 *
 * @property        string                                                    $id
 * @property        string                                                    $name
 * @property        string                                                    $email
 * @property        null|Carbon                                               $email_verified_at
 * @property        string                                                    $password
 * @property        null|string                                               $provider_id
 * @property        null|string                                               $provider_type
 * @property        null|string                                               $picture
 * @property        null|string                                               $remember_token
 * @property        null|Carbon                                               $created_at
 * @property        null|Carbon                                               $updated_at
 * @property        Collection<int, Flashcard>                                $flashcards
 * @property        null|int                                                  $flashcards_count
 * @property        DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property        null|int                                                  $notifications_count
 * @property        Collection<int, PersonalAccessToken>                      $tokens
 * @property        null|int                                                  $tokens_count
 * @method   static \Database\Factories\UserFactory                           factory($count = null, $state = [])
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|User        newModelQuery()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|User        newQuery()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|User        query()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|User        whereCreatedAt($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|User        whereEmail($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|User        whereEmailVerifiedAt($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|User        whereId($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|User        whereName($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|User        wherePassword($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|User        wherePicture($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|User        whereProviderId($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|User        whereProviderType($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|User        whereRememberToken($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|User        whereUpdatedAt($value)
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

    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class);
    }
}
