<template>
    <c-base-page :title="title">
        <c-sidebar v-model="showingSidebar"></c-sidebar>

        <c-app-nav-bar title="GPS Vault" :drawer="showingSidebar"
                       @update:drawer="showingSidebar = $event"></c-app-nav-bar>

        <v-main>
            <v-container style="height: 100%" fluid>
                <v-row align="start">
                    <v-col>
                        <v-toolbar
                            class="mt-2 mx-2"
                        >
                            <slot name="prependActions"></slot>

                            <v-spacer></v-spacer>

                            <v-toolbar-title>{{ title }}</v-toolbar-title>

                            <v-spacer></v-spacer>

                            <slot name="headerActions"></slot>

                            <c-sub-menu :menu-items="menuItems" v-if="menuItems.length > 0" @sub-menu-click="callMenuAction">
                                <template v-slot:activator="{ on: menu, attrs }">
                                    <v-tooltip bottom>
                                        <template v-slot:activator="{ on: tooltip }">
                                            <v-btn
                                                icon

                                                v-bind="attrs"
                                                v-on="{ ...tooltip, ...menu }"
                                            >
                                                <v-icon>mdi-dots-vertical</v-icon>
                                            </v-btn>
                                        </template>
                                        <span>Menu</span>
                                    </v-tooltip>
                                </template>
                            </c-sub-menu>
                        </v-toolbar>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <slot name="content">
                            <v-row>
                                <v-col cols="12">
                                    <v-sheet elevation="3" rounded width="100%">
                                        <slot></slot>
                                    </v-sheet>
                                </v-col>
                            </v-row>
                        </slot>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>

    </c-base-page>
</template>

<script>
import {Head} from '@inertiajs/vue3';
import CAppNavBar from '../components/Page/CAppNavBar';
import CSidebar from '../components/Page/CSidebar';
import CBasePage from './CVuetifyWrapper';
import CFooter from '../components/Page/CFooter';
import CSubMenu from '../components/Page/CSubMenu';

export default {
    name: 'CAppWrapper',
    props: {
        title: {
            required: true,
            type: String
        },
        menuItems: {
            required: false,
            type: Array,
            default: () => []
        }
    },

    components: {
        CSubMenu,
        CFooter,
        CBasePage,
        CSidebar,
        CAppNavBar,
        Head,
    },

    data() {
        return {
            showingSidebar: false,
            navigationRoutes: []
        }
    },
    methods: {
        callMenuAction(item) {
            if(item.hasOwnProperty('action')) {
                item.action();
            }
        }
    }
}
</script>
