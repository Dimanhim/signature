<?php

use app\models\Tablet;
use app\models\Template;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Создание документа';
$this->params['breadcrumbs'][] = ['label' => $model->modelName, 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
$signature = Yii::$app->settings->signature;
?>
<div class="document-index">
    <div class="card">
        <div class="card-header">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body">
                            <?= $form->field($model, 'appointment_id')->textInput(['maxlength' => true]) ?>
                            <div class="patient_container"></div>
                            <?= $form->field($model, 'template_id')->dropDownList(
                                Template::getListForCurrentUser(),
                                ['prompt' => '[Не выбрано]']
                            ) ?>
                            <?= $form->field($model, 'tablet_id')->dropDownList(
                                Tablet::getListForCurrentUser(),
                                [
                                    'value' => Tablet::getDefaultForCurrentUser(),
                                    'prompt' => '[Не выбрано]'
                                ]
                            ) ?>
                            <div class="form-group">
                                <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            Ваш текущий образец подписи
                        </div>
                        <div class="card-body">
                            <?php if($signature) : ?>
                                <div>
                                    <?= Html::img($signature, ['style' => 'width: 200px;']) ?>
                                </div>
                                <div>
                                    <?= Html::a('Редактировать', ['tablet/signature'], ['class' => 'btn btn-primary']) ?>
                                </div>
                            <?php else : ?>
                                <div>
                                    <?= Html::a('Создать', ['tablet/signature'], ['class' => 'btn btn-primary']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
