<?php

declare(strict_types=1);

namespace OutboxPattern;

use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class Consumer
{
    public function __construct(
        private readonly OutboxMessage $message,
        private ProcessorFactory $factory,
        private readonly DatabaseManager $db,
    ) {}

    public function process(int $limit): void
    {
        $messages = $this->db->transaction(function () use ($limit) {
            $messages = $this->message
                ->newQuery()
                ->lockForUpdate()
                ->timeToProcess()
                ->notLocked()
                ->pending()
                ->limit($limit)
                ->get();

            $this->message
                ->whereIn('id', $messages->pluck('id'))
                ->update([
                    'locked_until' => now()->addHour(),
                ]);

            return $messages;
        });

        /** @var OutboxMessage $message */
        foreach ($messages as $message) {
            $message->refresh();

            $message->incrementAttempts();

            $processor = $this->factory->make($message->getType());

            $result = $processor->process($message);

            if ($result->success()) {
                $message->markProcessed();
            } else if ($processor->getMaxAttempts() && $message->getAttempts() < $processor->getMaxAttempts()) {
                $message->markFailed($result->getErrors());
            } else {
                $message->unlock();
                $message->setRetryAfter(
                    $this->calculateRetryAfter($message->getAttempts(), $processor->getBackoff())
                );
            }

            $message->save();
        }
    }

    private function calculateRetryAfter(int $attempts, ?array $backoff): ?Carbon
    {
        if (!isset($backoff) || count($backoff) === 0) {
            return null;
        }

        if (isset($backoff[$attempts-1])) {
            return now()->addSeconds($backoff[$attempts-1]);
        }

        return $backoff[count($backoff)-1];

    }
}