<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;
use Flashcard\Application\Repository\IFlashcardDeckReadRepository;
use Shared\Enum\Language;
use Shared\User\IUser;

readonly class GetDeckRatingStats
{
    public function __construct(private IFlashcardDeckReadRepository $repository) {}

    public function get(FlashcardDeckId $id, Language $front_lang, Language $back_lang): RatingStatsReadCollection
    {
        return $this->repository->findRatingStats($id, $front_lang, $back_lang);
    }
}
