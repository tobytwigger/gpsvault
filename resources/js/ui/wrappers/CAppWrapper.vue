<template>
    <c-base-page :title="title">
        <c-sidebar v-model="showingSidebar"></c-sidebar>

        <c-app-nav-bar title="Cycle Store" :drawer="showingSidebar" @update:drawer="showingSidebar = $event"></c-app-nav-bar>

        <v-main >
            <v-container style="background-color: #f3f4f6; height: 100%" fluid>
                <v-row align="start">
                    <v-col>
                        <v-sheet color="white" elevation="3" rounded class="text-center">
                            <h1 class="px-4">
                                {{title}}
                            </h1>
                        </v-sheet>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <slot name="content">
                            <v-sheet color="white" elevation="3" rounded width="100%">
                                <slot></slot>
                            </v-sheet>
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

    export default {
        name: 'CAppWrapper',
        props: {
            title: {
                required: true,
                type: String
            }
        },

        components: {
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
