<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Shared\User\IUserFacade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Shared\Utils\ValueObjects\UserId;
use App\Console\Traits\EnsureDatabaseDriver;

class GenerateTestingUserCommand extends Command
{
    use EnsureDatabaseDriver;

    protected $signature = 'app:testing-user';

    protected $description = 'Generate testing user';

    public function handle(IUserFacade $user_facade): void
    {
        $this->ensureDefaultDriverIsPostgres();

        if (config('app.env') !== 'local') {
            $this->error('This action is only allowed for local environments!');
        }

        $email = 'email@email.com';
        $password = 'password123';

        if (!User::query()->where('email', $email)->exists()) {
            $user = User::factory()->create([
                'email' => 'email@email.com',
                'password' => Hash::make($password),
            ]);
        } else {
            $user = User::query()->where('email', $email)->first();
        }

        $token = $user_facade->issueToken(new UserId($user->id));

        $this->info('Your testing token. Use this to authorize to api: ');
        $this->info($token);
    }
}
