<template>
    <div>
        <Head :title="title" />

        <div class="flex min-h-screen bg-gray-100">
            <div class="flex-shrink">
                <v-sidebar :show="showingSidebar" :routes="navigationRoutes"></v-sidebar>
            </div>
            <div class="flex-grow">
                <nav class="bg-white border-b border-gray-100">
                    <!-- Primary Navigation Menu -->
                    <v-nav-bar v-model:sidebar="showingSidebar" :title="title"></v-nav-bar>
                </nav>

                <!-- Page Content -->
                <main>
                    <slot></slot>
                </main>
            </div>
        </div>
    </div>
</template>

<script>
    import JetDropdown from '@/Jetstream/Dropdown.vue'
    import JetDropdownLink from '@/Jetstream/DropdownLink.vue'
    import JetNavLink from '@/Jetstream/NavLink.vue'
    import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink.vue'
    import { Head, Link } from '@inertiajs/inertia-vue3';
    import VNavigation from './VSidebar';
    import { markRaw } from 'vue'

    export default {
        name: 'VAppLayout',
        props: {
            title: String
        },

        components: {
            VNavigation,
            Head,
            JetDropdown,
            JetDropdownLink,
            JetNavLink,
            JetResponsiveNavLink,
            Link,
        },

        data() {
            return {
                showingSidebar: true,
                navigationRoutes: [
                    {title: 'Dashboard', href: route('dashboard'), active: route().current('dashboard'), icon: 'fas fa-home'},
                    {title: 'Activities', href: route('activity.index'), active: route().current('activity.index'), icon: 'fas fa-biking'},
                    {title: 'Sync', href: route('sync.index'), active: route().current('sync.index'), icon: 'fas fa-sync'},
                    {component: markRaw({
                        template: '<hr style="border-color: rgba(0, 0, 0, 0.1); margin: 20px;">'
                    })},
                    {title: 'Profile', href: route('profile.show'), active: route().current('profile.show'), icon: 'fas fa-user'},
                    {title: 'Documentation', href: route('documentation'), icon: 'fas fa-book-open'},
                    {title: 'Log Out', href: route('documentation'), icon: 'fas fa-sign-out-alt'},
                    // { component: markRaw({
                    //     template: '<v-logout><jet-responsive-nav-link as="button">Log Out</jet-responsive-nav-link></v-logout>'
                    // })},
                    // {id: 'logout', title: 'Log Out'},
                ]
            }
        }
    }
</script>
