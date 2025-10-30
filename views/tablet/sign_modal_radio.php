<template x-if="show_modal && isTypeRadio">
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
                        <div class="modal__radio">
                            <span>Нет</span>
                            <label class="ui-switch ui-switch-success ui-both">
                                <input type="checkbox" x-model="radioCustomField"><i></i>
                            </label>
                            <span>Да</span>
                        </div>
                        <div class="modal__btns align-center">
                            <button class="btn" type="button" @click="saveRadio">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

