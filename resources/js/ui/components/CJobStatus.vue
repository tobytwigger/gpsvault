<template>
    <job-status :job-alias="job" :tags="tags" :poll-interval="2000">
        <template v-slot:default="params">
            <div v-if="params === null || params.complete === true">
                <slot></slot>
            </div>
            <div v-else>
                <slot name="incomplete"></slot>
            </div>
        </template>
    </job-status>
</template>

<script>
export default {
    name: "CJobStatus",
    props: {
        job: {
            type: String,
            required: true,
        },
        tags: {
            type: Object,
            required: false,
            default: () => {}
        }
    }
}
</script>

<style scoped>

</style>
