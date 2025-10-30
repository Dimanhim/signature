<?php

use app\models\TemplateCustomParams;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Tablet;
use kartik\editors\Summernote;

/** @var yii\web\View $this */
/** @var app\models\Industry $model */
/** @var yii\widgets\ActiveForm $form */

$templateId = $model->id;
?>

<div class="industry-form">
    <?php $form = ActiveForm::begin(); ?>
    <ul class="nav nav-tabs" id="myTab" role="tablist">

        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-main" role="tab" aria-controls="tab-main" aria-selected="true">
                Основная информация
            </a>
        </li>

        <!-- ТАБ Настройки (форма модели) -->
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-html" role="tab" aria-controls="tab-html" aria-selected="false">
                Html
            </a>
        </li>

        <?php if (isset($custom)): ?>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-custom" role="tab"
                   aria-controls="tab-custom"
                   aria-selected="false">
                    Пользовательские поля
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <div class="tab-content" id="myTabContent">

        <!-- Основные данные -->
        <div class="tab-pane fade show active" id="tab-main" role="tabpanel" aria-labelledby="tab-main">
            <div class="card">
                <div class="card-body">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'is_active')->checkbox(['label' => Html::tag('span','Активность'), 'labelOptions' => ['class' => 'ui-checkbox']]) ?>
                    <?= $this->render('_params', [
                        'model' => $model,
                        'form' => $form,
                    ]) ?>
                </div>
            </div>
        </div>

        <!-- Основные данные -->
        <div class="tab-pane" id="tab-html" role="tabpanel" aria-labelledby="tab-html">
            <div class="card">
                <div class="card-body">
                    <div class="form-content-box">
                        <?= $form->field($model, 'content')->widget(Summernote::className(), []) ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
        if (isset($custom)): ?>
            <div class="tab-pane" id="tab-custom" role="tabpanel" aria-labelledby="tab-custom">
                <div class="card">
                    <div class="card-body">
                        <p>
                            <?= Html::a(
                                'Добавить',
                                ['/template/' . $templateId . '/custom-params/create'],
                                ['class' => 'btn btn-success']
                            ) ?>
                        </p>
                        <?= GridView::widget([
                            'dataProvider' => $custom,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],

                                'placeholder',
                                'type' => 'typeName',
                                'description',

                                [
                                    'class' => ActionColumn::className(),
                                    'template' => '{update} {delete}',
                                    'urlCreator' => function (
                                        $action,
                                        TemplateCustomParams $model,
                                        $key,
                                        $index,
                                        $column
                                    ) use ($templateId) {
                                        return Url::toRoute(
                                            [
                                                '/template/' . $templateId . '/custom-params/' . $action,
                                                'id' => $model->id
                                            ]
                                        );
                                    }
                                ],
                            ],
                        ]); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="clearfix"></div>
    <div class="col-12">
        <div class="form-group mt10">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
