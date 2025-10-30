<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TemplateCustomParams $model */

$this->title = 'Редактирование: ' . $model->placeholder;
$this->params['breadcrumbs'][] = [
    'label' => $model->modelName,
    'url' => ['template/update?id=' . $model->template_id]
];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="industry-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>