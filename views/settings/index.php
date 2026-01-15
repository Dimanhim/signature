<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Настройки';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="settings-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'app_name')->textInput()->label('Название приложения') ?>
    <?= $form->field($model, 'rnova_api_url')->textInput()->label('Адрес API МИС') ?>
    <?= $form->field($model, 'rnova_api_key')->textInput()->label('Ключ API МИС') ?>
    <?= $form->field($model, 'tablet_url')->textInput()->label('Базовый URL планшета') ?>
    <?= $form->field($model, 'document_css')->textarea()->label('CSS-стили документов') ?>
    <?= $form->field($model, 'cancel_unsigned')->checkbox(['label' => 'Отменять неподписанные документы']) ?>
    <?= $form->field($model, 'update_on_demand')->checkbox(['label' => 'Обновлять планшеты вручную']) ?>
    
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>