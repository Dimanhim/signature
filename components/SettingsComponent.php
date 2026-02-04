<?php

namespace app\components;

use app\models\Setting;
use yii\base\Component;

class SettingsComponent extends Component
{
    private $data;

    /**
     *
     */
    public function init()
    {
        $this->setSettings();
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
