<?php

namespace App\Filament\Resources\FlashcardDeckResource\Pages;

use App\Filament\Resources\FlashcardDeckResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlashcardDecks extends ListRecords
{
    protected static string $resource = FlashcardDeckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
