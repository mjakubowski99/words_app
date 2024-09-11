<?php

namespace Flashcard\Domain\Services;

use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\Session;
use Flashcard\Domain\Models\SessionFlashcard;
use Flashcard\Domain\Models\SessionFlashcards;
use Flashcard\Domain\Repositories\ISessionFlashcardRepository;

class SessionFlashcardsService
{
    public function __construct(
        private SessionFlashcardsCountToGenerateCalculator $calculator,
        private ISessionFlashcardRepository $session_flashcard_repository,
        private IFlashcardSelector $selector,
    ) {}

    public function add(Session $session, int $limit): void
    {
        $limit = $this->calculator->calculate($session, $limit);

        $flashcards = $this->selector->select($session, $limit);

        $session_flashcards = new SessionFlashcards($session, array_map(function (Flashcard $flashcard) {
            return new SessionFlashcard($flashcard->getId(), null);
        }, $flashcards));

        $this->session_flashcard_repository->createMany($session_flashcards);
    }
}