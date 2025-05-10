<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\SmTwo;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\Contracts\IRepetitionAlgorithmDTO;
use Flashcard\Application\Services\FlashcardPollUpdater;
use Flashcard\Application\Services\IRepetitionAlgorithm;
use Flashcard\Application\Repository\ISmTwoFlashcardRepository;

class SmTwoRepetitionAlgorithm implements IRepetitionAlgorithm
{
    public function __construct(
        private ISmTwoFlashcardRepository $repository,
        private FlashcardPollUpdater $poll_updater,
    ) {}

    public function handle(IRepetitionAlgorithmDTO $dto): void
    {
        if (empty($dto->getRatedSessionFlashcardIds())) {
            return;
        }

        $user_flashcards = array_map(
            fn (SessionFlashcardId $id) => ['flashcard_id' => $dto->getFlashcardId($id), 'user_id' => $dto->getUserIdForFlashcard($id)->getValue()],
            $dto->getRatedSessionFlashcardIds()
        );

        $user_flashcard_map = [];
        foreach ($user_flashcards as $user_flashcard) {
            if (!array_key_exists($user_flashcard['user_id'], $user_flashcard_map)) {
                $user_flashcard_map[$user_flashcard['user_id']] = [];
            }
            $user_flashcard_map[$user_flashcard['user_id']][] = $user_flashcard['flashcard_id'];
        }

        foreach ($user_flashcard_map as $user_id => $flashcard_ids) {
            $user_id = new UserId($user_id);

            $sm_two_flashcards = $this->repository->findMany($user_id, $flashcard_ids);

            foreach ($dto->getRatedSessionFlashcardIds() as $session_flashcard_id) {
                $sm_two_flashcards->fillIfMissing($user_id, $dto->getFlashcardId($session_flashcard_id));

                $sm_two_flashcards->updateByRating(
                    $dto->getFlashcardId($session_flashcard_id),
                    $dto->getFlashcardRating($session_flashcard_id)
                );
            }

            $this->repository->saveMany($sm_two_flashcards);

            $this->poll_updater->handle($dto);
        }
    }
}
