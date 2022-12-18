<template>

    <v-app-bar app clipped-left>
        <v-app-bar-nav-icon @click.stop="$emit('update:drawer', !drawer)" v-if="$vuetify.breakpoint.mobile"></v-app-bar-nav-icon>

        <v-spacer></v-spacer>

        <v-toolbar-title>{{ title }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <c-feedback class="mx-2"></c-feedback>

        <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    small
                    @click="help"
                    class="mx-2"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon>mdi-help-circle</v-icon>
                </v-btn>
            </template>
            <span>Show a tour of the page</span>
        </v-tooltip>

        <c-add-item class="mx-2"></c-add-item>

    </v-app-bar>

</template>

<script>

import CAddItem from './CAddItem';
import CFeedback from './CFeedback';
export default {
    name: "CAppNavBar",
    components: {CFeedback, CAddItem},
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
        help() {
            this.$tour.start();
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
