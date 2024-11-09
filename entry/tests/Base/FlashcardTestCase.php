<?php

declare(strict_types=1);

namespace Tests\Base;

use Tests\TestCase;
use App\Models\User;
use App\Models\Flashcard;
use App\Models\SmTwoFlashcard;
use App\Models\LearningSession;
use App\Models\FlashcardCategory;
use Flashcard\Domain\Models\Category;
use Shared\Utils\ValueObjects\UserId;
use App\Models\LearningSessionFlashcard;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;

abstract class FlashcardTestCase extends TestCase
{
    public function createFlashcard(array $attributes = []): Flashcard
    {
        return Flashcard::factory()->create($attributes);
    }

    public function createFlashcardCategory(array $attributes = []): FlashcardCategory
    {
        return FlashcardCategory::factory()->create($attributes);
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

    public function createCategoryId(FlashcardCategory $category): CategoryId
    {
        return new CategoryId($category->id);
    }

    public function domainCategory(FlashcardCategory $category): Category
    {
        return (new Category(
            $category->user->toOwner(),
            $category->tag,
            $category->name,
        ))->init(new CategoryId($category->id));
    }
}
