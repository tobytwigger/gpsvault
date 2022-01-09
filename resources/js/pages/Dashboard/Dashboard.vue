<template>
    <c-app-wrapper title="Dashboard">
        <v-alert
            outlined
            type="warning"
            prominent
            border="left"
        >
            The dashboard is still in development and will be available soon.
        </v-alert>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
export default {
    name: "Dashboard",
    components: {CAppWrapper}
}
</script>

<style scoped>

</style>
