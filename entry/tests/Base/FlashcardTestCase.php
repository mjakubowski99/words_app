<?php

declare(strict_types=1);

namespace Tests\Base;

use Tests\TestCase;
use App\Models\User;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\SmTwoFlashcard;
use Shared\Enum\LanguageLevel;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Deck;
use Shared\Utils\ValueObjects\UserId;
use App\Models\LearningSessionFlashcard;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;

abstract class FlashcardTestCase extends TestCase
{
    public function createUserFlashcard(User $user, array $attributes = []): Flashcard
    {
        return Flashcard::factory()->byUser($user)->create($attributes);
    }

    public function createFlashcard(array $attributes = []): Flashcard
    {
        return Flashcard::factory()->create($attributes);
    }

    public function createSmTwoFlashcard(array $attributes = []): SmTwoFlashcard
    {
        return SmTwoFlashcard::factory()->create($attributes);
    }

    public function createUserDeck(User $user, array $attributes = []): FlashcardDeck
    {
        return FlashcardDeck::factory()->byUser($user)->create($attributes);
    }

    public function createFlashcardDeck(array $attributes = []): FlashcardDeck
    {
        return FlashcardDeck::factory()->create($attributes);
    }

    public function createLearningSession(array $attributes = []): LearningSession
    {
        return LearningSession::factory()->create($attributes);
    }

    public function createLearningSessionFlashcard(array $attributes = []): LearningSessionFlashcard
    {
        return LearningSessionFlashcard::factory()->create($attributes);
    }

    public function createSessionFlashcardId(LearningSessionFlashcard $flashcard): SessionFlashcardId
    {
        return new SessionFlashcardId($flashcard->id);
    }

    public function createUserId(User $user): UserId
    {
        return new UserId($user->id);
    }

    public function createSmTwoFlashcardId(SmTwoFlashcard $flashcard): FlashcardId
    {
        return new FlashcardId($flashcard->flashcard_id);
    }

    public function createSessionId(LearningSession $session): SessionId
    {
        return new SessionId($session->id);
    }

    public function createDeckId(FlashcardDeck $deck): FlashcardDeckId
    {
        return new FlashcardDeckId($deck->id);
    }

    public function domainDeck(FlashcardDeck $deck): Deck
    {
        return (new Deck(
            $deck->user->toOwner(),
            $deck->tag,
            $deck->name,
            LanguageLevel::from($deck->default_language_level)
        ))->init(new FlashcardDeckId($deck->id));
    }
}
