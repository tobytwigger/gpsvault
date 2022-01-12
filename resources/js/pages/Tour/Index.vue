<template>
    <c-app-wrapper title="Tours">
        <v-alert
            outlined
            type="warning"
            prominent
            border="left"
        >
            Tours are still in development and will be available soon.
        </v-alert>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from '../../ui/layouts/CAppWrapper';
export default {
    name: "Index",
    components: {CAppWrapper}
}
</script>

<style scoped>

</style>
