<?php

use app\models\Api;
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

// В начале файла формы или в AppAsset:
//$this->registerCssFile('/summernote/css/summernote-bs4.css');
//$this->registerCssFile('/summernote/css/codemirror.css');
//
//$this->registerJsFile('/summernote/js/summernote-bs4.js', ['depends' => [\yii\web\JqueryAsset::class]]);
//$this->registerJsFile('/summernote/js/summernote-ru-RU.js', ['depends' => [\yii\web\JqueryAsset::class]]);
//$this->registerJsFile('/summernote/js/codemirror.js', ['depends' => [\yii\web\JqueryAsset::class]]);
//$this->registerJsFile('/summernote/js/codemirror_xml.js', ['depends' => [\yii\web\JqueryAsset::class]]);
//$this->registerJsFile('/summernote/js/codemirror_formatting.js', ['depends' => [\yii\web\JqueryAsset::class]]);

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
                    <?= $form->field($model, 'clinic_ids')->dropDownList(
                        Api::getClinicsList(),
                        [
                            'multiple' => true,
                            'size' => 7,
                        ]
                    ) ?>
                    <?= $form->field($model, 'payment_option', [
                            'options' => ['class' => 'form-group template-payment-container']
                    ])->checkbox([
                            'label' => Html::tag('span', 'Требовать оплату перед подписанием ' . Html::tag('i', '', [
                                            'class' => 'bi bi-question-circle-fill text-primary template-payment-tooltip',
                                            'style' => 'cursor: pointer; font-size: 14px; margin-left: 5px; display: inline-block; vertical-align: middle; margin-top: -2px;',
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'top',
                                            'title' => "Активирует проверку счетов в МИС конкретно для этого шаблона документа.\nЕсли у пациента есть неоплаченный счет по визиту, планшет заблокирует подписание и покажет QR-код."
                                    ])),
                            'labelOptions' => ['class' => 'ui-checkbox']
                    ]) ?>
                    <div class="form-group mt10">
                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                    </div>
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
                        <?= $form->field($model, 'content')->widget(Summernote::className(), [
                            'pluginOptions' => [
                                    'lang' => 'ru-RU',
                            ],
                        ]) ?>
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
<style>
    /* Корректируем положение серого куба чекбокса в шаблонах */
    .template-payment-container .ui-checkbox input[type="checkbox"] + span::before {
        top: -2px !important;
    }
    /* Точный фикс положения внутренней галочки (как в настройках) */
    .template-payment-container .ui-checkbox input[type="checkbox"]:checked + span::after {
        top: 5px !important;
    }
</style>

<?php
// Инициализируем плагин тултипа для этой иконки
$js = <<<JS
    $('.template-payment-tooltip').tooltip();
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>
