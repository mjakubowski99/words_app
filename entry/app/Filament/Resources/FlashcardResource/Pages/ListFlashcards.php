<?php

namespace App\Filament\Resources\FlashcardResource\Pages;

use App\Filament\Resources\FlashcardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlashcards extends ListRecords
{
    protected static string $resource = FlashcardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
