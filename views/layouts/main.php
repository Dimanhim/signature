<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use app\models\User;
use app\models\Setting;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => Setting::findOne(['key' => 'app_name'])->value,
        'brandUrl' => Setting::findOne(['key' => 'app_name'])->value,
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
    ]);
    $items = [];
    $items[] = ['label' => 'Создать документ', 'url' => ['document/index']];
    if(User::isAdmin()) {
        $items[] = ['label' => 'Планшеты', 'url' => ['tablet/index']];
        $items[] = ['label' => 'Шаблоны', 'url' => ['template/index']];
        $items[] = ['label' => 'Пользователи', 'url' => ['user/index']];
        $items[] = ['label' => 'Настройки', 'url' => ['settings/index']];
    }
    if(Yii::$app->user->isGuest) {
        $items[] = ['label' => 'Вход', 'url' => ['/site/login']];
    }
    else {
        $items[] = '<li class="nav-item">'
        . Html::beginForm(['/site/logout'])
        . Html::submitButton(
            'Выход (' . Yii::$app->user->identity->username . ')',
            ['class' => 'nav-link btn btn-link logout']
        )
        . Html::endForm()
        . '</li>';
    }
    /*$items = [
        ['label' => 'Создать документ', 'url' => ['document/index']],
        ['label' => 'Планшеты', 'url' => ['tablet/index']],
        ['label' => 'Шаблоны', 'url' => ['template/index']],
        ['label' => 'Пользователи', 'url' => ['user/index']],
        Yii::$app->user->isGuest
            ? ['label' => 'Вход', 'url' => ['/site/login']]
            : '<li class="nav-item">'
            . Html::beginForm(['/site/logout'])
            . Html::submitButton(
                'Выход (' . Yii::$app->user->identity->username . ')',
                ['class' => 'nav-link btn btn-link logout']
            )
            . Html::endForm()
            . '</li>'
    ];*/
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => $items,
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs'], 'homeLink' => ['label' => 'Главная', 'url' => 'site/index']]) ?>
        <?php endif ?>
        <p class="info-message"></p>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; <?= Setting::findOne(['key' => 'app_name'])->value.' '. date('Y') ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
