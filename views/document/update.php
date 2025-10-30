<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\TemplateCustomParams;

/** @var yii\web\View $this */
/** @var app\models\Document $model */

$this->title = 'Редактирование: ' . $model->template->name . ' ' . $model->patient_name;
$this->params['breadcrumbs'][] = ['label' => $model->modelName, 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование документа';
?>
<div class="industry-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if($model->hasAdminCustomParams()) : ?>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <?= Html::beginForm(Url::to(['document/update', 'id' => $model->id]), 'post', ['enctype' => 'multipart/form-data']) ?>
                <?php foreach ($model->customParams as $param): ?>
                    <div class="mb-3">
                        <?php if(!TemplateCustomParams::isAdminParam($param['type'])) continue; ?>
                        <?= Html::label($param['description'], $param['id']) ?>
                        <?php if ($param['type'] === TemplateCustomParams::TYPE_ADMIN_TEXT): ?>
                            <?= Html::textInput($param['id'], ($param['value'] ?? null), ['class' => 'form-control']) ?>
                        <?php elseif ($param['type'] === TemplateCustomParams::TYPE_ADMIN_SWITCH): ?>
                            <div class="">
                                <?= Html::radio(
                                    $param['id'],
                                    isset($param['value']) && $param['value'] === 'Да',
                                    ['value' => 'Да', 'label' => 'Да', 'class' => 'form-check-input']
                                ) ?>

                                <?= Html::radio(
                                    $param['id'],
                                    isset($param['value']) && $param['value'] === 'Нет',
                                    ['value' => 'Нет', 'label' => 'Нет', 'class' => 'form-check-input']
                                ) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>






    <?php else : ?>
    <div class="card">
        <div class="card-body">
            <p>Пользовательских полей не найдено</p>
        </div>
    </div>

    <?php endif; ?>
</div>
