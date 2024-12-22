<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OutboxPattern\OutboxMessageType;
use OutboxPattern\Publisher;

class PublisherCommand extends Command
{
    protected $signature = 'app:publisher-command';

    protected $description = 'Command description';

    public function handle(Publisher $publisher)
    {
        for($i=0; $i<1000; $i++) {
            $publisher->publish(OutboxMessageType::DEFAULT, []);
        }
    }
}
