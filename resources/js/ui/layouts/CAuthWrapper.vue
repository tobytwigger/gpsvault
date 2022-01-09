<template>
    <c-base-page :title="title">

        <c-public-nav-bar :title="title" :drawer="showingSidebar" @update:drawer="showingSidebar = $event"></c-public-nav-bar>

        <v-main>
            <v-container class="fill-height" style="background-color: #f3f4f6;" fluid>
                <v-row
                    align="center"
                    class="fill-height"
                    justify="center">
                    <v-col
                        cols="12"
                        md="4"
                        sm="8">
                        <slot name="alerts"></slot>
                        <slot></slot>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </c-base-page>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue';
import CPublicNavBar from '../components/Page/CPublicNavBar';
import CSidebar from '../components/Page/CSidebar';
import CBasePage from './CVuetifyWrapper';

export default {
    name: 'CAuthWrapper',
    props: {
        title: String
    },

    components: {
        CBasePage,
        CSidebar,
        CPublicNavBar,
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
