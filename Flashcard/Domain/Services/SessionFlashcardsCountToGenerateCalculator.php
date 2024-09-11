<?php

namespace Flashcard\Domain\Services;

use Flashcard\Domain\Models\Session;
use Flashcard\Domain\Repositories\ISessionFlashcardRepository;

class SessionFlashcardsCountToGenerateCalculator
{
    public function __construct(
        private ISessionFlashcardRepository $session_flashcard_repository,
    ) {}

    public function calculate(Session $session, int $base_limit): int
    {
        $limit = $base_limit;

        $not_rated_count = $this->session_flashcard_repository->getNotRatedFlashcardsInSessionCount($session->getId());

        if ($not_rated_count < $base_limit) {
            $limit = $base_limit - $not_rated_count;
        } else {
            return 0;
        }

        $generated_count = $this->session_flashcard_repository->getTotalSessionFlashcardsCount($session->getId());

        if ($generated_count > $session->getCardsPerSession()) {
            $limit = $session->getCardsPerSession() - $generated_count;
        }

        return $limit;
    }
}