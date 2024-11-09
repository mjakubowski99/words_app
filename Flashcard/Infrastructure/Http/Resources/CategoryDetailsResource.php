<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources;

use OpenApi\Attributes as OAT;
use Shared\Enum\GeneralRatingType;
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
                    new OAT\Property(
                        property: 'rating',
                        description: 'Get general rating of flashcard. (NEW means that flashcard is not rated)',
                        type: 'string',
                        enum: [
                            GeneralRatingType::NEW,
                            GeneralRatingType::VERY_GOOD,
                            GeneralRatingType::GOOD,
                            GeneralRatingType::WEAK,
                            GeneralRatingType::UNKNOWN,
                        ],
                        example: 'NEW',
                    ),
                ],
                type: 'object'
            ),
        ),
        new OAT\Property(
            property: 'page',
            type: 'integer',
            example: 1,
        ),
        new OAT\Property(
            property: 'per_page',
            type: 'integer',
            example: 15,
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
        /** @var CategoryDetailsRead $category */
        $category = $this->resource['details'];

        /** @var int $page */
        $page = $this->resource['page'];

        /** @var int $page */
        $per_page = $this->resource['per_page'];

        return [
            'id' => $category->getId()->getValue(),
            'name' => $category->getName(),
            'flashcards' => array_map(function (FlashcardRead $flashcard) {
                return [
                    'id' => $flashcard->getId()->getValue(),
                    'word' => $flashcard->getWord(),
                    'word_lang' => $flashcard->getWordLang()->getValue(),
                    'translation' => $flashcard->getTranslation(),
                    'translation_lang' => $flashcard->getTranslationLang()->getValue(),
                    'context' => $flashcard->getContext(),
                    'context_translation' => $flashcard->getContextTranslation(),
                    'rating' => $flashcard->getGeneralRating()->getValue()->value,
                ];
            }, $category->getFlashcards()),
            'page' => $page,
            'per_page' => $per_page,
        ];
    }
}
