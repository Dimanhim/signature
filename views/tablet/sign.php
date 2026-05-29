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

    <?= $this->render('sign_loader'); ?>

    <?= $this->render('sign_qr', ['model' => $model]); ?>
    <?= $this->render('sign_qr_messages', ['model' => $model]); ?>
</div>

</div>

<?php if($tablet_css) : ?>
    <style>
        <?= $tablet_css ?>
    </style>
<?php endif; ?>

<?= $this->render('_alpine', [
    'model' => $model,
    'update_on_demand' => $update_on_demand
]); ?>

<style>
    /* Кнопка "Отменить оплату" (Темный нейтральный цвет) */
    #sign-container .btn-payment-main {
        background: #333232 !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: none !important;
    }

    /* Кнопка "Отмена" (Бледная под базовый стиль) */
    #sign-container .btn-payment-ghost {
        background: #ffffff !important;
        color: #333232 !important;
        border: 2px solid #ccc !important;
        box-shadow: none !important;
    }

    /* Ряд кнопок на экране QR */
    #sign-container .qr-buttons-row {
        display: flex;
        gap: 20px;
        width: 100%;
        max-width: 650px;
        margin-top: 60px;
        justify-content: center;
    }

    #sign-container .qr-buttons-row .btn {
        flex: 1;
        height: 80px;
        font-size: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-weight: bold;
    }

    #sign-container .qr-status-info {
        margin-top: 25px;
        font-size: 20px;
        font-weight: 300;
        color: #333232 !important;
    }

    #sign-container .qr-status-info span {
        font-weight: 600;
    }

    #sign-container .qr-header-title {
        margin: 0;
        font-size: 54px;
        line-height: 1.2;
        color: #333232 !important;
    }

    /* Ограничиваем контейнер, в который QRCreator вставляет холст */
    #qr-container {
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 100%;
        max-width: 350px;
        margin: 0 auto;
        aspect-ratio: 1 / 1;
    }

    #qr-container canvas,
    #qr-container img {
        width: 100% !important;
        height: auto !important;
        max-width: 100% !important;
        display: block;
    }

    /* Белый блок-подложка под QR код */
    .qr-code-block {
        background: #fff;
        padding: 30px;
        border-radius: 24px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 420px;
        margin: 0 auto;
        box-sizing: border-box;
    }

</style>
