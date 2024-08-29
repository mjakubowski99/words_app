<?php

declare(strict_types=1);

namespace Tests\Base;

use App\Models\FlashcardCategory;
use App\Models\LearningSession;
use App\Models\User;
use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\SessionId;
use Shared\Utils\ValueObjects\UserId;
use Tests\TestCase;

abstract class FlashcardTestCase extends TestCase
{
    public function createUserId(User $user): UserId
    {
        return new UserId($user->id);
    }

    public function createSessionId(LearningSession $session): SessionId
    {
        return new SessionId((string) $session->id);
    }

    public function createCategoryId(FlashcardCategory $category): CategoryId
    {
        return new CategoryId((string) $category->id);
    }

    public function domainCategory(FlashcardCategory $category): \Flashcard\Domain\Models\FlashcardCategory
    {
        $domain_category = new \Flashcard\Domain\Models\FlashcardCategory(
            new UserId($category->user_id),
            $category->tag,
            $category->name,
        );
        $domain_category->setCategoryId(new CategoryId($category->id));

        return $domain_category;
    }
}
