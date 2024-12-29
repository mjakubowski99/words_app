<?php

namespace Flashcard\Application\Facades;

use Flashcard\Application\Command\DeleteUserDataHandler;
use Shared\Flashcard\IFlashcardFacade;
use Shared\Utils\ValueObjects\UserId;

class FlashcardFacade implements IFlashcardFacade
{
    public function __construct(
        private DeleteUserDataHandler $delete_user_data_handler,
    )
    {

    }

    public function deleteUserData(UserId $user_id): void
    {
        $this->delete_user_data_handler->handle($user_id);
    }
}