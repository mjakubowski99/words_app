<?php

declare(strict_types=1);
use App\Models\User;
use App\Models\Admin;
use Admin\Resources\UserResource;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('success', function () {
    // GIVEN
    User::factory()->create();
    $admin = Admin::factory()->create();

    // WHEN
    $response = $this->actingAs($admin, 'admin')
        ->get(UserResource::getUrl('index'));

    // THEN
    $response->assertSuccessful();
});
