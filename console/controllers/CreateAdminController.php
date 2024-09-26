<?php

declare(strict_types=1);

namespace console\controllers;

use common\models\User;
use common\services\RoleManager;
use common\services\TransactionManager;
use common\access\Rbac;
use frontend\models\SignupForm;
use yii\console\Controller;
use yii\console\ExitCode;

final class CreateAdminController extends Controller
{
    public function __construct(
        $id,
        $module,
        private TransactionManager $transactionManager,
        private RoleManager $roleManager,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(string $username, string $email, string $password)
    {
        $signupForm = new SignupForm();
        $signupForm->email = $email;
        $signupForm->username = $username;
        $signupForm->password = $password;

        if (!$signupForm->validate()) {
            $errors = [];
            foreach ($signupForm->errors as $attributeErrors) {
                foreach ($attributeErrors as $error) {
                    $errors[] = $error;
                }
            }
            $errorMessages = implode("\n", $errors);

            echo "Failed to create admin user due to validation errors:\n$errorMessages\n";

            return ExitCode::UNAVAILABLE;
        }

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();

        $this->transactionManager->wrap(function () use ($user) {
            $user->save();
            $this->roleManager->assign($user->id, Rbac::Admin->value);
        });

        echo "Admin user created successfully.\n";
    }
}