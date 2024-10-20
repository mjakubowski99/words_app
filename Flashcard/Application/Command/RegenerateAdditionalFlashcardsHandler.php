<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Owner;
use Shared\Exceptions\ForbiddenException;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Application\Services\CategoryResolver;
use Flashcard\Application\Services\FlashcardGeneratorService;

class RegenerateAdditionalFlashcardsHandler
{
    public function __construct(
        private CategoryResolver $category_resolver,
        private FlashcardGeneratorService $flashcard_generator_service,
    ) {}

    public function handle(Owner $owner, CategoryId $id): void
    {
        $resolved_category = $this->category_resolver->resolveById($id);

        if (!$resolved_category->getCategory()->getOwner()->equals($owner)) {
            throw new ForbiddenException('You must be category owner');
        }

        $this->flashcard_generator_service->generate(
            $resolved_category,
            $resolved_category->getCategory()->getName()
        );
    }
}
