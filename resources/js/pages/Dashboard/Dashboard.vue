<template>
    <c-app-wrapper title="Dashboard">
        <v-alert
            outlined
            type="warning"
            prominent
            border="left"
        >
            The dashboard is still in development and will be available soon.
	    {{$setting.unit_system}}
            <v-btn @click="updateSettings">Update</v-btn>
        </v-alert>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
export default {
    name: "Dashboard",
    components: {CAppWrapper},
    methods: {
        updateSettings() {
            if(this.$setting.unit_system === 'metric') {
                this.$settings.setValue('unit_system', 'imperial');
            } else {
                this.$settings.setValue('unit_system', 'metric');
            }
        }
    },
}
</script>

<style scoped>

</style>
