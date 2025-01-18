<?php

namespace Flashcard\Infrastructure\Http\Request\v2;

use Flashcard\Application\Command\CreateDeckCommand;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\LanguageLevel;
use Shared\Http\Request\Request;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Requests\Flashcard\v2\StoreFlashcardDeckRequest',
    properties: [
        new OAT\Property(
            property: 'name',
            description: 'Flashcard deck name provided by user',
            type: 'string',
            example: 'Two people talk',
        ),
        new OAT\Property(
            property: 'language_level',
            ref: '#/components/schemas/LanguageLevel'
        ),
    ]
)]
class StoreFlashcardDeckRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'default_language_level' => ['required', 'string']
        ];
    }

    public function toCommand(): CreateDeckCommand
    {
        return new CreateDeckCommand(
            Owner::fromUser($this->currentId()),
            $this->input('name'),
            $this->input('name'),
            LanguageLevel::from($this->input('default_language_level'))
        );
    }
}