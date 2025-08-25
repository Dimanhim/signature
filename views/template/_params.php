<?php

use app\models\Template;
use yii\helpers\Html;

$params = Template::getParamsArray();

?>
<div class="form-group">
    <table class="table">
        <tr>
            <th>Параметр</th>
            <th>Обязательный</th>
        </tr>
        <?php if($params) : ?>
            <?php foreach($params as $paramName => $paramItems) : ?>
                <tr>
                    <th colspan="3" style="text-align: center;"><?= $paramName ?></th>
                </tr>
                <?php if($paramItems) : ?>
                    <?php foreach($paramItems as $paramItemId => $paramItemName) : ?>
                        <tr>
                            <td><?= $paramItemName ?></td>
                            <td><?= $form->field($model, "params[{$paramItemId}][required]")->checkbox(['label' => Html::tag('span',''), 'labelOptions' => ['class' => 'ui-checkbox']]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>
