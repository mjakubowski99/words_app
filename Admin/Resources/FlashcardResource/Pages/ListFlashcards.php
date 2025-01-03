<?php

declare(strict_types=1);

namespace Admin\Resources\FlashcardResource\Pages;

use Admin\Resources\FlashcardResource;
use Filament\Resources\Pages\ListRecords;

class ListFlashcards extends ListRecords
{
    protected static string $resource = FlashcardResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
