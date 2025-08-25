<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
    <meta name="description" content="Place the meta description text here.">
    <meta name="robots" content="noindex, nofollow">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="/design/css/style.min.css?v=1693222499365">
</head>


<body>
<div class="wrapper">
    <header class="header">
        <div class="container">
            <div class="header__wrap">
                <div class="header__logo">
                    <img src="/design/img/logo.svg" alt="">
                </div>
                <p>Планшет &#8470;&nbsp;<b><?= $model->tablet ? $model->tablet->name : '' ?></b></p>
            </div>
        </div>
    </header>


    <main>
        <section class="doc">
            <div class="doc__head">
                <p>Ф.И.О. пациента: <b>{фио_пациента}</b></p>
                <p>Дата рождения: <b>{дата_рождения_пациента}</b></p>
            </div>

            <div class="doc__content" data-simplebar data-simplebar-auto-hide="false">
                <div class="doc__content-inner">
                    <?= $this->render('_document_content') ?>
                </div>
            </div>

            <div class="doc__btn">
                <button class="btn" type="button">Отправить</button>
            </div>
        </section>
    </main>
</div>

<div class="modal-overlay js-modal-overlay" data-modal-close></div>

<div class="modal modal-sign" id="modal-sign" data-modal-close>

    <div class="modal__content">
        <button id="modal-sign-close" data-modal-close class="modal__close" aria-label="Закрыть модальное окно">
            <span></span>
            <span></span>
        </button>
        <div class="modal__wrap">
            <div class="modal__sign js_sign">
                <canvas id="canvas" class="modal__sign-canvas"></canvas>
                <p class="modal__sign-text js_sign-text">Место для подписи</p>
            </div>
            <div class="modal__btns">
                <button class="btn" type="button">Подписать</button>
                <button class="btn btn--black js_clear-canvas" type="button">Очистить</button>
            </div>
        </div>
    </div>
</div>


<script src="/design/js/main.min.js?v=1693222499365"></script>
</body>

</html>
