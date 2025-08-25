<?php

/** @var yii\web\View $this */

use app\models\Setting;

$this->title = Setting::findOne(['key' => 'app_name'])->value;
?>
<div class="site-index">
    <?= __DIR__ ?>

</div>
