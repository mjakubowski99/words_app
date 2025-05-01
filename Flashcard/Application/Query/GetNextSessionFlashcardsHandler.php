<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Application\ReadModels\SessionFlashcardsRead;
use Flashcard\Application\Repository\ISessionFlashcardReadRepository;
use Shared\Exercise\IFlashcardExerciseFacade;
use Shared\Exercise\IUnscrambleWordExerciseRead;

class GetNextSessionFlashcardsHandler
{
    public function __construct(
        private ISessionFlashcardReadRepository $repository,
        //private IFlashcardExerciseFacade $facade,
    ) {}

    public function handle(SessionId $session_id, int $limit): SessionFlashcardsRead
    {
        return $this->repository->findUnratedById($session_id, $limit);
    }

//    public function handleExercise(int $exercise_id): IUnscrambleWordExerciseRead
//    {
//        return $this->facade->getUnscrambleWordExercise($exercise_id);
//    }
}
