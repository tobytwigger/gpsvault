<template>
    <c-base-page :title="title">
        <c-sidebar v-model="showingSidebar"></c-sidebar>

        <c-app-nav-bar title="GPS Vault" :drawer="showingSidebar" @update:drawer="showingSidebar = $event"></c-app-nav-bar>

        <v-main >
            <v-container style="height: 100%" fluid>
                <v-row align="start">
                    <v-col>
                        <v-toolbar
                            class="mt-2 mx-2"
                        >
                            <v-spacer></v-spacer>

                            <v-toolbar-title>{{ title }}</v-toolbar-title>

                            <v-spacer></v-spacer>

                            <slot name="headerActions"></slot>
                        </v-toolbar>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <slot name="content">
                            <v-row v-if="actionSidebar">
                                <v-col cols="12" lg="8" xl="10">
                                    <v-sheet elevation="3" rounded width="100%">
                                        <slot></slot>
                                    </v-sheet>
                                </v-col>
                                <v-col cols="12" md="4" xl="2" class="d-flex justify-lg-center">
                                    <v-sheet elevation="3" rounded>
                                        <slot name="sidebar"></slot>
                                    </v-sheet>
                                </v-col>
                            </v-row>
                            <v-row v-else>
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
    import { Head } from '@inertiajs/inertia-vue';
    import CAppNavBar from '../components/Page/CAppNavBar';
    import CSidebar from '../components/Page/CSidebar';
    import CBasePage from './CVuetifyWrapper';
    import CFooter from '../components/Page/CFooter';

    export default {
        name: 'CAppWrapper',
        props: {
            title: {
                required: true,
                type: String
            },
            actionSidebar: {
                required: false,
                type: Boolean,
                default: false
            },
            headerAction: {
                required: false,
                type: Boolean,
                default: false
            },
        },

        components: {
            CFooter,
            CBasePage,
            CSidebar,
            CAppNavBar,
            Head,
        },

        data() {
            return {
                showingSidebar: false,
                navigationRoutes: [
                    // { component: markRaw({
                    //     template: '<v-logout><jet-responsive-nav-link as="button">Log Out</jet-responsive-nav-link></v-logout>'
                    // })},
                    // {id: 'logout', title: 'Log Out'},
                ]
            }
        }
    }
</script>
