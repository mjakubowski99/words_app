<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources;

use Flashcard\Application\DTO\FlashcardCategoryDetailsDTO;
use Flashcard\Application\DTO\FlashcardDTO;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property FlashcardCategoryDetailsDTO $resource
 */
class CategoryDetailsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getCategoryId()->getValue(),
            'name' => $this->resource->getName(),
            'flashcards' => array_map(function (FlashcardDTO $flashcard){
                return [
                    'id' => $flashcard->getId()->getValue(),
                    'word' => $flashcard->getWord(),
                    'word_lang' => $flashcard->getWordLang()->getValue(),
                    'translation' => $flashcard->getTranslation(),
                    'translation_lang' => $flashcard->getTranslationLang()->getValue(),
                    'context' => $flashcard->getContext(),
                    'context_translation' => $flashcard->getContextTranslation(),
                ];
            }, $this->resource->getFlashcards())
        ];
    }
}