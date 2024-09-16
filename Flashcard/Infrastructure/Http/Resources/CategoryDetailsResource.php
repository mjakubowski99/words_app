<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources;

use OpenApi\Attributes as OAT;
use Shared\Utils\ValueObjects\Language;
use Illuminate\Http\Resources\Json\JsonResource;
use Flashcard\Application\ReadModels\FlashcardRead;
use Flashcard\Application\ReadModels\CategoryDetailsRead;

#[OAT\Schema(
    schema: 'Resources\Flashcard\CategoryDetailsResource',
    properties: [
        new OAT\Property(
            property: 'id',
            description: 'ID of the session',
            type: 'integer',
            example: 10,
        ),
        new OAT\Property(
            property: 'name',
            description: 'Category name',
            type: 'string',
            example: 'Two people talk',
        ),
        new OAT\Property(
            property: 'flashcards',
            description: 'List of flashcards in the session',
            type: 'array',
            items: new OAT\Items(
                properties: [
                    new OAT\Property(
                        property: 'id',
                        description: 'Flashcard ID',
                        type: 'integer',
                        example: 11,
                    ),
                    new OAT\Property(
                        property: 'word',
                        description: 'Word on the flashcard',
                        type: 'string',
                        example: 'Polish'
                    ),
                    new OAT\Property(
                        property: 'word_lang',
                        description: 'Language of the word',
                        enum: [Language::EN, Language::PL],
                        example: Language::EN,
                    ),
                    new OAT\Property(
                        property: 'translation',
                        description: 'Translation of the word',
                        type: 'string',
                        example: 'Polska',
                    ),
                    new OAT\Property(
                        property: 'translation_lang',
                        description: 'Language of the translation',
                        type: 'string',
                        enum: [Language::EN, Language::PL],
                        example: Language::PL,
                    ),
                    new OAT\Property(
                        property: 'context',
                        description: 'Context sentence for the word',
                        type: 'string',
                        example: 'Country in the center of Europe'
                    ),
                    new OAT\Property(
                        property: 'context_translation',
                        description: 'Translation of the context sentence',
                        type: 'string',
                        example: 'Kraj w centrum Europy'
                    ),
                ],
                type: 'object'
            )
        ),
    ]
)]
/**
 * @property CategoryDetailsRead $resource
 */
class CategoryDetailsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId()->getValue(),
            'name' => $this->resource->getName(),
            'flashcards' => array_map(function (FlashcardRead $flashcard) {
                return [
                    'id' => $flashcard->getId()->getValue(),
                    'word' => $flashcard->getWord(),
                    'word_lang' => $flashcard->getWordLang()->getValue(),
                    'translation' => $flashcard->getTranslation(),
                    'translation_lang' => $flashcard->getTranslationLang()->getValue(),
                    'context' => $flashcard->getContext(),
                    'context_translation' => $flashcard->getContextTranslation(),
                ];
            }, $this->resource->getFlashcards()),
        ];
    }
}
