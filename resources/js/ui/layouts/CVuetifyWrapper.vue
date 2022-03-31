<template>
    <div>
        <Head :title="title" />

        <v-app>
            <slot></slot>
        </v-app>

    </div>
</template>

<script>
export default {
    name: "CVuetifyWrapper",
    props: {
        title: String
    },
    watch: {
        '$setting.dark_mode': function() {
            this.updateVuetifyTheme();
        }
    },
    methods: {
        updateVuetifyTheme() {
            this.$vuetify.theme.dark = this.$setting.dark_mode;
        }
    },
    mounted() {
        this.updateVuetifyTheme();
    },
}
</script>

<style scoped>

</style>
