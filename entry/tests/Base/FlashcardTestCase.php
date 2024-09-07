<?php

declare(strict_types=1);

namespace Tests\Base;

use Tests\TestCase;
use App\Models\User;
use App\Models\SmTwoFlashcard;
use App\Models\LearningSession;
use App\Models\FlashcardCategory;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\SessionId;
use Flashcard\Domain\Models\CategoryId;
use App\Models\LearningSessionFlashcard;
use Flashcard\Domain\Models\FlashcardId;
use Flashcard\Domain\Models\SessionFlashcardId;

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

    public function domainCategory(FlashcardCategory $category): \Flashcard\Domain\Models\FlashcardCategory
    {
        return (new \Flashcard\Domain\Models\FlashcardCategory(
            new UserId($category->user_id),
            $category->tag,
            $category->name,
        ))->init(new CategoryId($category->id));
    }
}
