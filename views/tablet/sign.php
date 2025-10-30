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
            document: null,
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
            custom: {},
            currentSignatureId: null,
            currentCustom: null,
            total_signatures: 0,
            total_custom: 0,
            customId: 0,
            show_modal: false,
            show_sign_text: true,
            custom_placeholders: [],
            typeList: [
                'signature', 'text', 'radio'
            ],
            type: null,

            textCustomField: null,
            radioCustomField: null,

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
                return 'Подписей:&nbsp;' + this.signedSignatures() + '&nbsp;из&nbsp;' + (this.total_signatures + this.total_custom);
            },
            signedSignatures() {
                return Object.values(this.signatures).filter((item) => item).length + Object.values(this.custom).filter((item) => item).length
            },
            isAllSigned() {
                return (this.total_signatures + this.total_custom - this.signedSignatures()) == 0;
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

                        this.document = result;
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
                    signatures: { ...this.signatures },
                    custom: { ...this.custom }
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
                this.signaturesEffect(() => {
                    this.customEffect();
                });

            },
            signaturesEffect(callback) {
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
                    .join('');
                callback();
            },
            customEffect() {
                this.customId = 0;
                this.setCustomPlaceholders(() => {
                    this.total_custom = 0;
                    this.custom_placeholders.forEach((item) => {
                        const customPlaceholder = '{' + item.placeholder + '}';
                        const layoutParts = this.processedLayout.split(customPlaceholder)

                        this.total_custom += layoutParts.length - 1;

                        this.processedLayout = layoutParts
                            .map((part, idx, allParts) => {
                                const customNumber = idx + 1
                                if (customNumber < allParts.length) {
                                    this.customId++
                                    const customId = `custom_${this.customId}`

                                    let customButton, customButtonSigned;

                                    if(item.type === 3) {
                                        customButton = `<button class="btn btn--white btn--sm" type="button" data-custom="${customId}" data-placeholder="${item.placeholder}" @click="showTextModal">Ввести</button>`
                                        customButtonSigned = `<button class="btn btn--white btn--sm btn--signed" type="button" data-custom="${customId}" data-placeholder="${item.placeholder}" @click="showTextModal">Введено</button>`
                                    }
                                    else if(item.type === 4) {
                                        customButton = `<button class="btn btn--white btn--sm" type="button" data-custom="${customId}" data-placeholder="${item.placeholder}" @click="showRadioModal">Выбрать</button>`
                                        customButtonSigned = `<button class="btn btn--white btn--sm btn--signed" type="button" data-custom="${customId}" data-placeholder="${item.placeholder}" @click="showRadioModal">Выбрано</button>`
                                    }


                                    if (!this.custom[customId]) {
                                        //this.unsetCurrentSignatureId()
                                        return part + customButton
                                    }

                                    return part + customButtonSigned
                                } else {
                                    return part
                                }
                            })
                            .join('')
                    })
                });
            },
            setCustomPlaceholders(callback) {
                if(!this.document || !this.document.custom_params) return;
                this.document.custom_params.forEach((item) => {
                    if(item.type === 3 || item.type === 4) {
                        this.custom_placeholders.push(item)
                    }
                })
                callback();
            },
            clearDocument() {
                this.document = null,
                this.document_id = null,
                this.patient_id = null,
                this.patient_birthday = null,
                this.patient_name = null,
                this.content = null,
                this.processedLayout = null,
                this.template = 'download',
                this.signatures = {},
                this.custom = {},
                this.currentSignatureId = null,
                this.currentCustom = null,
                this.textCustomField = null,
                this.radioCustomField = null,
                this.total_signatures = 0
                this.total_custom = 0
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
                this.setType('signature');
                this.loadCanvas();

            },
            hideSignatureModal() {
                this.clearSignature();
                this.show_modal = false;
                this.setType(null)
            },

            showTextModal(e) {
                const id = e.target.dataset.custom;
                const placeholder = e.target.dataset.placeholder;
                this.setCurrentCustom(id, placeholder);
                this.show_modal = true;
                this.setType('text');
                this.setCustomText();
            },
            setCustomText() {
                for (const k in this.custom) {
                    if(k === this.currentCustom.id) {
                        this.textCustomField = this.custom[k].data
                    }
                }
            },
            hideTextModal() {
                this.clearText();
                this.show_modal = false;
                this.setType(null)
                this.textCustomField = null;
            },

            showRadioModal(e) {
                const id = e.target.dataset.custom;
                const placeholder = e.target.dataset.placeholder;
                this.setCurrentCustom(id, placeholder);
                this.show_modal = true;
                this.setType('radio');
                this.setCustomRadio();
            },
            setCustomRadio() {
                for (const k in this.custom) {
                    if(k === this.currentCustom.id) {
                        this.radioCustomField = this.custom[k].data === 'Да' ? true : false;
                    }
                }
            },
            hideRadioModal() {
                this.show_modal = false;
                this.setType(null)
                this.radioCustomField = null;
            },

            setCurrentSignatureId(signatureId) {
                this.currentSignatureId = signatureId;
            },
            unsetCurrentSignatureId() {
                this.currentSignatureId = null;
            },
            setCurrentCustom(id, placeholder) {
                this.currentCustom = {id: id, placeholder: placeholder}
            },
            unsetCurrentCustom() {
                this.currentCustom = null;
            },

            setSignature(id, data) {
                this.signatures[id] = data;
                this.unsetCurrentSignatureId();
            },
            setCustom(currentCustom, data) {
                this.custom[currentCustom.id] = {id: currentCustom.id, placeholder: currentCustom.placeholder, data: data}
                this.unsetCurrentCustom();
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
            saveText() {
                this.setCustom(this.currentCustom, this.textCustomField);
                this.watchEffect();
                this.hideTextModal();
            },
            clearText() {
                this.textCustomField = null;
            },
            saveRadio() {
                const text = this.radioCustomField ? 'Да' : 'Нет';
                this.setCustom(this.currentCustom, text);
                this.watchEffect();
                this.hideRadioModal();
            },

            setType(typeName) {
                this.type = typeName
            },
            isTypeSignature() {
                return this.type === 'signature';
            },
            isTypeText() {
                return this.type === 'text';
            },
            isTypeRadio() {
                return this.type === 'radio';
            },
        }))
    });
</script>
