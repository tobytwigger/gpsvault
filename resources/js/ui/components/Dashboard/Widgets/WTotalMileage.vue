<template>
    <div>
        You have gone {{distance}}.
    </div>
</template>

<script>
export default {
    name: "WTotalMileage",
    props: {
        distance: {
            required: true,
            type: Number
        }
    }
}
</script>

<style scoped>

</style>
