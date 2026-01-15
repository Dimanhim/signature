<?php

use app\models\Document;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use himiklab\sortablegrid\SortableGridView;
use yii\web\View;
use app\models\Tablet;
use app\models\Template;
use app\models\User;

/** @var yii\web\View $this */
/** @var app\models\IndustrySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="industry-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить', ['index'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= SortableGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'appointment_id',
            [
                'attribute' => 'template_id',
                'value' => function($data) {
                    if($data->template) {
                        return $data->template->name;
                    }
                },
                'filter' => Template::getList(),
            ],
            [
                'attribute' => 'tablet_id',
                'value' => function($data) {
                    if($data->tablet) {
                        return $data->tablet->name;
                    }
                },
                'filter' => Tablet::getList(),
            ],
            'document_name',
            [
                'attribute' => 'is_signature',
                'value' => function($data) {
                    return $data->is_signature ? 'Да' : 'Нет';
                },
                'filter' => [0 => 'Нет', 1 => 'Да'],
            ],
            [
                'attribute' => 'user_id',
                'value' => function($data) {
                    if($data->user) {
                        return $data->user->username;
                    }
                },
                'visible' => User::isAdmin(),
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{cancel}{delete}',
                'buttons' => [
                    'cancel' => function($link, $model) {
                        return Html::a($model->cancelSvg, ['document/cancel', 'id' => $model->id], ['class' => 'table__action', 'title' => 'Отмена документа', 'data-confirm' => $model->cancelConfirmText]);
                    }
                ],
                'urlCreator' => function ($action, Document $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'visible' => User::isAdmin(),
                'contentOptions' => ['class' => 'action-column']
            ],
        ],
    ]); ?>


</div>

