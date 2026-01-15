<?php 

namespace app\models;

use yii\base\Model;

class SettingForm extends Model
{
    public $app_name = 'Сервис подписи';
    public $rnova_api_url = 'https://app.rnova.org/api/public/';
    public $rnova_api_key;
    public $tablet_url = 'https://localhost/tablet/?tabletId=';
    public $cancel_unsigned;
    public $update_on_demand;
    public $document_css;

    private $availableForApi = ['update_on_demand'];

    public function rules()
    {
        return [
            [['app_name', 'rnova_api_url', 'rnova_api_key', 'tablet_url'], 'required'],
            [['app_name', 'rnova_api_url', 'rnova_api_key', 'tablet_url', 'document_css'], 'string'],
            [['cancel_unsigned', 'update_on_demand'], 'boolean'],
        ];
    }

    public function setAttributesFromSettings($settings)
    {
        foreach ($settings as $key => $setting) {
            if (property_exists($this, $key)) {
                $this->$key = $setting->value;
            }
        }
    }

    public function saveSettings()
    {
        $settings = [
            'app_name' => $this->app_name,
            'rnova_api_url' => $this->rnova_api_url,
            'rnova_api_key' => $this->rnova_api_key,
            'tablet_url' => $this->tablet_url,
            'cancel_unsigned' => $this->cancel_unsigned ? '1' : '0',
            'update_on_demand' => $this->update_on_demand ? '1' : '0',
            'document_css' => $this->document_css,
        ];

        foreach ($settings as $key => $value) {
            $setting = Setting::findOne(['key' => $key]) ?? new Setting(['key' => $key]);
            $setting->value = (string) $value;

            if (in_array($key, $this->availableForApi)) {
              $setting->available_for_api = 1;
            }

            $setting->save();
        }

        return true;
    }
}