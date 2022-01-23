<template>

    <v-app-bar app clipped-left>
        <v-app-bar-nav-icon @click.stop="$emit('update:drawer', !drawer)" v-if="$vuetify.breakpoint.mobile"></v-app-bar-nav-icon>

        <v-spacer></v-spacer>

        <v-toolbar-title>{{ title }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn
            fab
            small
            @click="startTour"
            class="px-2"
        >
            <v-icon>mdi-help</v-icon>
        </v-btn>

        <c-add-item
            class="px-2"
        ></c-add-item>
    </v-app-bar>

</template>

<script>

import CAddItem from './CAddItem';
export default {
    name: "CAppNavBar",
    components: {CAddItem},
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
    methods: {
        startTour() {
            this.introJs().start();
            this.introJs().addHints();
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
