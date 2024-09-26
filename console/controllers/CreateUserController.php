<?php

declare(strict_types=1);

namespace console\controllers;

use backend\access\Rbac;
use backend\modules\user\models\services\CreateUserService;
use yii\console\Controller;
use yii\console\ExitCode;

final class CreateUserController extends Controller
{
    public function __construct(
        $id,
        $module,
        private CreateUserService $createAdminForm,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionAdmin(string $username, string $email, string $password)
    {
        return $this->createUser(Rbac::Admin->value, $email, $username, $password);
    }

    public function actionManager(string $username, string $email, string $password)
    {
        return $this->createUser(Rbac::Manager->value, $email, $username, $password);
    }

    private function createUser(string $role, string $email, string $username, string $password): int
    {
        if ($this->createAdminForm->create($role, $email, $username, $password) === false) {
            $errors = [];
            foreach ($this->createAdminForm->errors as $attributeErrors) {
                foreach ($attributeErrors as $error) {
                    $errors[] = $error;
                }
            }

            $errorMessages = implode("\n", $errors);
            echo "Failed to create user due to validation errors:\n$errorMessages\n";

            return ExitCode::UNAVAILABLE;
        }

        echo ucfirst($role) . " created successfully.\n";
        return ExitCode::OK;
    }
}