<template>
    <c-app-wrapper :title="pageTitle" :menu-items="menuItems">
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

            <v-tab href="#tab-timeline">
                Timeline
                <v-icon>mdi-timeline-text</v-icon>
            </v-tab>

            <v-tab href="#tab-files">
                Files
                <v-icon>mdi-file-document-multiple</v-icon>
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
<!--                                TODO-->
<!--                                <c-activity-location-summary v-if="hasStats" :started-at="humanStartedAt" :ended-at="humanEndedAt"></c-activity-location-summary>-->
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col>
                        <span v-if="routeModel.distance">Distance: {{routeModel.distance}}m.</span>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col class="pa-8">
                        <c-route-map :places="places" v-if="routePath" :geojson="routePath"></c-route-map>
                    </v-col>
                </v-row>
            </v-tab-item>
            <v-tab-item value="tab-timeline">
                <c-route-timeline :route-model="routeModel"></c-route-timeline>
            </v-tab-item>
            <v-tab-item value="tab-files">
                <c-manage-route-media :route-model="routeModel"></c-manage-route-media>
            </v-tab-item>

        </v-tabs-items>

        <c-delete-route-button :route-model="routeModel" v-model="showingRouteDeleteForm"></c-delete-route-button>
        <c-route-form :old-route="routeModel" title="Edit route" button-text="Update" v-model="showingRouteEditForm"></c-route-form>
        <c-route-file-form-dialog :route-model="routeModel" title="Upload a file" text="Upload a new file" v-model="showingRouteUploadFileForm">
        </c-route-file-form-dialog>
    </c-app-wrapper>
</template>

<script>
import moment from 'moment';
import CAppWrapper from 'ui/layouts/CAppWrapper';
import CRouteFileFormDialog from 'ui/components/Route/CRouteFileFormDialog';
import CManageRouteMedia from 'ui/components/Route/CManageRouteMedia';
import CRouteMap from 'ui/components/Route/CRouteMap';
import CRouteForm from 'ui/components/Route/CRouteForm';
import CDeleteRouteButton from 'ui/components/Route/CDeleteRouteButton';
import CUploadRouteFileButton from 'ui/components/Route/CUploadRouteFileButton';
import CActivityLocationSummary from '../../ui/components/CActivityLocationSummary';
import CPaginationIterator from '../../ui/reusables/table/CPaginationIterator';
import CPlaceCard from '../../ui/components/Place/CPlaceCard';
import CPlaceSearch from '../../ui/components/Place/CPlaceSearch';
import CRouteTimeline from '../../ui/components/Route/CRouteTimeline';

export default {
    name: "Show",
    components: {
        CRouteTimeline,
        CPlaceSearch,
        CPlaceCard,
        CPaginationIterator,
        CActivityLocationSummary,
        CUploadRouteFileButton,
        CDeleteRouteButton, CRouteForm, CRouteMap, CManageRouteMedia, CRouteFileFormDialog, CAppWrapper},
    props: {
        routeModel: {
            required: true,
            type: Object
        },
    },
    data() {
        return {
            tab: 'tab-summary',
            showingRouteEditForm: false,
            showingRouteDeleteForm: false,
            showingRouteUploadFileForm: false
        }
    },
    methods: {
        formatDateTime(dt) {
            return moment(dt).format('DD/MM/YYYY HH:mm:ss');
        },
        addToRoute(place) {
            this.$inertia.post(route('route.place.store', this.routeModel.id), {
                place_id: place.id
            }, {
                onSuccess: (page) => this.$refs.placeSearch.loadPlaces()
            });
        },
        removeFromRoute(place) {
            this.$inertia.delete(route('route.place.destroy', [this.routeModel.id, place.id]), {
                onSuccess: (page) => this.$refs.placeSearch.loadPlaces()
            });
        }
    },
    computed: {
        places() {
            if(this.routeModel.path) {
                return this.routeModel.path.waypoints.filter(w => w.place_id !== null)
                    .map(w => w.place)
            }
            return [];
        },
        pageTitle() {
            return this.routeModel?.name ?? 'New Route';
        },
        routePath() {
            if(this.routeModel.path) {
                return {
                    type: 'LineString',
                    coordinates: this.routeModel.path.linestring.map(c => c.coordinates)
                }
            }
            return null;
        },
        menuItems() {
            return [
                {
                    title: 'Edit route',
                    icon: 'mdi-pencil',
                    href: route('planner.edit', this.routeModel.id),
                    useInertia: false,
                },
                {
                    title: 'Edit route details',
                    icon: 'mdi-pencil',
                    action: () => {
                        this.showingRouteEditForm = true;
                    }
                },
                {
                    title: 'Delete route',
                    icon: 'mdi-delete',
                    action: () => {
                        this.showingRouteDeleteForm = true;
                    }
                },
                {isDivider: true},
                {
                    title: 'Download route backup',
                    icon: 'mdi-download',
                    href: route('route.download', this.routeModel.id),
                    useInertia: false,
                },
                {
                    title: 'Download route file',
                    icon: 'mdi-download',
                    disabled: this.routeModel.file_id === null,
                    href: this.routeModel.file_id ? route('file.download', this.routeModel.file_id) : '#',
                    useInertia: false
                },
                {isDivider: true},
                {
                    title: 'Upload a media file',
                    icon: 'mdi-upload',
                    action: () => this.showingRouteUploadFileForm = true
                }
            ];
        }
    }
}
</script>

<style scoped>

</style>
