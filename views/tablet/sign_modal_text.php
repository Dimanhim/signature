<template x-if="show_modal && isTypeText">
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
                        <div class="modal__text">
                            <input type="text" placeholder="Поле ввода" x-model="textCustomField">
                        </div>
                        <div class="modal__btns">
                            <button class="btn" type="button" @click="saveText">Сохранить</button>
                            <button class="btn btn--dark" type="button" @click="clearText">Очистить</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

