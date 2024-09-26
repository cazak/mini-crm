<?php

declare(strict_types=1);

namespace common\bootstrap;

use backend\services\TransactionManager;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\rbac\ManagerInterface;

final readonly class SetUp implements BootstrapInterface
{
    /**
     * @param Application $app
     */
    public function bootstrap($app): void
    {
        $container = \Yii::$container;

        $container->setSingleton(ManagerInterface::class, function () use ($app) {
            return $app->authManager;
        });

        $container->setSingleton(TransactionManager::class , function () use ($app) {
            return new TransactionManager($app->db);
        });
    }
}