<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->params['breadcrumbs'][] = ['label' => $model->modelName, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить пользователя?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'password',
            'email:email',
            [
                'attribute' => 'Роль',
                'value' => function($data) {
                    return $data->roleName;
                }
            ],

            [
                'attribute' => 'is_active',
                'value' => function($data) {
                    return $data->active;
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function($data) {
                    return $data->createdAt;
                }
            ],
            [
                'attribute' => 'updated_at',
                'value' => function($data) {
                    return $data->updatedAt;
                }
            ],
        ],
    ]) ?>

</div>
