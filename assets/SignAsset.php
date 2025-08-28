<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SignAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'sign/css/main.css',
    ];
    public $js = [
    ];

    public $depends = [
        //'yii\web\YiiAsset',
        //'yii\bootstrap5\BootstrapAsset',
    ];

    /**
     *
     */
    public function init()
    {
        $this->css = static::getCss();
        $this->js = static::getJs();
        return parent::init();
    }

    /**
     * @return array
     */
    public static function getCss()
    {
        return [
            'sign/css/simplebar.min.css',
            'sign/css/notie.min.css',
            'sign/css/main.css?v='.mt_rand(1000,10000),
            'sign/css/custom.css?v='.mt_rand(1000,10000),
        ];
    }

    /**
     * @return array
     */
    public static function getJs()
    {
        return [
            'sign/js/simplebar.min.js',
            'sign/js/notie.min.js',
            'sign/js/alpineDev.min.js',
        ];
    }
}
