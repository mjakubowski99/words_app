<?php

declare(strict_types=1);
use App\Models\Admin;
use App\Models\Report;
use Admin\Resources\ReportResource;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('success', function () {
    // GIVEN
    Report::factory()->create();
    $admin = Admin::factory()->create();

    // WHEN
    $response = $this->actingAs($admin, 'admin')
        ->get(ReportResource::getUrl('index'));

    // THEN
    $response->assertSuccessful();
});
