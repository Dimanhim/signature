<?php

use app\models\Setting;
use yii\helpers\Url;

$this->registerJsFile('/sign/js/fabric.min.js', ['position' => \yii\web\View::POS_HEAD]);
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

    <?= $this->render('sign_modal', [
        'model' => $model
    ]); ?>

    <?= $this->render('sign_loader'); ?>

</div>

<?php if($tablet_css) : ?>
    <style>
        <?= $tablet_css ?>
    </style>
<?php endif; ?>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('sign', () => ({
            apiUrl: '<?= Url::home(true) ?>api/',
            update_on_demand: <?= $update_on_demand ?>,
            tabletId: <?= $model->id ?>,
            loader: false,
            document_id: null,
            patient_id: null,
            patient_birthday: null,
            patient_name: null,
            content: null,
            processedLayout: null,
            templates: [
                'download', 'document'
            ],
            template: 'download',
            signatures: {},
            currentSignatureId: null,
            total_signatures: 0,
            show_modal: false,
            show_sign_text: true,

            initService() {
                if(!this.update_on_demand) {
                    this.loadDocument();
                }
            },

            isTemplate(templateName) {
                return templateName == this.template
            },
            setTemplate(templateName) {
                if(this.templates.includes(templateName)) this.template = templateName;
            },

            loaderOn() {
                this.loader = true
            },
            loaderOff() {
                this.loader = false
            },

            signaturesText() {
                return 'Подписей:&nbsp;' + this.signedSignatures() + '&nbsp;из&nbsp;' + this.total_signatures;
            },
            signedSignatures() {
                return Object.values(this.signatures).filter((item) => item).length
            },
            isAllSigned() {
                return (this.total_signatures - this.signedSignatures()) == 0;
            },
            isPartedSigned() {
                return !this.isAllSigned();
            },

            showSignText() {
                this.show_sign_text = true
            },
            hideSignText() {
                this.show_sign_text = false
            },

            async loadData(method, params) {
                this.loaderOn();
                const response = await fetch(this.apiUrl + method, {
                    method: 'POST',
                    body: params
                });
                let data = await response.json();
                if(data.error == 0) return data.data;
            },
            async loadDataJson(method, params) {
                this.loaderOn();
                const response = await fetch(this.apiUrl + method, {
                    method: 'POST',
                    'Content-Type': 'application/json',
                    body: JSON.stringify(params)
                });
                return await response.json();
            },

            loadDocument() {
                this.loaderOn();
                const params = new URLSearchParams();
                params.set('tablet_id', this.tabletId);

                const response = this.loadData('get-documents', params)

                response.then((data) => {
                    if(data.length) {
                        let result = data[0];

                        this.document_id = result.document_id;
                        this.patient_id = result.patient_id;
                        this.patient_name = result.patient_name;
                        this.patient_birthday = result.patient_birthday;
                        this.content = result.content;
                        this.watchEffect();
                        this.setSimplebar();
                        if(this.content.length) {
                            this.setTemplate('document')
                        }
                        this.loaderOff();
                    }
                    else {
                        notie.alert({
                            type: 'success',
                            text: 'Документов для планшета не найдено',
                        });
                        this.loaderOff();
                    }
                });
            },
            sendDocument() {
                const data = {
                    document_id: this.document_id,
                    signatures: { ...this.signatures }
                }

                this.loaderOn();

                const response = this.loadDataJson('set-signatures', data)

                response.then((data) => {
                    let message;

                    if(data.error == 0) {
                        message = data.message || 'Документ успешно отправлен'
                        notie.alert({
                            type: 'success',
                            text: message
                        })
                        this.clearDocument()
                        this.loaderOff();
                    }
                    else {
                        message = data.message || 'Ошибка при отправке данных. Попробуйте ещё раз'
                        notie.alert({
                            type: 'error',
                            text: message
                        })
                        this.clearDocument()
                        this.loaderOff();
                    }
                })
            },

            watchEffect() {
                const signaturePlaceholder = '{место_для_подписи}'
                const layoutParts = this.content.split(signaturePlaceholder)

                this.total_signatures = layoutParts.length - 1;

                this.processedLayout = layoutParts
                    .map((part, idx, allParts) => {
                        const signatureNumber = idx + 1

                        if (signatureNumber < allParts.length) {
                            const signatureId = `signature_${signatureNumber}`
                            const signatureButton = `<button class="btn btn--white" type="button" data-signature="${signatureId}" @click="showSignatureModal">Подписать</button>`
                            const signatureButtonSigned = `<button class="btn btn--white btn--signed" type="button" data-signature="${signatureId}" @click="showSignatureModal">Подписано</button>`

                            if (!this.signatures[signatureId]) {
                                this.unsetCurrentSignatureId()
                                return part + signatureButton
                            }

                            return part + signatureButtonSigned
                        } else {
                            return part
                        }
                    })
                    .join('')
            },
            clearDocument() {
                this.document_id = null,
                this.patient_id = null,
                this.patient_birthday = null,
                this.patient_name = null,
                this.content = null,
                this.processedLayout = null,
                this.template = 'download',
                this.signatures = {},
                this.currentSignatureId = null,
                this.total_signatures = 0
            },

            loadCanvas() {
                setTimeout(function() {
                    initCanvas();
                }, 500);
            },
            setSimplebar() {
                setTimeout(function() {
                    new SimpleBar(document.getElementById('content-inner'));
                }, 500)
            },

            showSignatureModal(e) {
                const signatureId = e.target.dataset.signature;
                this.setCurrentSignatureId(signatureId);
                this.show_sign_text = true;
                this.show_modal = true;
                this.loadCanvas();

            },
            hideSignatureModal() {
                this.clearSignature();
                this.show_modal = false;
            },

            setCurrentSignatureId(signatureId) {
                this.currentSignatureId = signatureId;
            },
            unsetCurrentSignatureId() {
                this.currentSignatureId = null;
            },
            setSignature(id, data) {
                this.signatures[id] = data;
                this.unsetCurrentSignatureId();
            },
            saveSignature() {
                if(this.show_sign_text) return;

                const signatureImage = canvas.toDataURL({
                    width: canvas.width,
                    height: canvas.height,
                    left: 0,
                    top: 0,
                    format: 'png'
                });
                this.setSignature(this.currentSignatureId, signatureImage);
                this.watchEffect();
                this.hideSignatureModal();
            },
            clearSignature() {
                this.show_sign_text = true;
                canvas.clear()
                this.unsetCurrentSignatureId();
                this.show_sign_text = true;
            },
        }))
    });
</script>
