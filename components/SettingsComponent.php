<?php

namespace app\components;

use app\models\Setting;
use yii\base\Component;
use app\models\UserSignature;

class SettingsComponent extends Component
{
    private $data;
    private $signature = null;
    private $signatureModel = null;

    /**
     *
     */
    public function init()
    {
        $this->setSettings();
        $this->setSignature();
        parent::init();
    }

    /**
     * @return bool
     */
    public function setSettings()
    {
        if($this->data) return true;

        $settings = Setting::find()->all();
        if(!$settings) return false;

        foreach($settings as $setting) {
            $this->data[$setting->key] = $setting->value;
        }
    }

    public function setSignature()
    {
        if (!\Yii::$app->user->isGuest) {
            $this->signatureModel = UserSignature::findOne([
                'user_id' => \Yii::$app->user->id,
                'is_active' => 1
            ]);
        }
    }

    public function getSignatureModel()
    {
        if ($this->signatureModel) {
            return $this->signatureModel;
        }

        return new UserSignature([
            'user_id' => !\Yii::$app->user->isGuest ? \Yii::$app->user->id : null
        ]);
    }

    public function getSignature()
    {
        return $this->signatureModel->signature_data ?? null;
    }

    /**
     * @param null $paramName
     * @return |null
     */
    public function getParam($paramName = null)
    {
        if(!$paramName) return null;

        return $this->data[$paramName] ?? null;
    }
}
