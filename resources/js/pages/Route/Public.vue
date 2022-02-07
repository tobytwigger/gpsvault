<template>
    <c-public-wrapper :title="routeModel.name">
        <v-tabs
            v-model="tab"
            centered
            grow
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>

            <v-tab href="#tab-summary">
                Summary
                <v-icon>mdi-information</v-icon>
            </v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tab-summary">
                <v-row>
                    <v-col>
                        <v-row>
                            <v-col class="px-8 pt-8">
                                <div v-if="routeModel.description">
                                    {{ routeModel.description }}
                                </div>
                                <div v-else>
                                    No description
                                </div>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="px-8 pt-8">
                                <div v-if="routeModel.notes">
                                    {{ routeModel.notes }}
                                </div>
                                <div v-else>
                                    No notes
                                </div>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="px-8 pt-8">
                                <c-activity-location-summary v-if="hasStats" :started-at="humanStartedAt" :ended-at="humanEndedAt"></c-activity-location-summary>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col>
                        <c-stats v-if="this.stats" :schema="statSchema" :limit="4"></c-stats>
                        <div v-else>No stats available</div>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col class="pa-8">
                        <c-route-map v-if="hasStats" :key="'map-' + stats.integration" :stats="stats"></c-route-map>
                    </v-col>
                </v-row>
            </v-tab-item>
        </v-tabs-items>

        <template #sidebar>
            <v-list>
                <v-list-item>
                    <v-select
                        class="pt-2"
                        v-model="activeDataSource"
                        item-text="integration"
                        item-value="integration"
                        :items="allStats"
                        hint="Choose which data sets to show"
                        label="Data Source"
                        dense
                    ></v-select>
                </v-list-item>
            </v-list>
        </template>
    </c-public-wrapper>
</template>

<script>
import moment from 'moment';
import CAppWrapper from 'ui/layouts/CAppWrapper';
import CRouteFileFormDialog from 'ui/components/Route/CRouteFileFormDialog';
import CManageRouteMedia from 'ui/components/Route/CManageRouteMedia';
import stats from 'ui/mixins/stats';
import CStats from 'ui/components/CStats';
import CRouteMap from 'ui/components/Route/CRouteMap';
import CRouteForm from 'ui/components/Route/CRouteForm';
import CDeleteRouteButton from 'ui/components/Route/CDeleteRouteButton';
import CUploadRouteFileButton from 'ui/components/Route/CUploadRouteFileButton';
import CActivityLocationSummary from '../../ui/components/CActivityLocationSummary';
import CPublicWrapper from '../../ui/layouts/CPublicWrapper';

export default {
    name: "Show",
    components: {
        CPublicWrapper,
        CActivityLocationSummary,
        CUploadRouteFileButton,
        CDeleteRouteButton, CRouteForm, CRouteMap, CStats, CManageRouteMedia, CRouteFileFormDialog, CAppWrapper},
    props: {
        routeModel: {
            required: true,
            type: Object
        }
    },
    mixins: [stats],
    data() {
        return {
            tab: 'tab-summary'
        }
    },
    methods: {
        formatDateTime(dt) {
            return moment(dt).format('DD/MM/YYYY HH:mm:ss');
        }
    },
    computed: {
        allStats() {
            return this.routeModel.stats;
        },
        pageTitle() {
            return this.routeModel.name ?? 'New Route';
        }
    }
}
</script>

<style scoped>

</style>
