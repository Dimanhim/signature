<?php
use app\components\Helpers;
use kartik\widgets\FileInput;
?>
<div class="card">
    <div class="card-header">
        Изображения
    </div>
    <div class="card-body">
        <?php if (!$model->isNewRecord && $model->gallery) echo $model->gallery->getPreviewListHTML() ?>
        <?= $form->field($model, 'image_fields[]')->widget(FileInput::classname(), Helpers::getFileInputOptions()) ?>
    </div>
</div>
