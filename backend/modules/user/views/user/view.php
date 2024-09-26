<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\modules\user\models\User $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (Yii::$app->user->can(\backend\access\Rbac::Admin->value)): ?>

        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>

        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'email:email',
            [
                'attribute' => 'status',
                'value' => function (\backend\modules\user\models\User $model) {
                    return $model->getStatusName();
                }
            ],
            [
                'attribute' => 'role',
                'value' => function (\backend\modules\user\models\User $model) {
                    $roles = \Yii::$app->authManager->getRolesByUser($model->id);
                    return implode(', ', array_map(function($role) {
                        return $role->name;
                    }, $roles));
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',
            'verification_token',
        ],
    ]) ?>

</div>
