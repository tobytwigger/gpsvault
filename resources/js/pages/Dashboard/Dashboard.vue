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

        <v-alert
            outlined
            type="info"
            prominent
            border="left"
        >
            Your unit system is {{$setting.unit_system}}.

            Your theme is {{theme}}
            <v-btn @click="updateSettings">Update</v-btn>
        </v-alert>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
export default {
    name: "Dashboard",
    components: {CAppWrapper},
    mounted() {
        this.$settings.loadSetting('unit_system');
    },
    methods: {
        updateSettings() {
            if(this.unitSystem === 'metric') {
                this.$settings.setValue('unit_system', 'imperial');
            } else {
                this.$settings.setValue('unit_system', 'metrics');
            }
        }
    },
    computed: {
        unitSystem() {
            if(this.$setting.unit_system === undefined) {
                return 'undefined';
            }
            return this.$setting.unit_system;
        },
        theme() {
            return this.$setting.theme;
        }
    }
}
</script>

<style scoped>

</style>
