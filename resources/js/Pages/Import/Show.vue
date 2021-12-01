<template>
    <div>
        {{ importData }}
    </div>
</template>

<script>
export default {
    name: "Show",
    props: {
        importData: {
            required: true,
            type: Object
        }
    }
}
</script>

<style scoped>

</style>
