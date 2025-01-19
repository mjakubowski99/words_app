<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v2;

use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Flashcard\Domain\ValueObjects\FlashcardId;

#[OAT\Schema(
    schema: 'Requests\Flashcard\v2\BulkDeleteFlashcardRequest',
    required: ['flashcard_ids'],
    properties: [
        new OAT\Property(
            property: 'flashcard_ids',
            description: 'Flashcard ids to delete',
            type: 'array',
            items: new OAT\Items(
                type: 'integer'
            ),
            example: [1, 2]
        ),
    ]
)]
class BulkDeleteFlashcardRequest extends Request
{
    public function rules(): array
    {
        return [
            'flashcard_ids' => ['required', 'array', 'min:1'],
        ];
    }

    /** @return FlashcardId[] */
    public function getFlashcardIds(): array
    {
        return array_map(function ($flashcard_id) {
            return new FlashcardId((int) $flashcard_id);
        }, $this->input('flashcard_ids'));
    }
}
