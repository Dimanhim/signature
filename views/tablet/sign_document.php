<template x-if="isTemplate('document')">
    <div class="wrapper" >
        <header class="header">
            <div class="container">
                <div class="header__wrap">
                    <div class="header__logo"><img :src="logoPath" alt=""></div>
                    <p> Планшет №<b x-html="tabletId"></b></p></div>
            </div>
        </header>
        <main>
            <section class="doc">
                <div class="doc__head"><p> Ф.И.О. пациента: <b x-html="patient_name"></b></p>
                    <p> Дата рождения: <b x-html="patient_birthday"></b></p></div>
                <div id="content-inner" class="doc__content">
                    <div class="doc__content-inner" x-html="processedLayout"></div>
                </div>

                <div class="doc__btn">
                    <template x-if="isAllSigned">
                        <button class="btn" type="button" @click="sendDocument">
                            Отправить
                        </button>
                    </template>
                    <template x-if="isPartedSigned">
                        <div>
                            <button class="btn" type="button" disabled="disabled" x-html="signaturesText"></button>
                            <button class="btn btn__cancel" type="button" @click="cancelDocument">Отмена</button>
                        </div>

                    </template>

                </div>
            </section>
        </main>
    </div>
</template>

<style>
    .doc__content .btn.btn--sm {
        padding: 5px 24px 5px 14px;
        font-size: 16px;
    }
    .doc__content .btn.btn--sm::before {
        width: 23px;
        height: 24px;
    }
    .doc__content .btn.btn--sm {
        min-height: 62px;
        margin: 5px 0;
    }
</style>
