<template>
    <div v-if="images.length > 0">
        <carousel :items-to-show="3.95" :wrap-around="true">
            <slide v-for="image in images" :key="image.id" class="p-2">
                <div class="carousel__item cursor-pointer" @click="viewingImage = image">
                    <img
                        :src="route('file.preview', image.id)"
                        :alt="image.caption ?? 'Activity media'"
                        class="shadow-lg rounded max-w-full h-auto align-middle border-none"/>
                </div>
            </slide>

            <template #addons>
                <navigation />
                <pagination />
            </template>
        </carousel>
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
    }
}
</script>

<style scoped>

</style>
