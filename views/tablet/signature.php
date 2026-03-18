<?php
/** @var app\models\UserSignature $model */
use yii\helpers\Url;

$this->title = 'Настройка образца подписи';
$this->registerJsFile('/sign/js/fabric.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div id="sign-container" x-data="userSignature" x-init="initCanvas">
    <div class="wrapper">
        <header class="header">
            <div class="container">
                <div class="header__wrap">
                    <div class="header__logo"><img src="/sign/img/logo.svg" alt=""></div>
                    <p>Настройка личного клише подписи</p>
                </div>
            </div>
        </header>

        <main>
            <section class="doc">
                <div class="doc__content" style="display: flex; flex-direction: column; align-items: center; padding: 60px 0;">
                    <p class="mb-4" style="font-size: 18px; color: #666;">Распишитесь в поле ниже:</p>

                    <div class="modal__sign"
                         style="border: 2px dashed #45ac55; background: #fff; width: 600px; height: 250px; position: relative;"
                         @touchstart="hideSignText"
                         @mousedown="hideSignText">

                        <canvas id="userSignatureCanvas" width="600" height="250"></canvas>

                        <template x-if="show_sign_text">
                            <p class="modal__sign-text">Место для подписи</p>
                        </template>
                    </div>

                    <div class="modal__btns" style="margin-top: 30px; display: flex; gap: 20px; justify-content: center;">
                        <button class="btn btn--white btn--fix-width" type="button" @click="clearSignature">Очистить</button>
                        <button class="btn btn--fix-width" type="button" @click="saveSignature">Сохранить</button>
                    </div>

                </div>

                <?php if ($model->signature_data): ?>
                    <div class="doc__head" style="margin-top: 20px; background: #f9f9f9; text-align: center; border-top: 1px solid #eee;">
                        <p style="margin-bottom: 10px;">Ваша текущая подпись в системе:</p>
                        <div style="background: #fff; padding: 15px; border: 1px solid #eee; display: inline-block;">
                            <img src="<?= $model->signature_data ?>" style="max-height: 80px;">
                        </div>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <template x-if="loader">
        <div class="vld-container">
            <div class="vl-overlay vl-active vl-full-page" style="z-index: 999999;">
                <div class="vl-background" style="background: rgb(255, 255, 255); opacity: 0.93;"></div>
                <div class="vl-icon">
                    <svg viewBox="0 0 38 38" width="64" height="64" stroke="#45ac55">
                        <g fill="none" fill-rule="evenodd">
                            <g transform="translate(1 1)" stroke-width="2">
                                <circle stroke-opacity=".25" cx="18" cy="18" r="18"></circle>
                                <path d="M36 18c0-9.94-8.06-18-18-18">
                                    <animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur="0.8s" repeatCount="indefinite"></animateTransform>
                                </path>
                            </g>
                        </g>
                    </svg>
                </div>
            </div>
        </div>
    </template>
</div>

<style>
    #sign-container section.doc {
        height: auto !important;
        min-height: 100vh;
        display: block !important;
        position: relative !important;
        overflow: visible !important;
    }

    #sign-container .doc__content {
        position: relative !important;
        display: flex !important;
        flex-direction: column !important;
        height: auto !important;
        padding-bottom: 50px !important;
    }

    #sign-container .doc__head {
        position: relative !important;
        top: 0 !important;
        left: 0 !important;
        margin-top: 40px !important;
        border-top: 1px solid #ddd !important;
        padding: 30px !important;
        background: #f9f9f9 !important;
        width: 100% !important;
        height: auto !important;
    }

    .doc__head img {
        max-width: 250px !important;
        height: auto !important;
        border: 1px solid #ccc;
        background: #fff;
        padding: 10px;
    }

    .notie-container {
        z-index: 1000000 !important;
    }

    .notie-textbox-inner,
    .notie-content,
    .notie-text {
        color: #ffffff !important;
        font-weight: 600 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    .notie-background-success {
        background-color: #45ac55 !important;
    }

</style>







<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('userSignature', () => ({
            canvas: null,
            loader: false,
            show_sign_text: true,

            initCanvas() {
                this.canvas = new fabric.Canvas('userSignatureCanvas', {
                    isDrawingMode: true
                });
                this.canvas.freeDrawingBrush.width = 4;
                this.canvas.freeDrawingBrush.color = '#000080';
            },

            hideSignText() {
                this.show_sign_text = false;
            },

            clearSignature() {
                this.canvas.clear();
                this.show_sign_text = true;
            },

            async saveSignature() {
                if (this.canvas.getObjects().length === 0) {
                    notie.alert({ type: 'error', text: 'Пожалуйста, распишитесь' });
                    return;
                }

                this.loader = true;
                const dataUrl = this.canvas.toDataURL({ format: 'png', quality: 1 });

                try {
                    const response = await fetch('<?= Url::to(['api/save-signature']) ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            'signature': dataUrl,
                            '<?= Yii::$app->request->csrfParam ?>': '<?= Yii::$app->request->csrfToken ?>'
                        })
                    });

                    const result = await response.json();
                    if (result.error === 0) {
                        notie.alert({ type: 'success', text: result.message });
                        setTimeout(() => window.location = '/', 2000)
                    } else {
                        notie.alert({ type: 'error', text: result.message });
                        this.loader = false;
                    }
                } catch (e) {
                    notie.alert({ type: 'error', text: 'Ошибка соединения' });
                    this.loader = false;
                }
            }
        }));
    });
</script>
