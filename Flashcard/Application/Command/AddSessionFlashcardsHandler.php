<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\Flashcard;
use Shared\Exercise\IFlashcardExerciseFacade;
use Flashcard\Application\DTO\SessionFlashcardSummary;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Domain\Models\NextSessionFlashcardResult;
use Flashcard\Domain\Services\SessionFlashcardsService;
use Flashcard\Application\Repository\INextSessionFlashcardsRepository;

class AddSessionFlashcardsHandler
{
    public function __construct(
        private INextSessionFlashcardsRepository $next_session_flashcards_repository,
        private readonly IFlashcardSelector $selector,
        private readonly SessionFlashcardsService $service,
        private IFlashcardExerciseFacade $facade,
    ) {}

    public function handle(AddSessionFlashcards $command, int $display_limit = 1): void
    {
        $next_session_flashcards = $this->next_session_flashcards_repository->find($command->getSessionId());

        if ($next_session_flashcards->getUnratedCount() >= $display_limit) {
            return;
        }

        $flashcards = $this->selector->select($next_session_flashcards, $command->getLimit());

        $exercise_type = $next_session_flashcards->resolveNextExerciseType();

        if ($next_session_flashcards->isMixedSessionType() && $flashcards[0]->getLastUserRating() && $flashcards[0]->getLastUserRating()->value < Rating::GOOD->value) {
            $exercise_type = null;
        }

        if ($exercise_type) {
            $required_flashcards_count_for_exercise = 1;

            $additional_flashcards = $this->selector->select(
                $next_session_flashcards,
                $required_flashcards_count_for_exercise - 1,
                [$flashcards[0]->getId()]
            );

            $next_session_flashcards = $this->service->add($next_session_flashcards, [$flashcards[0]]);

            foreach ($additional_flashcards as $additional_flashcard) {
                $next_session_flashcards->addNextAdditional($additional_flashcard);
            }

            $save_flashcards_results = $this->next_session_flashcards_repository->save($next_session_flashcards);

            $all_flashcards = array_merge([$flashcards[0]], $additional_flashcards);

            $this->facade->makeExercise(
                $this->buildFlashcardSummaryObjects($all_flashcards, $save_flashcards_results),
                $command->getUserId(),
                $exercise_type
            );
        } else {
            $next_session_flashcards = $this->service->add($next_session_flashcards, $flashcards);
        }

        $this->next_session_flashcards_repository->save($next_session_flashcards);
    }

    private function buildFlashcardSummaryObjects(array $flashcards, array $save_flashcards_results): array
    {
        $flashcard_summaries = [];

        /** @var NextSessionFlashcardResult $save_flashcards_result */
        foreach ($save_flashcards_results as $save_flashcards_result) {
            $flashcard = array_filter(
                $flashcards,
                fn (Flashcard $flashcard) => $flashcard->getId()->equals($save_flashcards_result->getFlashcardId())
            )[0];

            $flashcard_summaries[] = new SessionFlashcardSummary(
                $save_flashcards_result->getId()->getValue(),
                $flashcard->getFrontWord(),
                $flashcard->getBackWord(),
                $flashcard->getFrontContext(),
                $flashcard->getBackContext(),
                $flashcard->getFrontLang(),
                $flashcard->getBackLang(),
                $flashcard->getEmoji()->toUnicode(),
            );
        }

        return $flashcard_summaries;
    }
}
