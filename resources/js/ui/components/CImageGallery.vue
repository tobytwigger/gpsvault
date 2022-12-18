<template>
    <div>
        <v-sheet :max-height="maxHeight">
            <v-slide-group
                v-model="index"
                @change="showImg"
                class="pa-4"
                show-arrows
            >
                <v-slide-item
                    v-for="(image, localIndex) in images"
                    :key="localIndex"
                    v-slot="{ active, toggle }"
                >
                    <v-card
                        color="grey lighten-1"
                        class="ma-4"
                        @click="showImg(localIndex)"
                    >
                        <v-card-text>
                            <v-row
                                class="fill-height"
                                align="center"
                                justify="center"
                            >
                                <v-scale-transition>
                                    <v-img :src="image.thumbnail" :alt="image.alt" @click="toggle" height="200" width="100"></v-img>
                                </v-scale-transition>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-slide-item>
            </v-slide-group>
        </v-sheet>
        <vue-easy-lightbox
            :visible="showLightbox"
            :imgs="imageSrcs"
            :index="index"
            @hide="handleHide"
        ></vue-easy-lightbox>
    </div>
</template>

<script>
export default {
    name: "CImageGallery",
    props: {
        images: {
            required: true,
            type: Array
        },
        maxHeight: {
            required: false,
            type: Number,
            default: 300
        }
    },
    data() {
        return {
            showLightbox: false,
            index: 0
        }
    },
    computed: {
        imageSrcs() {
            return this.images.map(image => image.src);
        }
    },
    methods: {
        showImg (index) {
            this.index = index;
            this.showLightbox = true
        },
        handleHide () {
            this.showLightbox = false
        }
    }
}
</script>

<style scoped>

</style>
