<?php

declare(strict_types=1);

namespace Admin\Resources\FlashcardDeckResource\Pages;

use Admin\Resources\FlashcardDeckResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFlashcardDeck extends CreateRecord
{
    protected static string $resource = FlashcardDeckResource::class;
}
