<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Api;

/** @var yii\web\View $this */
/** @var app\models\Industry $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="industry-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'clinic_id')->dropDownList(Api::getClinicsList(), ['prompt' => '[Не выбрано]']) ?>
                    <?= $form->field($model, 'is_active')->checkbox(['label' => Html::tag('span','Активность'), 'labelOptions' => ['class' => 'ui-checkbox']]) ?>
                    <div class="form-group">
                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
