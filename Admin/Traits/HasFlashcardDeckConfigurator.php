<?php

declare(strict_types=1);

namespace Admin\Traits;

use Filament\Actions\Action;
use Shared\User\IUserFacade;
use Shared\Enum\LanguageLevel;
use Admin\Models\FlashcardDeck;
use Filament\Actions\BulkAction;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Spatie\SimpleExcel\SimpleExcelReader;
use Shared\Flashcard\IFlashcardAdminFacade;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HasFlashcardDeckConfigurator
{
    public static function tableColumns(): array
    {
        return [
            TextColumn::make('id')->sortable(),
            TextColumn::make('name')->sortable(),
            TextColumn::make('default_language_level')->sortable(),
        ];
    }

    public static function formFields(): array
    {
        return [
            TextInput::make('name'),
        ];
    }

    public static function buildImportAction(): Action
    {
        return Action::make('Import')
            ->schema([
                TextInput::make('name')->required(),
                Select::make('language_level')->options(LanguageLevel::getForSelectOptions())->required(),
                Select::make('owner_user')->options(array_combine(config('app.privileged_emails'), config('app.privileged_emails')))
                    ->nullable()
                    ->helperText('This is optional, when we as admins want to import decks for us'),
                FileUpload::make('import_file')
                    ->required()
                    ->acceptedFileTypes([
                        'text/csv',
                    ])
                    ->helperText(
                        'File should be CSV file with given structure: '
                        . 'front_word(word on front of the card), '
                        . 'front_context(context sentence on the end of the card), '
                        . 'back_word(word on the back of the card), '
                        . 'back_context(context sentence on the back of the card). '
                        . 'emoji(optional can be omitted).'
                        . "If those headers are missing import will fail\n"
                    ),
            ])->action(function (array $data) {
                $file = Storage::disk('public')->path($data['import_file']);

                $rows = SimpleExcelReader::create($file)->getRows()->toArray();

                /** @var IFlashcardAdminFacade $facade */
                $facade = app()->make(IFlashcardAdminFacade::class);

                if ($data['owner_user']) {
                    $user = app()->make(IUserFacade::class);
                    $user = $user->findByEmail($data['owner_user']);

                    $facade->importDeckForUser(
                        $user->getId()->getValue(),
                        $data['name'],
                        LanguageLevel::from($data['language_level']),
                        $rows
                    );
                } else {
                    $facade->importDeck(
                        auth()->id(),
                        $data['name'],
                        LanguageLevel::from($data['language_level']),
                        $rows
                    );
                }
            });
    }

    public static function buildExportAction(): BulkAction
    {
        return BulkAction::make('Export')
            ->action(function (Collection $records): StreamedResponse {
                $csvData = [];
                $csvData[] = ['id', 'name'];

                foreach ($records as $deck) {
                    $csvData[] = [
                        $deck->id,
                        $deck->name,
                    ];
                }

                return response()->streamDownload(function () use ($csvData) {
                    $handle = fopen('php://output', 'w');
                    foreach ($csvData as $row) {
                        fputcsv($handle, $row);
                    }
                    fclose($handle);
                }, 'flashcard_decks-' . now()->format('Y-m-d-H-i-s') . '.csv');
            });
    }

    public static function buildUpsertDeckNames(): Action
    {
        return Action::make('Upsert deck details')
            ->schema([
                FileUpload::make('import_file')
                    ->required()
                    ->acceptedFileTypes([
                        'text/csv',
                    ])
                    ->helperText(
                        'Select records. Export them with a export button, edit details in excel'
                        . ' and use this action to import updated names in quick way'
                    ),
            ])->action(function (array $data) {
                $file = Storage::disk('public')->path($data['import_file']);

                $rows = SimpleExcelReader::create($file)->getRows()->toArray();

                foreach ($rows as $row) {
                    FlashcardDeck::query()
                        ->whereNull('user_id')
                        ->where('id', $row['id'])
                        ->update(['name' => $row['name']]);
                }
            });
    }
}
