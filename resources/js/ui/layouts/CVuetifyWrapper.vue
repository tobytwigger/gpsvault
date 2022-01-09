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
    mounted() {
        this.$inertia.on('finish', () => this.checkDarkMode())
        this.checkDarkMode();
    },
    methods: {
        checkDarkMode() {
            if(this.$vuetify.theme.dark !== this.$page.props.settings.dark_mode) {
                this.$vuetify.theme.dark = this.$page.props.settings.dark_mode;
            }
        }
    }
}
</script>

<style scoped>

</style>
