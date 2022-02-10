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
<!--            {{$setting}}-->
            {{test}}
            {{testTwo}}
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
            this.$testtwo = 'updated';
            // this.$setting = 'yellow';
            // if(this.$settings.getValue('unit_system') === 'metric') {
            //     this.$settings.setValue('unit_system', 'imperial');
            // } else {
            //     this.$settings.setValue('unit_system', 'metric');
            // }
        }
    },
    watch: {
        // '$setting'(val) {
        //     console.log('IN THE WATCHER');
        //     console.log(val);
        // }
    },
    computed: {
        test() {
            return this.$testtwo;
            console.log('begfore');
            console.log(this.$testtwo);
            console.log('after');
            // console.log('IN THE COMPUTED PROP');
            // return this.$setting;
        },
        testTwo() {
            // console.log('Diff setter');
            // return this.$settings;
            // return this.$setting.$data.unit_system;
        }
    }
}
</script>

<style scoped>

</style>
