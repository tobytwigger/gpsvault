<template>
    <div class="flex flex-row">

        <div class="flex flex-col rounded-lg p-4">
            <ul class="divide-y divide-gray-300">
                <slot name="menu"></slot>
            </ul>
        </div>

        <div class="flex flex-col flex-grow">
            <div class="flex flex-row content-center py-3 px-3">
                <span class="text-lg font-bold pr-4">{{title}}</span>
                <div v-for="dataItem in data" class="flex flex-col text-center justify-start px-4">
                    <span class="text-sm text-gray-600 font-semibold">{{dataItem.label}}</span>
                    <span class="font-semibold">{{dataItem.value}}</span>
                </div>
                <div class="flex flex-grow"></div>
                <slot name="titleAction"></slot>
            </div>

            <slot name="belowTitle"></slot>


            <div class="px-3 py-4 bg-gray-50 shadow-md content-center">
                <div class="content-center">
                    <slot></slot>
                </div>
            </div>
        </div>

    </div>
</template>

<script>

export default {
    name: "SyncLayout",
    props: {
        title: {
            required: true,
            type: String
        },
        data: {
            required: false,
            type: Array
        }
    }
}
</script>

<style scoped>

</style>
