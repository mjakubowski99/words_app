<?php

declare(strict_types=1);

namespace Exercise\Application\Facades;

use Exercise\Application\Repositories\IExerciseSummaryRepository;
use Exercise\Application\Repositories\IUnscrambleWordExerciseReadRepository;
use Exercise\Application\Services\FlashcardExerciseFactory;
use Shared\Enum\ExerciseType;
use Shared\Exercise\ExerciseTypes\IUnscrambleWordExerciseRead;
use Shared\Exercise\IExerciseSummary;
use Shared\Exercise\IFlashcardExercise;
use Shared\Exercise\IFlashcardExerciseFacade;
use Shared\Flashcard\ISessionFlashcardSummary;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Utils\ValueObjects\UserId;

class FlashcardExerciseFacade implements IFlashcardExerciseFacade
{
    public function __construct(
        private FlashcardExerciseFactory $exercise_factory,
        private IUnscrambleWordExerciseReadRepository $repository,
        private IExerciseSummaryRepository $summary_repository,
    ) {}

    public function getExerciseSummaryByEntryId(int $exercise_entry_id): IExerciseSummary
    {
        return $this->summary_repository->getExerciseSummaryByFlashcard($exercise_entry_id);
    }

    /** 
     * @param ISessionFlashcardSummary[] $session_flashcard_summaries
     * @return IFlashcardExercise[]
     * */
    public function buildExercise(array $session_flashcard_summaries, UserId $user_id, ExerciseType $type): array
    {
        return $this->exercise_factory->makeExercise($session_flashcard_summaries, $user_id, $type);
    }

    public function getUnscrambleWordExercise(ExerciseId $id): IUnscrambleWordExerciseRead
    {
        return $this->repository->find($id);
    }
}
