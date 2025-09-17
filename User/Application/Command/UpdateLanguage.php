<?php

namespace User\Application\Command;

use Shared\Flashcard\IFlashcardFacade;
use Shared\Utils\ValueObjects\Language;
use Shared\Utils\ValueObjects\UserId;
use User\Application\DTO\UserDTO;
use User\Application\Repositories\IUserRepository;

class UpdateLanguage
{
    public function __construct(
        private IUserRepository $repository,
        private IFlashcardFacade $flashcard_facade
    ) {}

    public function handle(UserId $user_id, Language $user_language, Language $learning_language): void
    {
        $domain_user = $this->repository->findById($user_id);

        $domain_user->setUserLanguage($user_language);
        $domain_user->setLearningLanguage($learning_language);
        $domain_user->setProfileCompleted();

        $this->repository->update($domain_user);

        $this->flashcard_facade->postLanguageUpdate(new UserDTO($domain_user));
    }
}