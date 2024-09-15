<?php

declare(strict_types=1);

namespace Tests\Base;

use App\Models\FlashcardCategory;
use App\Models\LearningSession;
use App\Models\LearningSessionFlashcard;
use App\Models\SmTwoFlashcard;
use App\Models\User;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\ValueObjects\SessionId;
use Shared\Utils\ValueObjects\UserId;
use Tests\TestCase;

abstract class FlashcardTestCase extends TestCase
{
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

    public function domainCategory(FlashcardCategory $category): \Flashcard\Domain\Models\Category
    {
        return (new \Flashcard\Domain\Models\Category(
            $category->user->toOwner(),
            $category->tag,
            $category->name,
        ))->init(new CategoryId($category->id));
    }
}
