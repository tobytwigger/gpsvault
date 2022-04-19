<template>
    <c-app-wrapper :title="pageTitle" :action-sidebar="true">
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

            <v-tab href="#tab-files">
                Files
                <v-icon>mdi-file-document-multiple</v-icon>
            </v-tab>

            <v-tab href="#tab-waypoints">
                Waypoints
                <v-icon>mdi-map-marker</v-icon>
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
            <v-tab-item value="tab-files">
                <c-route-file-form-dialog :route-model="routeModel" title="Upload a file" text="Upload a new file">
                    <template v-slot:activator="{trigger,showing}">
                        <v-btn
                            color="secondary"
                            @click.stop="trigger"
                            :disabled="showing"
                        >
                            <v-icon>mdi-upload</v-icon>
                            Upload file
                        </v-btn>
                    </template>
                </c-route-file-form-dialog>
                <c-manage-route-media :route-model="routeModel"></c-manage-route-media>
            </v-tab-item>

            <v-tab-item value="tab-waypoints">

                <v-row
                    align="center"
                    justify="center">
                    <v-col>
                        <v-progress-circular
                            v-if="loading"
                            :size="70"
                            :width="7"
                            color="primary"
                            indeterminate
                        ></v-progress-circular>

                        <div v-else-if="places === null">No places found</div>

                        <c-pagination-iterator v-else :paginator="places" item-key="id">
                            <template v-slot:default="{item}">
                                <c-place-card :place="item"></c-place-card>
                            </template>
                        </c-pagination-iterator>
                    </v-col>
                </v-row>
            </v-tab-item>

        </v-tabs-items>

        <template #sidebar>
            <v-list>
                <v-list-item>
                    <c-delete-route-button :route-model="routeModel"></c-delete-route-button>
                </v-list-item>
                <v-list-item v-if="!routeModel.file_id">
                    <c-upload-route-file-button :route-model="routeModel"></c-upload-route-file-button>
                </v-list-item>
                <v-list-item v-if="routeModel.file_id">
                    <v-btn link :href="route('file.download', routeModel.file_id)">
                        Download route file
                    </v-btn>
                </v-list-item>
                <v-list-item v-if="routeModel.file_id">
                    <v-btn link :href="route('route.download', routeModel.id)">
                        Download route
                    </v-btn>
                </v-list-item>
                <v-list-item>
                    <c-route-form :old-route="routeModel" title="Edit route" button-text="Update">
                        <template v-slot:activator="{trigger,showing}">
                            <v-btn :disabled="showing" @click="trigger">
                                Edit Route
                            </v-btn>
                        </template>
                    </c-route-form>
                </v-list-item>
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
    </c-app-wrapper>
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
import CPaginationIterator from '../../ui/components/CPaginationIterator';
import CPlaceCard from '../../ui/components/Place/CPlaceCard';

export default {
    name: "Show",
    components: {
        CPlaceCard,
        CPaginationIterator,
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
            tab: 'tab-summary',
            loading: false,
            places: null
        }
    },
    mounted() {
        this.loadPlaces();
    },
    methods: {
        formatDateTime(dt) {
            return moment(dt).format('DD/MM/YYYY HH:mm:ss');
        },
        loadPlaces() {
            this.loading = true;
            axios.get(route('place.search'))
                .then(response => this.places = response.data)
                .then(() => this.loading = false);
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
