<?php

declare(strict_types=1);

namespace User\Infrastructure\Entities;

use App\Models\User as BaseModel;
use Shared\Utils\ValueObjects\UserId;
use Shared\Utils\ValueObjects\Uuid;
use User\Domain\Models\Entities\IUser;
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
}
