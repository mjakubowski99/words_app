<?php

namespace App\Filament\Resources;

use App\Filament\Imports\FlashcardDeckImporter;
use App\Filament\Resources\FlashcardDeckResource\Pages;
use App\Filament\Resources\FlashcardDeckResource\RelationManagers;
use App\Models\FlashcardDeck;
use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class FlashcardDeckResource extends Resource
{
    protected static ?string $model = FlashcardDeck::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function readCSV($csvFile, $delimiter = ',')
    {
        $file_handle = fopen($csvFile, 'r');
        while ($csvRow = fgetcsv($file_handle, null, $delimiter)) {
            $line_of_text[] = $csvRow;
        }
        fclose($file_handle);
        return $line_of_text;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->headerActions([
                Tables\Actions\Action::make('Import')
                    ->form([
                        Forms\Components\FileUpload::make('import_file'),
                    ])->action(function (array $data) {
                        $file = Storage::disk('public')->path($data['import_file']);

                        dump(self::readCSV($file));
                    })
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FlashcardsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlashcardDecks::route('/'),
            'create' => Pages\CreateFlashcardDeck::route('/create'),
            'edit' => Pages\EditFlashcardDeck::route('/{record}/edit'),
        ];
    }
}
