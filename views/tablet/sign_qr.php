<?php
/** @var \app\models\Document $model */
?>
<template x-if="isTemplate('qr')">
    <div class="wrapper">
        <section class="wallpaper" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; text-align: center;">
            <div class="qr-header" style="margin-bottom: 40px;">
                <p class="qr-header-title">
                    Оплата по QR-коду
                </p>
            </div>

            <div class="qr-code-block">
                <div id="qr-container"></div>
            </div>

            <div class="qr-status-info">
                <p>Ожидание оплаты: <span x-text="qr_seconds"></span> сек.</p>
            </div>

            <div class="qr-buttons-row">
                <button class="btn btn-payment-main" type="button" @click="cancelPayment">
                    Отменить оплату
                </button>

                <button class="btn btn-payment-ghost" type="button" @click="cancelDocument">
                    Отмена
                </button>
            </div>
        </section>
    </div>
</template>
