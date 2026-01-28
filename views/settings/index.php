<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
//use yii\bootstrap5\ActiveForm;
//use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\helpers\Url;

$this->title = 'Настройки';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="settings-index">
    <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <?= Html::encode($this->title) ?>
                    </div>
                    <div class="card-body">
                        <?= $form->field($model, 'app_name')->textInput()->label('Название приложения') ?>
                        <?= $form->field($model, 'rnova_api_url')->textInput()->label('Адрес API МИС') ?>
                        <?= $form->field($model, 'rnova_api_key')->textInput()->label('Ключ API МИС') ?>
                        <!-- <?//= $form->field($model, 'tablet_url')->textInput()->label('Базовый URL планшета') ?>-->
                        <?= $form->field($model, 'document_css')->textarea()->label('CSS-стили документов') ?>
                        <?= $form->field($model, 'tablet_css')->textarea()->label('CSS-стили планшета') ?>
                        <?= $form->field($model, 'cancel_unsigned')->checkbox(['label' => Html::tag('span','Отменять неподписанные документы'), 'labelOptions' => ['class' => 'ui-checkbox']]) ?>
                        <?= $form->field($model, 'update_on_demand')->checkbox(['label' => Html::tag('span','Обновлять планшеты вручную'), 'labelOptions' => ['class' => 'ui-checkbox']]) ?>
                        <div class="form-group">
                            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>
