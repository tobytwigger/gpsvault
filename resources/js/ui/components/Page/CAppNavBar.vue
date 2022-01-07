<template>

    <v-app-bar app clipped-left>
        <v-app-bar-nav-icon @click.stop="$emit('update:drawer', !drawer)" v-if="$vuetify.breakpoint.mobile"></v-app-bar-nav-icon>

        <v-spacer></v-spacer>

        <v-toolbar-title>{{ title }}</v-toolbar-title>

        <v-spacer></v-spacer>
        <!-- -->
    </v-app-bar>

</template>

<script>

export default {
    name: "CAppNavBar",
    props: {
        drawer: {
            required: true,
            type: Boolean
        },
        title: {
            required: true,
            type: String
        }
    },
    computed: {
        showingSidebar: {
            get() {
                return this.sidebar;
            },
            set(val) {
                this.$emit('update:sidebar', val)
            }
        }
    }
}
</script>

<style scoped>

</style>
