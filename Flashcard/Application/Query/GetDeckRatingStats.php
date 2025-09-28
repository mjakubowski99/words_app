<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Shared\Enum\Language;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;
use Flashcard\Application\Repository\IFlashcardDeckReadRepository;

readonly class GetDeckRatingStats
{
    public function __construct(private IFlashcardDeckReadRepository $repository) {}

    public function get(FlashcardDeckId $id, Language $front_lang, Language $back_lang): RatingStatsReadCollection
    {
        return $this->repository->findRatingStats($id, $front_lang, $back_lang);
    }
}
