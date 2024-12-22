<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OutboxPattern\Consumer;

class ConsumerCommand extends Command
{
    protected $signature = 'app:consumer-command';

    protected $description = 'Command description';

    public function handle(Consumer $consumer): void
    {
        $consumer->process(500);
    }
}
