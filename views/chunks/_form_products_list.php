<?php

use app\models\Product;
use kartik\widgets\Select2;
use yii\helpers\Html;

?>

<div class="form-group field-company-product_ids">
    <label class="control-label" for="company-product_ids"><?= $model->attributeLabels()['product_ids'] ?></label>
    <?= Html::activeDropDownList($model, 'product_ids', $model->productList(), ['prompt' => '[не выбраны]', 'multiple' => true, 'class' => 'form-control select2']) ?>
    <div class="help-block"></div>
</div>


