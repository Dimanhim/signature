<?php

use himiklab\thumbnail\EasyThumbnailImage;
use yii\helpers\Html;

?>

<?php if ($model && ($images = $model->images)): ?>
    <div class="container-fluid">
        <div class="image-preview-container">
            <div class="row image-preview-container-o">
                <?php foreach($images as $image) : ?>
                    <div class="col-4 image-preview image-preview-o" data-id="<?= $image->id ?>">
                        <div class="image-preview-content <?= $image->isVideo ? 'video-preview' : '' ?>">
                            <a href="<?= $image->fileUrl ?>" <?= $image->galleryAttributes() ?>>
                                <?= $image->img ?>
                            </a>

                            <p>
                                <?= Html::a('Удалить', ['images/delete', 'id' => $image->id], ['class' => 'btn btn-sm btn-danger delete-image delete-image-o', 'data-confirm' => 'Удалить файл?']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif ?>
