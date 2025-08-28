<?php

use app\assets\SignAsset;

SignAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/tablet/manifest.webmanifest">
    <title>Подписать документ</title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div id="app" data-v-app="">
    <?= $content ?>
</div>

<?php $this->endBody() ?>
<script>
    let canvas;
    function initCanvas() {
        canvas = new fabric.Canvas('signatureCanvas', {
            isDrawingMode: true,
            width: 958,
            height: 280
        });

        canvas.freeDrawingBrush.color = '#333232'
        canvas.freeDrawingBrush.width = 5

    }
    initCanvas();
</script>
</body>
</html>
<?php $this->endPage() ?>
