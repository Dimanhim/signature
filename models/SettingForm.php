<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class SettingForm extends Model
{
    public $app_name = 'Сервис подписи';
    public $rnova_api_url = 'https://app.rnova.org/api/public/';
    public $rnova_api_key;
    public $tablet_url = 'https://localhost/tablet/?tabletId=';
    public $cancel_unsigned;
    public $update_on_demand;
    public $document_css;
    public $tablet_css;
    public $images;
    public $lifetime_days;

    public $image_fields;

    private $availableForApi = ['update_on_demand'];
    private $_default_images = [
        'check' => ['extension' => 'svg'],
        'logo' => ['extension' => 'svg'],
        'logo-bg' => ['extension' => 'svg'],
        'reload' => ['extension' => 'svg'],
        'touch-icon-180' => ['extension' => 'png']
    ];

    public function rules()
    {
        return [
            [['app_name', 'rnova_api_url', 'rnova_api_key', 'tablet_url'], 'required'],
            [['app_name', 'rnova_api_url', 'rnova_api_key', 'tablet_url', 'document_css', 'tablet_css', 'lifetime_days'], 'string'],
            [['images', 'image_fields'], 'safe'],
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
        $this->handleImages();

        $settings = [
            'app_name' => $this->app_name,
            'rnova_api_url' => $this->rnova_api_url,
            'rnova_api_key' => $this->rnova_api_key,
            'tablet_url' => $this->tablet_url,
            'cancel_unsigned' => $this->cancel_unsigned ? '1' : '0',
            'update_on_demand' => $this->update_on_demand ? '1' : '0',
            'document_css' => $this->document_css,
            'tablet_css' => $this->tablet_css,
            'images' => $this->images,
            'lifetime_days' => $this->lifetime_days,
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

    public function handleImages()
    {
        $data = json_decode($this->images, true);

        if($this->image_fields) {
            foreach($this->image_fields as $imageName => $image) {
                $file = UploadedFile::getInstance($this, 'image_fields['.$imageName.']');
                $fileName = $this->getFileName($imageName, $file);
                if(!$fileName) continue;

                if($file->saveAs(\Yii::getAlias('@tablet') . '/' . $fileName)) {
                    $data[$imageName] = $fileName;
                }
            }
        }

        if($data) {
            $this->images = json_encode($data);
        }
    }

    public function getDefaultImages()
    {
        return array_keys($this->_default_images);
    }

    public function getFileName($iconName = null, $file = null)
    {
        if(!$iconName || !$file) return false;

        if(isset($this->_default_images[$iconName])) {
            $fileName = $iconName;
            $fileExtension = $this->_default_images[$iconName]['extension'];
            if($file && $file->extension == $fileExtension) {
                return $fileName . '.' . $fileExtension;
            }
        }
        return false;
    }

    public function getImageByName($imageName)
    {
        $values = Setting::getImages();

        return $values[$imageName] ?? null;
    }
}
