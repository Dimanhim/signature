<?php
$color = '#45ac55';
if($model->clinic_id === 1) {
    $color = '#966eaa';
}
elseif($model->clinic_id === 2) {
    $color = '#e41280';
}
elseif($model->clinic_id === 3) {
    $color = '#bd0a7e';
}
elseif($model->clinic_id === 4) {
    $color = '#AB1270';
}
elseif($model->clinic_id === 6) {
    $color = '#B38040';
}
elseif($model->clinic_id === 7) {
    $color = '#B3885B';
}
?>

<template x-if="loader">
    <div class="vld-container">
        <div tabindex="0" class="vl-overlay vl-active vl-full-page" aria-busy="true" aria-label="Loading"
             style="z-index: 999999;">
            <div class="vl-background" style="background: rgb(255, 255, 255); opacity: 0.93;"></div>
            <div class="vl-icon">
                <svg viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" width="64" height="64" stroke="<?= $color ?>">
                    <g fill="none" fill-rule="evenodd">
                        <g transform="translate(1 1)" stroke-width="2">
                            <circle stroke-opacity=".25" cx="18" cy="18" r="18"></circle>
                            <path d="M36 18c0-9.94-8.06-18-18-18">
                                <animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18"
                                                  dur="0.8s" repeatCount="indefinite"></animateTransform>
                            </path>
                        </g>
                    </g>
                </svg>
            </div>
        </div>
    </div>
</template>
