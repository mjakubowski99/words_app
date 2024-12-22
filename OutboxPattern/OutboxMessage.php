<?php

namespace OutboxPattern;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OutboxMessage extends Model implements IOutboxMessage
{
    protected $table = 'outbox_messages';

    protected $guarded = [];

    protected $casts = [
        'processed_at' => 'datetime',
        'retry_after' => 'datetime',
        'locked_until' => 'datetime',
        'payload' => 'array',
    ];

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function setRetryAfter(?Carbon $at): void
    {
        $this->retry_after = $at;
    }

    public function getType(): OutboxMessageType
    {
        return OutboxMessageType::from($this->type);
    }

    public function incrementAttempts(): void
    {
        $this->attempts++;
        $this->save();
    }

    public function markProcessed(): void
    {
        $this->status = OutboxMessageStatus::SUCCESS->value;
        $this->unlock();
    }

    public function unlock(): void
    {
        $this->locked_until = null;
    }

    public function markFailed(?array $errors): void
    {
        $this->status = OutboxMessageStatus::FAILED->value;
        $this->errors = $errors;
        $this->unlock();
    }

    public function scopeTimeToProcess(Builder $builder): Builder
    {
        return $builder->where(function ($query) {
            return $query->whereNull('retry_after')
                ->orWhere('retry_after', '>', now());
        });
    }

    public function scopePending(Builder $builder): Builder
    {
        return $builder->where('status', '=', OutboxMessageStatus::PENDING->value);
    }

    public function scopeNotLocked(Builder $builder): Builder
    {
        return $builder->where(function ($query){
            return $query->whereNull('locked_until')
                ->orWhere('locked_until', '<', now());
        });
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): OutboxMessageStatus
    {
        return OutboxMessageStatus::from($this->status);
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }
}