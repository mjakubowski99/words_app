<?php

declare(strict_types=1);

namespace Admin\Resources\FlashcardDeckResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Admin\Traits\HasFlashcardConfigurator;
use Filament\Resources\RelationManagers\RelationManager;

class FlashcardsRelationManager extends RelationManager
{
    use HasFlashcardConfigurator;

    protected static string $relationship = 'flashcards';

    public function form(Form $form): Form
    {
        return $form->schema(self::formFields());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns(self::tableColumns())
            ->actions([
                self::buildEditAction(),
                self::buildDeleteAction(),
            ]);
    }
}
