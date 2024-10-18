<?php

declare(strict_types=1);

namespace Shared\Database;

interface ITransactionManager
{
    public function beginTransaction(): void;

    public function commit(): void;

    public function rollback(): void;
}
