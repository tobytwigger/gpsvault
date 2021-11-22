<template>
    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow transform transition-all mb-4 w-full sm:w-1/3 sm:my-8 md:w-1/4">
        <div class="bg-white p-5">
            <div class="sm:flex sm:items-start">
                <div class="text-center sm:mt-0 sm:ml-2 sm:text-left">
                    <h3 class="text-sm leading-6 font-medium text-gray-400">{{ title }}</h3>
                    <p class="text-3xl font-bold text-black">{{ value }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "DataDisplayTile",
    props: {
        title: {
            required: true,
            type: String
        },
        value: {
            required: false,
            default: null,
            type: String
        },
        loading: {
            required: false,
            default: false,
            type: Boolean
        }
    }
}
</script>

<style scoped>

</style>
