<?php

declare(strict_types=1);

namespace backend\access;

enum Rbac: string
{
    case Admin = 'admin';
    case Manager = 'manager';

    public static function getRoles(): array
    {
        return [
            self::Admin->value => self::Admin->name,
            self::Manager->value => self::Manager->name,
        ];
    }
}
