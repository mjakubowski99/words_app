<?php

declare(strict_types=1);

namespace Tests\Smoke\Admin\Resources;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Admin\Resources\UserResource;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserResourceTest extends TestCase
{
    use DatabaseTransactions;

    public function test__success(): void
    {
        // GIVEN
        User::factory()->create();
        $admin = Admin::factory()->create();

        // WHEN
        $response = $this->actingAs($admin, 'admin')
            ->get(UserResource::getUrl('index'));

        // THEN
        $response->assertSuccessful();
    }
}
