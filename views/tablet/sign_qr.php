<template x-if="isTemplate('qr')">
    <div class="wrapper">
        <section class="wallpaper" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; text-align: center;">
            <div class="qr-header" style="margin-bottom: 40px;">
                <p class="wallpaper__update-text" style="margin: 0; font-size: 54px; line-height: 1.2;">
                    Оплата по QR-коду
                </p>
            </div>

            <div class="qr-code-block" style="background: #fff; padding: 40px; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.1);">
                <div id="qr-container" style="display: flex; align-items: center; justify-content: center; min-width: 350px; min-height: 350px;">
                </div>
            </div>

            <div class="qr-status-info">
                <p>Ожидание оплаты: <span x-text="qr_seconds" style="font-weight: 600; color: #333;"></span> сек.</p>
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
