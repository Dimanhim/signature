<?php

use yii\helpers\Url;

?>

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
                'download', 'document', 'qr', 'qr_messages'
            ],
            template: 'download',
            signatures: {},
            custom: {},
            currentSignatureId: -null,
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
                this.initCustomImages();
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
            async loadResponse(method, params) {
                this.loaderOn();
                const response = await fetch(this.apiUrl + method, {
                    method: 'POST',
                    body: params
                });
                return await response.json();
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

                const response = this.loadResponse('get-documents', params)

                response.then((res) => {
                    if(res && res.data && res.data.length) {
                        let result = res.data[0];

                        this.document = result;
                        this.document_id = result.document_id;
                        this.patient_id = result.patient_id;
                        this.patient_name = result.patient_name;
                        this.patient_birthday = result.patient_birthday;
                        this.content = result.content;
                        this.payment_option = result.payment_option;

                        this.appointment = res.appointment || null;
                        this.invoices = res.invoices || [];
                        this.qr_link = res.qr_link || null;

                        this.watchEffect();

                        this.setSimplebar();
                        if(this.content.length) {
                            this.setTemplate('document')
                        }

                        // if (this.tabletId == 50 && this.payment_option == 1) {
                        //     this.setTemplate('qr');
                        //     this.$nextTick(() => {
                        //         this.generateQr();
                        //     });
                        // }

                        // if (this.tabletId == 50 && this.payment_option == 1) {
                        //     this.qr_message = 'Найдено несколько неоплаченных счетов!';
                        //     this.setTemplate('qr_messages');
                        // }


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
                setTimeout(() => {
                    const el = document.getElementById('content-inner');
                    if (el) {
                        new SimpleBar(el);
                    }
                }, 500);
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




            // CUSTOM
            clinicId: <?= $model->clinic_id ?>,
            reloadPath: '/sign/img/reload.svg',
            logoPath: '/sign/img/logo.svg',
            logoBgPath: '/sign/img/logo-bg.svg',
            checkPath: null,

            customImages: [
                {
                    id: 1,
                    title: 'Альфа Проф',
                    reload: '/custom/alfa/img/prof/reload.svg',
                    logo: '/custom/alfa/img/prof/logo.svg',
                    logoBg: '/custom/alfa/img/prof/logo-bg.png',
                    check: '/custom/alfa/img/prof/check.svg',
                },
                {
                    id: 2,
                    title: 'Альфа',
                    reload: '/custom/alfa/img/alfa/reload.svg',
                    logo: '/custom/alfa/img/alfa/logo.svg',
                    logoBg: '/custom/alfa/img/alfa/logo-bg.svg',
                    check: '/custom/alfa/img/alfa/check.svg',
                },
                {
                    id: 3,
                    title: 'Альфа Kids',
                    reload: '/custom/alfa/img/kids/reload.svg',
                    logo: '/custom/alfa/img/kids/logo.svg',
                    logoBg: '/custom/alfa/img/kids/logo-bg.png',
                    check: '/custom/alfa/img/kids/check.svg',
                },
                {
                    id: 4,
                    title: '3К',
                    reload: '/custom/alfa/img/3k/reload.svg',
                    logo: '/custom/alfa/img/3k/logo.svg',
                    logoBg: '/custom/alfa/img/3k/logo-bg.svg',
                    check: '/custom/alfa/img/3k/check.svg',
                },
                {
                    id: 6,
                    title: 'Альфа Линия',
                    reload: '/custom/alfa/img/line/reload.svg',
                    logo: '/custom/alfa/img/line/logo.svg',
                    logoBg: null,
                    check: '/custom/alfa/img/line/check.svg',
                },
                {
                    id: 7,
                    title: 'Альфа Смайл',
                    reload: '/custom/alfa/img/smile/reload.svg',
                    logo: '/custom/alfa/img/smile/logo.svg',
                    logoBg: null,
                    check: '/custom/alfa/img/smile/check.svg',
                },
            ],

            initCustomImages() {
                let clinics = Object.values(this.customImages).filter((item) => item.id === this.clinicId), clinic;
                if(clinics.length) {
                    clinic = clinics[0];
                    this.reloadPath = clinic.reload;
                    this.logoPath = clinic.logo;
                    this.logoBgPath = clinic.logoBg;
                }
            },
            cancelDocument() {
                if(!confirm('Вы действительно хотите отменить документ?')) return;

                this.loaderOn();
                const params = new URLSearchParams();
                params.set('document_id', this.document_id);

                const response = this.loadData('cancel-document', params)

                response.then((data) => {
                    this.setTemplate('download')
                    notie.alert({
                        type: 'success',
                        text: 'Документ успешно отменен',
                    });
                    this.loaderOff();
                });
            },


            // PAYMENT
            payment_option: 0,
            qr_link: null,
            qr_message: '',
            appointment: [],
            invoices: [],
            generateQr() {
                const container = document.getElementById('qr-container');
                if (container && this.qr_link) {
                    container.innerHTML = '';

                    const qr = QRCreator(this.qr_link, {
                        modsize: 10,
                        margin: 0
                    });

                    container.append(qr.result);
                }
            },


        }))
    });
</script>