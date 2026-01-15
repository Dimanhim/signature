<?php

namespace app\models;

use Yii;
use app\models\Image;
use app\models\Gallery;
use himiklab\sortablegrid\SortableGridBehavior;
use himiklab\thumbnail\EasyThumbnailImage;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\UploadedFile;

class BaseModel extends ActiveRecord
{
    const DEFAULT_ATTRIBUTE = 'name';

    public $video_field;
    public $image_field;
    public $image_fields;
    public $image_preview_field = [];

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            'sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'position',
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function getModelName()
    {
        return self::className()::modelName();
    }

    /**
     * @return mixed
     */
    public function getTypeId()
    {
        return self::className()::typeId();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['image_field', 'image_fields', 'image_preview_field'], 'safe'],
            [['is_active', 'position'], 'integer'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'image_field' => 'Изображение',
            'image_fields' => 'Изображение',
            'image_preview_field' => 'Превью изображения',
            'is_active' => 'Активность',
            'position' => 'Сортировка',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * @return mixed
     */
    public static function findModels()
    {
        return self::className()::find()->andWhere(['is_active' => 1])->orderBy(['position' => 'SORT ASC']);
    }

    /**
     * @return array
     */
    public static function getList()
    {
        return ArrayHelper::map(self::findModels()->asArray()->all(), 'id', self::DEFAULT_ATTRIBUTE);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->handleImages();
        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImage()
    {
        return $this->hasOne(Image::className(), ['id' => 'image_id']);
    }

    /**
     * @return bool
     */
    public function getMainImage()
    {
        if($this->gallery && $this->gallery->images) {
            return $this->gallery->images[0];
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getVideo()
    {
        if($this->gallery && $this->gallery->images) {
            foreach($this->gallery->images as $image) {
                if($image->isVideo) {
                    return $image;
                }
            }
        }
        return false;
    }

    public function fullPath($path)
    {
        $prefix = Yii::$app->db->tablePrefix;
        $dirName = str_replace($prefix, '', self::className()::tableName());
        return '/images'.$dirName.'/'.$path;
    }

    /**
     * @return array|ActiveRecord[]
     */
    public function getGallery()
    {
        return Gallery::find()->where(['object_type' => $this->typeId, 'object_id' => $this->id])->one();
    }

    /**
     * @param $models
     * @return bool
     */
    public function getListChunk($models)
    {
        if(!$models) return false;
        return Yii::$app->controller->renderPartial('//chunks/_list_names', [
            'models' => $models,
        ]);
    }

    /**
     * @param $models
     * @return bool
     */
    public function getListLinksChunk($models, $link)
    {
        if(!$models) return false;
        return Yii::$app->controller->renderPartial('//chunks/_list_links', [
            'models' => $models,
            'link' => $link,
        ]);
    }

    private function handleImages() {

        $prefix = Yii::$app->db->tablePrefix;
        $dirName = str_replace($prefix, '', self::className()::tableName());
        $filesDir = Yii::getAlias('@upload')."/{$dirName}/";
        if (!file_exists($filesDir)) mkdir($filesDir, 0777, true);

        if($files = UploadedFile::getInstances($this, 'image_fields')) {
            if(!$gallery = $this->gallery) {
                $gallery = new Gallery();
                $gallery->object_id = $this->id;
                $gallery->object_type = $this->typeId;
                $gallery->save();
            }
            foreach($files as $file) {
                $fileName = $this->id.'_'.uniqid();
                $filePath = "/{$dirName}/{$fileName}.{$file->extension}";

                if (!$file->saveAs(Yii::getAlias('@upload').$filePath)) {
                    continue;
                }

                $image = Image::create($filePath, $gallery->id);
            }
        }
    }

    /**
     * @return string
     */
    public function getImg($width = 100, $height = 100)
    {
        if($this->image) {
            $img = EasyThumbnailImage::thumbnailImg(Yii::getAlias('@upload').$this->image->path, $width, $height, EasyThumbnailImage::THUMBNAIL_OUTBOUND);
            return Html::a($img, '/upload/'.$this->image->path, ['target' => '_blanc']);
        }
    }

    public function getImageByPath($path, $width, $height)
    {
        $prefix = Yii::$app->db->tablePrefix;
        $dirName = str_replace($prefix, '', self::className()::tableName());

        $img = EasyThumbnailImage::thumbnailImg(Yii::getAlias('@upload').'/'.$path, $width, $height, EasyThumbnailImage::THUMBNAIL_OUTBOUND);
        return $img;
    }

    public function getMainImageHtml()
    {
        if($this->mainImage) {
            if(in_array($this->mainImage->extension, $this->mainImage->_images_extensions)) {
                return Html::a(
                    EasyThumbnailImage::thumbnailImg(Yii::getAlias('@upload').$this->mainImage->path, 100, 100, EasyThumbnailImage::THUMBNAIL_OUTBOUND),
                    EasyThumbnailImage::thumbnailFileUrl(Yii::getAlias('@upload').$this->mainImage->path, 1000, 1000, EasyThumbnailImage::THUMBNAIL_OUTBOUND),
                    ['data-fancybox' => 'gallery']
                );
            }
            else {
                return Html::a($this->mainImage->getExtensionSvg(20, 20, '#000'), $this->mainImage->fileUrl, ['target' => '_blanc', 'download' => true]);
            }
        }
    }
    public function getImagesHtml()
    {
        if($this->gallery) return $this->gallery->getPreviewListHTML();
    }
    public function getImagesPaths()
    {
        $imagesPaths = [];
        $videoPaths = [];
        if($this->gallery and ($images = $this->gallery->images)) {
            foreach($images as $image) {
                $path = Yii::$app->request->hostInfo.'/upload'.$image->path;
                if($image->isImage) {
                    $imagesPaths[] = $path;
                }
                if($image->isVideo) {
                    $videoPaths[] = $path;
                }

            }
        }
        return ['images' => $imagesPaths, 'video' => $videoPaths];
    }
    public function getAvatar()
    {
        if($this->mainImage) {
            return '/upload'.$this->mainImage->path;
        }
        return '../img/no-img.png';
    }

    public function getActive()
    {
        return $this->is_active ? 'Да' : 'Нет';
    }
    public function getCreatedAt()
    {
        return date('d.m.Y H:i', $this->created_at);
    }
    public function getUpdatedAt()
    {
        return date('d.m.Y H:i', $this->updated_at);
    }

    public function getImagesField($form)
    {
        return Yii::$app->controller->renderPartial('//chunks/_images_form_field', [
            'form' => $form,
            'model' => $this,
        ]);
    }

    public function getErrorString()
    {
        if($this->errors) {
            foreach($this->errors as $attributes) {
                if($attributes) {
                    foreach($attributes as $attribute) {
                        return $attribute;
                    }
                }
            }
        }
        return false;
    }
}
