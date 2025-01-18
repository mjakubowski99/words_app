<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v2;

use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

#[OAT\Schema(
    schema: 'Requests\Flashcard\v2\BulkDeleteDeckRequest',
    required: ['flashcard_deck_ids'],
    properties: [
        new OAT\Property(
            property: 'flashcard_deck_ids',
            description: 'Flashcard deck ids to delete',
            type: 'array',
            items: new OAT\Items(
                type: 'integer'
            ),
            example: [1, 2]
        ),
    ]
)]
class BulkDeleteDeckRequest extends Request
{
    public function rules(): array
    {
        return [
            'flashcard_deck_ids' => ['required', 'array', 'min:1'],
        ];
    }

    /** @return FlashcardDeckId[] */
    public function getDeckIds(): array
    {
        return array_map(function ($deck_id) {
            return new FlashcardDeckId((int) $deck_id);
        }, $this->input('flashcard_deck_ids'));
    }
}
