<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Api;
use app\models\Tablet;
use app\models\User;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Основная информация
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'password')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'role')->dropDownList(
                        User::getRoleList()
                    ) ?>
                    <?= $form->field($model, 'clinic_id')->dropDownList(
                        Api::getClinicsList(),
                        ['prompt' => '[Не выбрано]']
                    ) ?>
                    <?= $form->field($model, 'default_tablet_id')->dropDownList(
                        Tablet::getList(),
                        ['prompt' => '[Не выбрано]']
                    ) ?>
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
