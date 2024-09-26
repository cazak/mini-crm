<?php

declare(strict_types=1);

namespace backend\services;

use Exception;
use yii\db\Connection;

final readonly class TransactionManager
{
    public function __construct(private Connection $connection)
    {
    }

    public function wrap(callable $function): void
    {
        $transaction = $this->connection->beginTransaction();

        try {
            $function();
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();

            throw $e;
        }
    }
}
