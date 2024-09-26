<?php

namespace backend\modules\user\models\forms;

use backend\access\Rbac;
use backend\modules\user\models\User;
use backend\services\RoleManager;
use backend\services\TransactionManager;
use yii\base\Model;

final class UserForm extends Model
{
    public string $role;
    public string $username;
    public string $email;
    public int $status;

    public User $user;

    public function __construct(
        private TransactionManager $transactionManager,
        private RoleManager $roleManager,
        $config = []
    ) {
        parent::__construct($config);
    }

    public function setUserData(User $user): void
    {
        $this->user = $user;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->status = $user->status;
        $this->role = array_keys(\Yii::$app->authManager->getRolesByUser($user->id))[0];
    }

    public function rules()
    {
        return [
            ['role', 'required'],
            ['role', 'in', 'range' => array_keys(Rbac::getRoles())],

            ['status', 'default', 'value' => User::STATUS_INACTIVE],
            ['status', 'in', 'range' => [User::STATUS_ACTIVE, User::STATUS_INACTIVE]],

            ['username', 'trim'],
            ['username', 'required'],
            [
                'username',
                'unique',
                'targetClass' => '\backend\modules\user\models\User',
                'message' => 'This username has already been taken.',
                'filter' => function ($query) {
                    $query->andWhere(['!=', 'id', $this->user->id]);
                },
            ],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            [
                'email',
                'unique',
                'targetClass' => '\backend\modules\user\models\User',
                'message' => 'This email address has already been taken.',
                'filter' => function ($query) {
                    $query->andWhere(['!=', 'id', $this->user->id]);
                },
            ],
        ];
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->user;

        $user->setAttributes([
            'username' => $this->username,
            'email' => $this->email,
            'status' => $this->status,
        ], false);

        $this->transactionManager->wrap(function () use ($user) {
            $user->save();
            $this->roleManager->assign($user->id, $this->role);
        });

        return true;
    }
}
