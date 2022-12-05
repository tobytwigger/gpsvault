<template>
    <c-app-wrapper :title="dashboardTitle" :menu-items="menuItems">
        <v-tabs
            v-if="dashboards.length > 0"
            vertical
            v-model="selectedDashboardIndex"
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>

            <v-tab
                v-for="(dashboard, i) in dashboards"
                :key="i"
            >
                {{ dashboard.name }}
            </v-tab>

            <v-tab-item
                v-for="(dashboard, i) in dashboards"
                :key="i">
                <dashboard-show :schema="dashboard"></dashboard-show>
            </v-tab-item>


        </v-tabs>
        <div v-else>
            No dashboards available
        </div>

    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
import DashboardShow from '../../ui/components/Dashboard/DashboardShow';
export default {
    name: "Dashboard",
    components: {DashboardShow, CAppWrapper},
    props: {
        initialSelection: {
            required: true,
            type: Number
        },
        dashboards: {
            required: true,
            type: Array
        }
    },
    mounted() {
        let dashboard = this.getDashboardWithId(this.initialSelection) ?? null;
        if(dashboard !== null) {
            this.selectedDashboardIndex = this.dashboards.indexOf(dashboard);
        }
    },
    data() {
        return {
            selectedDashboardIndex: null,
            menuItems: [
                {
                    title: 'Refresh',
                    icon: 'mdi-autorenew',
                    action: () => {
                        this.$inertia.reload();
                    }
                },
            ]
        }
    },
    methods: {
        getDashboardWithId(id) {
            return this.dashboards.find(d => d.id === id);
        }
    },
    computed: {
        dashboardTitle() {
            return this.getDashboardWithId(this.selectedDashboardIndex)?.name ?? 'No dashboards found';
        },
    }
}
</script>

<style scoped>

</style>
