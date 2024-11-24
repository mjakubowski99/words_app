<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v1;

use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Flashcard\Domain\Models\Owner;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Application\Command\CreateFlashcard;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

#[OAT\Schema(
    schema: 'Requests\Flashcard\StoreFlashcardRequest',
    properties: [
        new OAT\Property(
            property: 'flashcard_category_id',
            description: 'Category id to update',
            type: 'integer',
            example: 1,
        ),
        new OAT\Property(
            property: 'word',
            description: 'Flashcard word',
            type: 'string',
            example: 'Jabłko',
        ),
        new OAT\Property(
            property: 'translation',
            description: 'Flashcard word translation',
            type: 'string',
            example: 'Apple',
        ),
        new OAT\Property(
            property: 'context',
            description: 'Word context',
            type: 'string',
            example: 'Adam je jabłko',
        ),
        new OAT\Property(
            property: 'context_translation',
            description: 'Word context translation',
            type: 'string',
            example: 'Adam eats apple',
        ),
    ]
)]
class StoreFlashcardRequest extends Request
{
    public function rules(): array
    {
        return [
            'flashcard_category_id' => ['required', 'integer'],
            'word' => ['required', 'string', 'max:255'],
            'context' => ['required', 'string', 'max:255'],
            'translation' => ['required', 'string', 'max:255'],
            'context_translation' => ['required', 'string', 'max:255'],
        ];
    }

    public function toCommand(): CreateFlashcard
    {
        $user = $this->current();

        return new CreateFlashcard(
            Owner::fromUser($user->getId()),
            new FlashcardDeckId($this->input('flashcard_category_id')),
            Language::pl(),
            $this->input('word'),
            $this->input('context'),
            Language::en(),
            $this->input('translation'),
            $this->input('context_translation')
        );
    }
}
