<template>
    <div>
        <div
            v-for="(image, index) in images"
            :key="index"
            class="pic"
            @click="showImg(index)"
        >
            <v-img :src="image.src" :alt="image.alt"></v-img>
            <img :src="src">
        </div>
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
            this.index = index
            this.visible = true
        },
        handleHide () {
            this.visible = false
        }
    }
}
</script>

<style scoped>

</style>
