<template>
    <div>
        {{results}}
    </div>
</template>

<script>
export default {
    name: "StravaActivity",
    props: {
        results: {
            required: true,
            type: Array
        }
    }
}
</script>

<style scoped>

</style>
