<template>
    <c-app-wrapper title="Dashboard">
        <v-alert
            outlined
            type="warning"
            prominent
            border="left"
        >
            The dashboard is still in development and will be available soon.
            {{test}}
        </v-alert>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
export default {
    name: "Dashboard",
    components: {CAppWrapper},
    data() {
        return {
            test: null
        }
    },
    mounted() {
        setInterval(() => {
            this.$settings.loadSetting('unit_system');
            this.test = this.$settings.getValue('unit_system');
        }, 5000);
    }
}
</script>

<style scoped>

</style>
