<?php

declare(strict_types=1);

namespace User\Infrastructure\Entities;

use App\Models\User as BaseModel;
use Shared\Utils\ValueObjects\UserId;
use User\Domain\Models\Entities\IUser;
use Shared\Utils\ValueObjects\Language;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 */
class User extends BaseModel implements IUser
{
    use HasUuids;

    public function getId(): UserId
    {
        return new UserId($this->id);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function profileCompleted(): bool
    {
        return $this->profile_completed;
    }

    public function getUserLanguage(): Language
    {
        return Language::from($this->user_language);
    }

    public function getLearningLanguage(): Language
    {
        return new Language($this->learning_language);
    }

    public function setUserLanguage(Language $language): void
    {
        $this->user_language = $language->getValue();
    }

    public function setLearningLanguage(Language $language): void
    {
        $this->learning_language = $language->getValue();
    }

    public function setProfileCompleted(): void
    {
        $this->profile_completed = true;
    }
}
