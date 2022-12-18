<template>
    <c-base-page :title="title">

        <c-public-nav-bar :title="title" :drawer="showingSidebar" @update:drawer="showingSidebar = $event"></c-public-nav-bar>

        <v-main style="padding-bottom: 0">
            <v-container class="fill-height" style="background-color: #f3f4f6;" fluid>
                <slot name="alerts"></slot>

                <slot></slot>
            </v-container>
        </v-main>

        <c-footer></c-footer>
    </c-base-page>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue';
import CPublicNavBar from '../components/Page/CPublicNavBar';
import CSidebar from '../components/Page/CSidebar';
import CBasePage from './CVuetifyWrapper';
import CFooter from '../components/Page/CFooter';

export default {
    name: 'CPublicWrapper',
    props: {
        title: String
    },

    components: {
        CFooter,
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
