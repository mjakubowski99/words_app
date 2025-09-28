<?php

declare(strict_types=1);

namespace Admin\Resources\FlashcardDeckResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Admin\Traits\HasFlashcardConfigurator;
use Filament\Resources\RelationManagers\RelationManager;

class FlashcardsRelationManager extends RelationManager
{
    use HasFlashcardConfigurator;

    protected static string $relationship = 'flashcards';

    public function form(Schema $schema): Schema
    {
        return $schema->components(self::formFields());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns(self::tableColumns())
            ->recordActions([
                self::buildEditAction(),
                self::buildDeleteAction(),
            ]);
    }
}
