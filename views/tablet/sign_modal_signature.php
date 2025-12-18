<template x-if="show_modal && isTypeSignature">
    <div class="vfm vfm--fixed vfm--inset" style="z-index: 1000;" role="dialog" aria-modal="true">
        <div class="vfm__overlay vfm--overlay vfm--absolute vfm--inset vfm--prevent-none" aria-hidden="true"></div>
        <div class="vfm__content vfm--outline-none" tabindex="0">
            <div class="modal modal-sign" id="modal-sign">
                <div class="modal__content">
                    <button type="button" class="modal__close" aria-label="Закрыть модальное окно" @click="hideSignatureModal">
                        <span></span>
                        <span></span>
                    </button>
                    <div class="modal__wrap">
                        <div class="modal__sign" @touchstart="hideSignText" @mousedown="hideSignText">
                            <canvas id="signatureCanvas" class="modal__sign-canvas"></canvas>
                            <template x-if="show_sign_text">
                                <p class="modal__sign-text">Место для подписи</p>
                            </template>
                        </div>
                        <div class="modal__btns">
                            <button class="btn btn--dark" type="button" @click="clearSignature">Очистить</button>
                            <button class="btn" type="button" @click="saveSignature">Подписать</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

