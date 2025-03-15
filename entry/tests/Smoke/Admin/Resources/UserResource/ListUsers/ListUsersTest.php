<?php

declare(strict_types=1);

namespace Tests\Smoke\Admin\Resources\UserResource\ListUsers;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use Admin\Resources\UserResource\Pages\ListUsers;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ListUsersTest extends TestCase
{
    use DatabaseTransactions;

    public function test__success(): void
    {
        $users = [User::factory()->create(), User::factory()->create()];

        Livewire::test(ListUsers::class)->assertSuccessful();
    }
}
