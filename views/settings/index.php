<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
//use yii\bootstrap5\ActiveForm;
//use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\helpers\Url;

$this->title = 'Настройки';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="settings-index">
    <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <?= Html::encode($this->title) ?>
                    </div>
                    <div class="card-body">
                        <?= $form->field($model, 'app_name')->textInput()->label('Название приложения') ?>
                        <?= $form->field($model, 'rnova_api_url')->textInput()->label('Адрес API МИС') ?>
                        <?= $form->field($model, 'rnova_api_key')->textInput()->label('Ключ API МИС') ?>
                        <!-- <?//= $form->field($model, 'tablet_url')->textInput()->label('Базовый URL планшета') ?>-->
                        <?= $form->field($model, 'document_css')->textarea()->label('CSS-стили документов') ?>
                        <?= $form->field($model, 'tablet_css')->textarea()->label('CSS-стили планшета') ?>
                        <?= $form->field($model, 'cancel_unsigned')->checkbox(['label' => Html::tag('span','Отменять неподписанные документы'), 'labelOptions' => ['class' => 'ui-checkbox']]) ?>
                        <?= $form->field($model, 'update_on_demand')->checkbox(['label' => Html::tag('span','Обновлять планшеты вручную'), 'labelOptions' => ['class' => 'ui-checkbox']]) ?>
                        <?= $form->field($model, 'lifetime_days')->textInput()->label('Удалять документы через, дн.') ?>
                        <div class="form-group">
                            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Изображения
                    </div>
                    <div class="card-body">
                        <?php foreach($model->getDefaultImages() as $imageName) : ?>
                                <div class="card" style="margin: 10px 0">
                                    <div class="card-body">
                                        <?php if($fileName = $model->getImageByName($imageName)) : ?>
                                            <div class="img-container">
                                                <div class="img-content">
                                                    <a href="/sign/img/<?= $fileName ?>" data-fancybox>
                                                        <img src="/sign/img/<?= $fileName ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="img-action">
                                                    <a href="<?= Url::to(['settings/delete-img', 'name' => $imageName]) ?>" class="btn btn-sm btn-danger" data-confirm="Вы действительно хотите удалить изображение?">Удалить</a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?= $form->field($model, 'image_fields['.$imageName.']')->widget(FileInput::className(), [
                                            'options' => [
                                                'accept' => 'image/*',
                                                'multiple' => false
                                            ],
                                            'pluginOptions' => [
                                                'browseLabel' => 'Выбрать',
                                                'showPreview' => false,
                                                'showUpload' => false,
                                                'showRemove' => false,
                                            ]
                                        ])->label($imageName) ?>
                                    </div>

                                </div>
                        <?php endforeach; ?>
                        <div class="form-group">
                            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>
