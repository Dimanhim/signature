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
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
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
            'css/awesome/line-awesome-font-awesome.min.css',
            'css/bootstrap-icons.css',
            'css/jquery.fancybox.min.css',
            'css/select2.min.css',
            'css/chosen.css',
            'css/jquery-ui.min.css',
            'css/site.css?v='.mt_rand(1000,10000),
            'css/app.css?v='.mt_rand(1000,10000),
        ];
    }

    /**
     * @return array
     */
    public static function getJs()
    {
        return [
            'js/bootstrap.min.js',
            'js/jquery.fancybox.min.js',
            'js/select2.min.js',
            'js/chosen.jquery.min.js',
            'js/inputmask.js',
            'js/jquery.inputmask.js',
            'js/jquery-ui.min.js',
            'js/common.js?v='.mt_rand(1000,10000),
        ];
    }
}
