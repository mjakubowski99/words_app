<?php

declare(strict_types=1);

namespace Shared\Flashcard;

use Shared\Utils\ValueObjects\UserId;

interface IFlashcardFacade
{
    public function deleteUserData(UserId $user_id): void;
}