<?php

use app\models\Tablet;
use app\models\Template;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Создание документа';
$this->params['breadcrumbs'][] = ['label' => $model->modelName, 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-index">
    <div class="card">
        <div class="card-header">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <?= $form->field($model, 'appointment_id')->textInput(['maxlength' => true]) ?>
                            <div class="patient_container"></div>
                            <?= $form->field($model, 'template_id')->dropDownList(Template::getList(), ['prompt' => '[Не выбрано]']) ?>
                            <?= $form->field($model, 'tablet_id')->dropDownList(Tablet::getList(), ['prompt' => '[Не выбрано]']) ?>
                            <div class="form-group">
                                <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
