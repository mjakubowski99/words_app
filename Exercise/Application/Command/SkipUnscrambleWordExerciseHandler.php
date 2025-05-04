<?php

declare(strict_types=1);

namespace Exercise\Application\Command;

use Shared\Utils\ValueObjects\UserId;
use Shared\Flashcard\IFlashcardFacade;
use Exercise\Domain\Models\ExerciseEntry;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Exceptions\UnauthorizedException;
use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;

class SkipUnscrambleWordExerciseHandler
{
    public function __construct(
        private IUnscrambleWordExerciseRepository $repository,
        private IFlashcardFacade $facade,
    ) {}

    public function handle(ExerciseId $id, UserId $user_id): void
    {
        $exercise = $this->repository->find($id);

        if (!$exercise->getUserId()->equals($user_id)) {
            throw new UnauthorizedException();
        }

        $exercise->skipExercise();

        $this->repository->save($exercise);

        $entries = array_values(
            array_filter($exercise->getExerciseEntries(), fn (ExerciseEntry $entry) => $entry->getSessionFlashcardId() !== null)
        );

        $session_flashcard_ids = array_map(
            fn (ExerciseEntry $entry) => $entry->getSessionFlashcardId()->getValue(),
            $entries
        );

        $this->facade->updateRatingsByPreviousRates($session_flashcard_ids);
    }
}
