<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request;

use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Flashcard\Domain\Models\Owner;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Application\Command\UpdateFlashcard;

#[OAT\Schema(
    schema: 'Requests\Flashcard\UpdateFlashcardRequest',
    properties: [
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
class UpdateFlashcardRequest extends Request
{
    public function rules(): array
    {
        return [
            'word' => ['required', 'string', 'max:255'],
            'context' => ['required', 'string', 'max:255'],
            'translation' => ['required', 'string', 'max:255'],
            'context_translation' => ['required', 'string', 'max:255'],
        ];
    }

    public function toCommand(): UpdateFlashcard
    {
        $user = $this->current();

        return new UpdateFlashcard(
            new FlashcardId((int) $this->route('flashcard_id')),
            Owner::fromUser($user->getId()),
            new CategoryId($this->input('flashcard_category_id')),
            Language::pl(),
            $this->input('word'),
            $this->input('context'),
            Language::en(),
            $this->input('translation'),
            $this->input('context_translation')
        );
    }
}
