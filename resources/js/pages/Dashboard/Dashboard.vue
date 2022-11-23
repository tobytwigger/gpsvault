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
                    name: 'Refresh',
                    icon: 'mdi-autorenew',
                    action: () => {
                        this.$inertia.reload();
                    }
                },
                // { isDivider: true },
                // { name: "Menu Item 2",
                // action: () => {
                //     console.log('test')
                // }},
                // {
                //     name: "Sub 1",
                //     menu: [
                //         { name: "1.1" },
                //         { name: "1.2" },
                //         {
                //             name: "Sub-menu 2",
                //             menu: [
                //                 { name: "2.1" },
                //                 { name: "2.2" },
                //                 {
                //                     name: "Sub-menu 3",
                //                     menu: [
                //                         { name: "3.1" },
                //                         { name: "3.2" },
                //                         {
                //                             name: "Sub-menu 4",
                //                             menu: [{ name: "4.1" }, { name: "4.2" }, { name: "4.3" }]
                //                         }
                //                     ]
                //                 }
                //             ]
                //         }
                //     ]
                // },
                // { name: "Menu Item 3" },
                // { isDivider: true },
                // {
                //     name: "Menu Item 4",
                //     action: () => {
                //         console.log("menu-item-4");
                //     }
                // },
                // {
                //     name: "Menu Item 5",
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
