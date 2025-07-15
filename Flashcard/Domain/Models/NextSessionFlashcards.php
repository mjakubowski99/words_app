<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Enum\SessionType;
use Shared\Enum\ExerciseType;
use Shared\Exercise\IFlashcardExercise;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Flashcard\Domain\Exceptions\InvalidNextSessionFlashcards;
use Flashcard\Domain\Exceptions\TooManySessionFlashcardsException;

class NextSessionFlashcards extends SessionFlashcardsBase
{
    public const UNRATED_LIMIT = 5;

    /** @var NextSessionFlashcard[] */
    private array $next_session_flashcards = [];
    private array $additional_flashcards = [];

    public function __construct(
        private SessionId $session_id,
        private SessionType $type,
        private UserId $user_id,
        private ?Deck $deck,
        private int $current_session_flashcards_count,
        private int $unrated_count,
        private int $max_flashcards_count,
    ) {
        if (!$this->isValid()) {
            throw new InvalidNextSessionFlashcards(
                "Cannot generate next session flashcards for session: {$this->session_id->getValue()}"
            );
        }
    }

    public function getSessionId(): SessionId
    {
        return $this->session_id;
    }

    public function getSessionType(): SessionType
    {
        return $this->type;
    }

    public function getMaxFlashcardsCount(): int
    {
        return $this->max_flashcards_count;
    }

    public function getUserId(): UserId
    {
        return $this->user_id;
    }

    public function getUnratedCount(): int
    {
        return $this->unrated_count;
    }

    public function getCurrentSessionFlashcardsCount(): int
    {
        return $this->current_session_flashcards_count;
    }

    public function hasDeck(): bool
    {
        return $this->deck !== null;
    }

    public function getDeck(): Deck
    {
        return $this->deck;
    }

    public function isValid(): bool
    {
        if ($this->unrated_count > self::UNRATED_LIMIT) {
            return false;
        }

        return $this->current_session_flashcards_count <= $this->max_flashcards_count;
    }

    public function canAddNext(): bool
    {
        if ($this->unrated_count + 1 > self::UNRATED_LIMIT) {
            return false;
        }

        return $this->current_session_flashcards_count + 1 <= $this->max_flashcards_count;
    }

    public function associateExercises(array $entries, ExerciseType $type): void
    {
        foreach ($entries as $entry) {
            $this->associateExercise(
                new FlashcardId($entry->getFlashcardId()),
                $entry->getExerciseEntryId(),
                $type
            );
        }
    }

    public function associateExercise(FlashcardId $flashcard_id, ExerciseEntryId $exercise_entry_id, ExerciseType $type): void
    {
        foreach ($this->next_session_flashcards as $flashcard) {
            if ($flashcard_id->equals($flashcard->getFlashcardId())) {
                $flashcard->setExercise($exercise_entry_id, $type);

                return;
            }
        }
        foreach ($this->additional_flashcards as $flashcard) {
            if ($flashcard_id->equals($flashcard->getFlashcardId())) {
                $flashcard->setExercise($exercise_entry_id, $type);

                return;
            }
        }
    }

    public function addNext(Flashcard $flashcard): void
    {
        if (!$this->canAddNext()) {
            throw new TooManySessionFlashcardsException();
        }

        $this->next_session_flashcards[] = new NextSessionFlashcard(
            $flashcard->getId()
        );
        ++$this->current_session_flashcards_count;

        ++$this->unrated_count;
    }

    public function addNextAdditional(Flashcard $flashcard): void
    {
        $this->additional_flashcards[] = new NextSessionFlashcard(
            $flashcard->getId()
        );
    }

    public function isMixedSessionType(): bool
    {
        return $this->type === SessionType::MIXED;
    }

    public function resolveNextExerciseType(): ?ExerciseType
    {
        $type = $this->type;

        if ($type === SessionType::MIXED) {
            $type = SessionType::allowedInMixed()[array_rand(SessionType::allowedInMixed())];
        }

        if ($type === SessionType::UNSCRAMBLE_WORDS) {
            return ExerciseType::UNSCRAMBLE_WORDS;
        }
        if ($type === SessionType::WORD_MATCH) {
            return ExerciseType::WORD_MATCH;
        }

        return null;
    }

    /** @return NextSessionFlashcard[] */
    public function getNextFlashcards(): array
    {
        return $this->next_session_flashcards;
    }

    public function getAdditionalFlashcards(): array
    {
        return $this->additional_flashcards;
    }
}
