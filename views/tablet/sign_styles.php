<?php
    $checkPath = '/sign/img/check.svg';
    $bg = '#45ac55';
    $bgDark = '#1f6229';

    switch ($model->clinic_id) {
        case 1 : {
            $checkPath = '/custom/alfa/img/prof/check.svg';
            $bg = '#966eaa';
            $bgDark = '#333232';
        }
            break;
        case 2 : {
            $checkPath = '/custom/alfa/img/alfa/check.svg';
            $bg = '#e41280';
            $bgDark = '#333232';
        }
            break;
        case 3 : {
            $checkPath = '/custom/alfa/img/kids/check.svg';
            $bg = '#bd0a7e';
            $bgDark = '#333232';
        }
            break;
        case 4 : {
            $checkPath = '/custom/alfa/img/3k/check.svg';
            $bg = '#AB1270';
            $bgDark = '#333232';
        }
            break;
        case 6 : {
            $checkPath = '/custom/alfa/img/line/check.svg';
            $bg = '#B38040';
            $bgDark = '#333232';
        }
            break;
        case 7 : {
            $checkPath = '/custom/alfa/img/smile/check.svg';
            $bg = '#B3885B';
            $bgDark = '#333232';
        }
            break;
    }
?>


<?php if($model->clinic_id === 3) : ?>
    <style>
        .wallpaper {
            background: linear-gradient(0deg, #c75283 0%, #e7737a 50%, #f8c197 100%) !important;
        }
        .wallpaper__update-text {
            color: #fff !important
        }
    </style>
<?php elseif($model->clinic_id === 6) : ?>
    <style>
        .wallpaper {
            background: url('/custom/alfa/img/line/splash-bg.jpg') no-repeat center/cover !important;
        }
    </style>
<?php elseif($model->clinic_id === 7) : ?>
    <style>
        .wallpaper {
            background: url('/custom/alfa/img/smile/splash-bg.jpg') no-repeat center/cover !important;
        }
    </style>
<?php endif; ?>
<style>
    @font-face {
        font-family: "Geometria";
        src: url('/fonts/Geometria/Geometria-Light.ttf') format('truetype');
        font-weight: normal;
        font-style: normal;
    }
    @font-face {
        font-family: "Geometria";
        src: url('/fonts/Geometria/Geometria-Bold.ttf') format('truetype');
        font-weight: bold;
        font-style: normal;
    }
    body {
        color: #333232;
        font-weight: 300;
        font-size: 18px;
        font-family: "Geometria",Roboto,Segoe UI,Helvetica,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji;
        line-height: 1.56;
        text-rendering: optimizeLegibility;
    }




    .doc__content .btn::before {
        background: url(<?= $checkPath ?>) no-repeat center/cover !important;
    }
    .btn {
        font-weight: 300 !important;
    }
    .btn.btn--signed {
        font-weight: 600 !important;
    }
    .modal__btns .btn,
    .doc__btn .btn,
    .doc__btn .btn__cancel.btn
    {
        background: <?= $bg ?> !important;
    }
    .modal__btns .btn.btn--dark {
        background: <?= $bgDark ?> !important;
    }
    .doc__btn .btn {
        background: <?= $bg ?> !important;
    }
    .ui-switch.ui-both.ui-switch-success :checked + i {
        background: <?= $bg ?>;
    }
    .modal__close span {
        background: <?= $bg ?>;
    }
    .simplebar-scrollbar::before {
        background-color: <?= $bg ?>;
    }








    .wallpaper {
        padding: 50px;
        background: #f3f5f8;
    }
    .wallpaper__update {
        background-color: transparent;
    }
    .wallpaper img {
        width: auto;
        max-height: 100%;
        object-fit: cover;
    }
    .wallpaper__update-text {
        margin-right: 40px;
        white-space: nowrap;
        font-family: "Geometria",Roboto,Segoe UI,Helvetica,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji;
        font-style: normal;
        font-weight: 300;
        font-size: 75px;
        line-height: 53px;
        color: #333232;
    }
    .doc__btn .btn:disabled {
        background: #f3f5f8 !important;
        color: #333232;
    }
</style>
