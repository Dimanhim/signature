<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Setting;
use app\models\SettingForm;

class SettingsController extends Controller
{
    public function actionIndex()
    {
        $model = new SettingForm();
        $settings = Setting::find()->indexBy('key')->all();

        $model->setAttributesFromSettings($settings);

        if ($model->load(Yii::$app->request->post()) && $model->saveSettings()) {
            Yii::$app->session->setFlash('success', 'Настройки сохранены.');
            return $this->refresh();
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}