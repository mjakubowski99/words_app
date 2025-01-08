<?php

declare(strict_types=1);

namespace Tests\Smoke\Admin\Resources\ReportsResource;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Report;
use Admin\Resources\ReportResource;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportsResourceTest extends TestCase
{
    use DatabaseTransactions;

    public function test__success(): void
    {
        // GIVEN
        Report::factory()->create();
        $admin = Admin::factory()->create();

        // WHEN
        $response = $this->actingAs($admin, 'admin')
            ->get(ReportResource::getUrl('index'));

        // THEN
        $response->assertSuccessful();
    }
}
