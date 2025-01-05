<?php

declare(strict_types=1);

namespace Flashcard\Application\Facades;

use Shared\Utils\ValueObjects\UserId;
use Shared\Flashcard\IFlashcardFacade;
use Flashcard\Application\Command\DeleteUserDataHandler;

class FlashcardFacade implements IFlashcardFacade
{
    public function __construct(
        private DeleteUserDataHandler $delete_user_data_handler,
    ) {}

    public function deleteUserData(UserId $user_id): void
    {
        $this->delete_user_data_handler->handle($user_id);
    }
}
