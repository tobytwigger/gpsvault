<template>
    <c-app-wrapper :title="schema.name">
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
