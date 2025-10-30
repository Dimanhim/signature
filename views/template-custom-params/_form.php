<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Api;

/** @var yii\web\View $this */
/** @var app\models\TemplateCustomParams $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="industry-form">
    <?php
    $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Основная информация
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'placeholder')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'type')->dropDownList(
                        $model::typeLabels(),
                        ['prompt' => '[Не выбрано]']
                    ) ?>
                    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
                    <div class="form-group">
                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    ActiveForm::end(); ?>
</div>
