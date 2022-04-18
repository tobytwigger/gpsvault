<template>
    <c-app-wrapper :title="place.name">
        {{place}}
    </c-app-wrapper>

</template>

<script>
import CAppWrapper from '../../ui/layouts/CAppWrapper';
export default {
    name: "Show",
    components: {CAppWrapper},
    props: {
        place: {
            required: true,
            type: Object
        }
    }
}
</script>

<style scoped>

</style>
