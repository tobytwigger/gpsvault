<template>
    <span class="text-sm text-gray-400">{{activitiesLoading}} currently loading</span>
</template>

<script>

export default {
    name: "StravaIntegrationAddon",
    props: {
        activitiesLoading: {
            required: true,
            type: Number
        }
    }
}
</script>

<style scoped>

</style>
