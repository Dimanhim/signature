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

    public function actionDeleteImg($name)
    {
        $json = Setting::getValueByName('images');
        $fileName = null;
        $data = [];

        if($images = json_decode($json, true)) {
            foreach($images as $imageName => $imageValue) {
                if($imageName == $name) {
                    $data[$imageName] = null;
                    $fileName = $imageValue;
                }
                else {
                    $data[$imageName] = $imageValue;
                }
            }
            $model = Setting::findOne(['key' => 'images']);
            $model->value = json_encode($data);
            if($model->save()) {
                if($fileName && file_exists(Yii::getAlias('@tablet') . '/'.$fileName)) {
                    unlink(Yii::getAlias('@tablet') . '/'.$fileName);
                }
                Yii::$app->session->setFlash('success', 'Изображение успешно удалено');
            }
        }

        return $this->redirect('index');
    }
}
