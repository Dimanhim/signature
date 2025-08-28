<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Industry $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->modelName, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="industry-view">
    <div class="card">
        <div class="card-header">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="card-body">
            <p>
                <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить?',
                        'method' => 'post',
                    ],
                ]) ?>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    [
                        'attribute' => 'clinic_id',
                        'value' => function($data) {
                            return $data->clinicName;
                        }
                    ],
                    [
                        'attribute' => 'URL',
                        'format' => 'raw',
                        'value' => function($data) {
                            return Html::a('<i class="fa fa-check-square-o"></i>', $data->link, ['target' => '_blanc', 'class' => 'url-link', 'title' => 'Перейти в планшет']);
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
    </div>
</div>
