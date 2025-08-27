<?php

declare(strict_types=1);
use App\Models\Report;
use Livewire\Livewire;
use Admin\Resources\ReportResource\Pages\ListReports;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('success', function () {
    // GIVEN
    $reports = [Report::factory()->create(), Report::factory()->create()];

    // WHEN
    Livewire::test(ListReports::class)
        ->assertCanSeeTableRecords($reports);
});
