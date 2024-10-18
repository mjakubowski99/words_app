<?php

declare(strict_types=1);

namespace App\Console\Traits;

trait EnsureDatabaseDriver
{
    public function ensureDefaultDriverIsPostgres(): void
    {
        if (config('database.default') !== 'pgsql') {
            throw new \Exception('Command is only for sql database driver!. If you have changed your DB driver, please review this code again if it still works');
        }
    }
}
