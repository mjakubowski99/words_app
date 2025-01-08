<?php

declare(strict_types=1);

namespace Tests\Smoke\Admin\Resources\ReportsResource\ListReports;

use Tests\TestCase;
use App\Models\Report;
use Livewire\Livewire;
use Admin\Resources\ReportResource;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ListReportsTest extends TestCase
{
    use DatabaseTransactions;

    public function test__success(): void
    {
        // GIVEN
        $reports = [Report::factory()->create(), Report::factory()->create()];

        // WHEN
        Livewire::test(ReportResource\Pages\ListReports::class)
            ->assertCanSeeTableRecords($reports);
    }
}
