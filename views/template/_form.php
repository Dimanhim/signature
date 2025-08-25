<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Tablet;
use kartik\editors\Summernote;

/** @var yii\web\View $this */
/** @var app\models\Industry $model */
/** @var yii\widgets\ActiveForm $form */
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
    </div>
    <div class="clearfix"></div>
    <div class="col-12">
        <div class="form-group mt10">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
