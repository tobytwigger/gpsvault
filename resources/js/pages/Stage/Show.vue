<template>
    <c-app-wrapper :title="tour.name ? tour.name : 'New Tour'" :action-sidebar="true">
        <v-tabs
            v-model="tab"
            centered
            grow
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>
            <v-tab href="#tab-summary">Summary<v-icon>mdi-information</v-icon></v-tab>
            <v-tab href="#tab-accommodation">Accommodation<v-icon>mdi-bed</v-icon></v-tab>
            <v-tab href="#tab-waypoints">Waypoints<v-icon>mdi-map-marker</v-icon></v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tab-summary">
                <v-row>
                    <v-col>
                        <v-row>
                            <v-col>
                                {{ stage.name }}
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                Stage {{ stage.stage_number }}
                            </v-col>
                            <v-col v-if="stageDate">
                                {{ stageDate.format('dddd, Do MMMM YYYY') }}
                            </v-col>
                        </v-row>
                        <v-row v-if="hasStats">
                            <v-col>
                                <c-activity-location-summary :started-at="humanStartedAt" :ended-at="humanEndedAt"></c-activity-location-summary>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="px-8 pt-8">
                                <div v-if="stage.description">
                                    {{ stage.description }}
                                </div>
                                <div v-else>
                                    No description
                                </div>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col>
                        <c-stats v-if="hasStats" :schema="statSchema" :limit="4"></c-stats>
                        <div v-else>No stats available</div>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col class="pa-8">
                        <c-route-map v-if="hasStats" :key="'map-' + stats.integration" :stats="stats"></c-route-map>
                    </v-col>
                </v-row>
            </v-tab-item>

            <v-tab-item value="tab-accommodation">
                Accommodation
            </v-tab-item>

            <v-tab-item value="tab-waypoints">
                Waypoints. Paginator of them all, can filter by type.

                Points of interest, cafes & lunch, dinner, shops, toilets, viewpoints, accommodation
            </v-tab-item>
        </v-tabs-items>

        <template #sidebar>
            <v-list>
                <v-list-item>
                    <c-stage-form :tour-id="stage.tour_id" :old-stage="stage" title="Edit stage" button-text="Update">
                        <template v-slot:activator="{trigger, showing}">
                            <v-btn :disabled="showing" @click="trigger">
                                Edit Stage
                            </v-btn>
                        </template>
                    </c-stage-form>
                </v-list-item>

                <v-list-item>
                    <c-delete-stage-button :stage="stage">
                        <template #button="{trigger, showing}">
                            <v-btn :disabled="showing" @click="trigger" :loading="showing" color="error">
                                Delete Stage
                            </v-btn>
                        </template>
                    </c-delete-stage-button>
                </v-list-item>

                <v-list-item v-if="stage.route_id">
                    <v-btn @click="$inertia.get(route('route.show', stage.route_id))">
                        View Route
                    </v-btn>
                </v-list-item>

                <v-list-item v-if="stage.activity_id">
                    <v-btn @click="$inertia.get(activity('activity.show', stage.activity_id))">
                        View Activity
                    </v-btn>
                </v-list-item>
            </v-list>
        </template>

    </c-app-wrapper>
</template>

<script>
import CStageForm from 'ui/components/Stage/CStageForm';
import CStageTable from 'ui/components/Stage/CStageTable';
import CPaginationIterator from 'ui/components/CPaginationIterator';
import CStageCard from 'ui/components/Stage/CStageCard';
import CTourMap from 'ui/components/Tour/CTourMap';
import CDeleteTourButton from 'ui/components/Tour/CDeleteTourButton';
import CTourForm from '../../ui/components/Tour/CTourForm';
import CActivityLocationSummary from '../../ui/components/CActivityLocationSummary';


import moment from 'moment';
import CStats from '../../ui/components/CStats';
import stats from '../../ui/mixins/stats';
import CAppWrapper from 'ui/layouts/CAppWrapper';
import CRouteMap from '../../ui/components/Route/CRouteMap';
import CDeleteStageButton from '../../ui/components/Stage/CDeleteStageButton';

export default {
    name: "Show",
    mixins: [stats],
    components: {
        CDeleteStageButton,
        CRouteMap,
        CActivityLocationSummary,
        CStats,
        CTourForm,
        CDeleteTourButton, CTourMap, CStageCard, CPaginationIterator, CStageTable, CStageForm, CAppWrapper},
    props: {
        tour: {
            required: true,
            type: Object
        },
        stage: {
            required: true,
            type: Object
        },
        routeModel: {
            required: false,
            type: Object
        }
    },
    data() {
        return {
            tab: null,
        }
    },
    computed: {
        allStats() {
            if(this.routeModel) {
                return this.routeModel.stats;
            }
            return [];
        },
        stageDate() {
            if(this.stage.date) {
                return moment(this.stage.date);
            }
            return null;
        }
    }
}
</script>

<style scoped>

</style>
