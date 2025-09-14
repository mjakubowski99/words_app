<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v2;

use OpenApi\Attributes as OAT;
use Shared\Enum\LanguageLevel;
use Shared\Http\Request\Request;
use Flashcard\Domain\Models\Owner;
use Flashcard\Application\Command\CreateFlashcard;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

#[OAT\Schema(
    schema: 'Requests\Flashcard\v2\StoreFlashcardRequest',
    properties: [
        new OAT\Property(
            property: 'flashcard_deck_id',
            description: 'Deck id to update',
            type: 'integer',
            example: 1,
        ),
        new OAT\Property(
            property: 'front_word',
            description: 'Flashcard front word',
            type: 'string',
            example: 'Jabłko',
        ),
        new OAT\Property(
            property: 'back_word',
            description: 'Flashcard back word',
            type: 'string',
            example: 'Apple',
        ),
        new OAT\Property(
            property: 'front_context',
            description: 'Front flashcard context sentence',
            type: 'string',
            example: 'Adam je jabłko',
        ),
        new OAT\Property(
            property: 'back_context',
            description: 'Back flashcard context sentence',
            type: 'string',
            example: 'Adam eats apple',
        ),
        new OAT\Property(
            property: 'language_level',
            ref: '#/components/schemas/LanguageLevel'
        ),
        new OAT\Property(
            property: 'emoji',
            description: 'Flashcard context emoji',
            type: 'string',
            example: '❤️',
            nullable: true
        ),
    ]
)]
class StoreFlashcardRequest extends Request
{
    public function rules(): array
    {
        return [
            'flashcard_deck_id' => ['required', 'integer'],
            'front_word' => ['required', 'string', 'max:255'],
            'back_word' => ['required', 'string', 'max:255'],
            'front_context' => ['required', 'string', 'max:255'],
            'back_context' => ['required', 'string', 'max:255'],
            'language_level' => ['nullable', 'string'],
            'emoji' => ['nullable'],
        ];
    }

    public function toCommand(): CreateFlashcard
    {
        $user = $this->current();

        return new CreateFlashcard(
            Owner::fromUser($user->getId()),
            new FlashcardDeckId($this->input('flashcard_deck_id')),
            $user->getUserLanguage(),
            $this->input('front_word'),
            $this->input('front_context'),
            $user->getLearningLanguage(),
            $this->input('back_word'),
            $this->input('back_context'),
            $this->input('language_level') ? LanguageLevel::from($this->input('language_level')) : LanguageLevel::default(),
            $this->input('emoji'),
        );
    }
}
