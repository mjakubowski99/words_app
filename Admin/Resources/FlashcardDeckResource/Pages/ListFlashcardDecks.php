<?php

declare(strict_types=1);

namespace Admin\Resources\FlashcardDeckResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Admin\Resources\FlashcardDeckResource;

class ListFlashcardDecks extends ListRecords
{
    protected static string $resource = FlashcardDeckResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
