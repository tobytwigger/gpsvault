<template>
    <c-app-wrapper :title="schema.name" :menu-items="menuItems">
        <v-tabs
            :value="tab"
            centered
            grow
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>

            <v-tab
                v-for="(dashboard, i) in dashboards"
                :key="i"
                @click="$inertia.visit(route('dashboard.show', dashboard.id))"
            >
                {{ dashboard.name }}
            </v-tab>
        </v-tabs>

        <dashboard-show :schema="schema"></dashboard-show>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
import DashboardShow from '../../ui/components/Dashboard/DashboardShow';
export default {
    name: "Dashboard",
    components: {DashboardShow, CAppWrapper},
    props: {
        schema: {
            required: true,
            type: Object
        },
        dashboards: {
            required: true,
            type: Array
        }
    },
    data() {
        return {
            menuItems: [
                {
                    title: 'Refresh',
                    icon: 'mdi-autorenew',
                    action: () => {
                        this.$inertia.reload();
                    }
                },
                // { isDivider: true },
                // { title: "Menu Item 2",
                // action: () => {
                //     console.log('test')
                // }},
                // {
                //     title: "Sub 1",
                //     menu: [
                //         { title: "1.1" },
                //         { title: "1.2" },
                //         {
                //             title: "Sub-menu 2",
                //             menu: [
                //                 { title: "2.1" },
                //                 { title: "2.2" },
                //                 {
                //                     title: "Sub-menu 3",
                //                     menu: [
                //                         { title: "3.1" },
                //                         { title: "3.2" },
                //                         {
                //                             title: "Sub-menu 4",
                //                             menu: [{ title: "4.1" }, { title: "4.2" }, { title: "4.3" }]
                //                         }
                //                     ]
                //                 }
                //             ]
                //         }
                //     ]
                // },
                // { title: "Menu Item 3" },
                // { isDivider: true },
                // {
                //     title: "Menu Item 4",
                //     action: () => {
                //         console.log("menu-item-4");
                //     }
                // },
                // {
                //     title: "Menu Item 5",
                //     action: () => {
                //         console.log("menu-item-5");
                //     }
                // }
            ]
        }
    },
    computed: {
        tab() {
            let dashboard = this.dashboards.filter(d => d.id === this.schema.id);
            console.log(dashboard);
            if(dashboard.length > 0) {
                return this.dashboards.indexOf(dashboard[0]);
            }
            return null;
        }
    }
}
</script>

<style scoped>

</style>
