<template x-if="isTemplate('download')">
    <div>
        <section class="wallpaper"><img :src="logoBgPath" alt="">
            <div class="wallpaper__update"><p class="wallpaper__update-text">Загрузить договор</p>
                <button
                        class="wallpaper__update-button"
                        type="button"
                        @click="loadDocument"
                >
                    <img :src="reloadPath" alt="">
                </button>
            </div>
        </section>

    </div>
</template>


