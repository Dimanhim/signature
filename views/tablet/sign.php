<?php

use app\models\Setting;
use yii\helpers\Url;

$this->registerJsFile('/sign/js/fabric.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('/js/QRCreator.js', ['position' => \yii\web\View::POS_HEAD]);
$update_on_demand = Setting::getValueByName('update_on_demand');
$tablet_css = Setting::getValueByName('tablet_css');
?>

<div id="sign-container" x-data="sign" x-init="initService" data-tablet="<?= $model->id ?>">

    <?= $this->render('sign_download', [
        'model' => $model
    ]); ?>

    <?= $this->render('sign_document', [
        'model' => $model
    ]); ?>

    <?= $this->render('sign_modal_signature', [
        'model' => $model
    ]); ?>

    <?= $this->render('sign_modal_radio', [
        'model' => $model
    ]); ?>

    <?= $this->render('sign_modal_text', [
        'model' => $model
    ]); ?>

    <?= $this->render('sign_loader', [
        'model' => $model
    ]); ?>

    <?= $this->render('sign_qr', [
            'model' => $model
    ]); ?>

    <?= $this->render('sign_qr_messages', ['model' => $model]); ?>

    <?= $this->render('sign_styles', [
        'model' => $model,
    ]) ?>

</div>

<?php if($tablet_css) : ?>
    <style>
        <?= $tablet_css ?>
    </style>
<?php endif; ?>

<?= $this->render('alpine', [
    'model' => $model,
    'update_on_demand' => $update_on_demand,
]) ?>
