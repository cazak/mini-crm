<?php

namespace backend\modules\user\models\services;

use backend\access\Rbac;
use backend\modules\user\models\User;
use backend\services\RoleManager;
use backend\services\TransactionManager;
use Yii;
use yii\base\Model;

final class CreateUserService extends Model
{
    public string $role;
    public string $username;
    public string $email;
    public string $password;

    public function __construct(
        private TransactionManager $transactionManager,
        private RoleManager $roleManager,
        $config = [],
    ) {
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['role', 'required'],
            ['role', 'in', 'range' => array_keys(Rbac::getRoles())],

            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\backend\modules\user\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\backend\modules\user\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
        ];
    }

    public function create(string $role, string $email, string $username, string $password): bool
    {
        $this->role = $role;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;

        if (!$this->validate()) {
            return false;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->status = User::STATUS_ACTIVE;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        $this->transactionManager->wrap(function () use ($user) {
            $user->save();
            $this->roleManager->assign($user->id, $this->role);
        });

        return true;
    }
}
