<?php

declare(strict_types=1);

namespace Shared\Database;

use Illuminate\Database\DatabaseManager;

class LaravelTransactionManager implements ITransactionManager
{
    public function __construct(private readonly DatabaseManager $db) {}

    public function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    public function commit(): void
    {
        $this->db->commit();
    }

    public function rollback(): void
    {
        $this->db->rollBack();
    }
}
