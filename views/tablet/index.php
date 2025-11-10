<?php

use app\models\Tablet;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use himiklab\sortablegrid\SortableGridView;
use app\models\User;

/** @var yii\web\View $this */
/** @var app\models\IndustrySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="industry-index">
    <div class="card">
        <div class="card-header">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="card-body">
            <p>
                <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?php
                $columns = [
                    ['class' => 'yii\grid\SerialColumn'],

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
                    ]
                ];
                if(User::isAdmin()) {
                    $columns[] = [
                        'class' => ActionColumn::className(),
                        'contentOptions' => [
                            'class' => 'td-actions'
                        ],
                        'urlCreator' => function ($action, Tablet $model, $key, $index, $column) {
                            return Url::toRoute([$action, 'id' => $model->id]);
                        }
                    ];
                }
            ?>

            <?= SortableGridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => $columns
            ]); ?>
        </div>
    </div>
</div>
