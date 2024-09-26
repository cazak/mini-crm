<?php

declare(strict_types=1);

namespace backend\services;

use DomainException;
use yii\rbac\ManagerInterface;

final readonly class RoleManager
{
    public function __construct(private ManagerInterface $manager)
    {
    }

    public function assign(int $userId, string $name): void
    {
        if (!$role = $this->manager->getRole($name)) {
            throw new DomainException('Role "' . $name . '" does not exist.');
        }

        $this->manager->revokeAll($userId);
        $this->manager->assign($role, $userId);
    }
}
