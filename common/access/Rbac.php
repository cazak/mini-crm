<?php

declare(strict_types=1);

namespace common\access;

enum Rbac: string
{
    case Admin = 'admin';
    case User = 'user';
}
