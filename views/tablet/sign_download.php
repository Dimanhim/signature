<template x-if="isTemplate('download')">
    <div>
        <section class="wallpaper"><img src="/sign/img/logo-bg.svg" alt="">
            <div class="wallpaper__update"><p class="wallpaper__update-text">Загрузить документ</p>
                <button
                        class="wallpaper__update-button"
                        type="button"
                        @click="loadDocument"
                >
                    <img src="/sign/img/reload.svg" alt="">
                </button>
            </div>
        </section>

    </div>
</template>


