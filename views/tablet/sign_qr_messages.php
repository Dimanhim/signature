<template x-if="isTemplate('qr_messages')">
    <div class="wrapper">
        <section class="wallpaper" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; text-align: center;">

            <div class="qr-header" style="margin-bottom: 40px; padding: 0 20px;">
                <p class="wallpaper__update-text" style="margin: 0; font-size: 48px; line-height: 1.2; color: #ff4d4d;" x-html="qr_message">
                </p>
            </div>

            <div class="doc__btn" style="margin-top: 40px; width: 100%; max-width: 450px;">
                <button class="btn" type="button" @click="setTemplate('document')" style="width: 100%; height: 80px; font-size: 24px;">
                    Вернуться к документу
                </button>
            </div>

        </section>
    </div>
</template>

