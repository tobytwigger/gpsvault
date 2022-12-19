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
                        <v-list flat>
                            <v-list-item v-if="routeModel.description">
                                <v-list-item-icon>
                                    <v-tooltip left>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-icon
                                                v-bind="attrs"
                                                v-on="on">
                                                mdi-text
                                            </v-icon>
                                        </template>
                                        <span>Route description</span>
                                    </v-tooltip>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title v-text="routeModel.description"></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="routeModel.notes">
                                <v-list-item-icon>
                                    <v-tooltip left>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-icon
                                                v-bind="attrs"
                                                v-on="on">
                                                mdi-information-outline
                                            </v-icon>
                                        </template>
                                        <span>Route notes</span>
                                    </v-tooltip>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title v-text="routeModel.notes"></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="routeModel.path?.human_started_at && routeModel.path?.human_ended_at">
                                <v-list-item-icon>
                                    <v-tooltip left>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-icon
                                                v-bind="attrs"
                                                v-on="on">
                                                mdi-map-marker
                                            </v-icon>
                                        </template>
                                        <span>Route start/end points</span>
                                    </v-tooltip>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <c-activity-location-summary :started-at="routeModel.path.human_started_at" :ended-at="routeModel.path.human_ended_at"></c-activity-location-summary>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                    <v-col>
                        <c-stats :schema="statsSchema" :selectable="false"></c-stats>
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
import CStats from '../../ui/components/CStats';
import units from '../../ui/mixins/units';

export default {
    name: "Show",
    mixins: [units],
    components: {
        CStats,
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
        }
    },
    computed: {
        statsSchema() {
            let schema = [];
            if(this.routeModel.path?.distance) {
                schema.push({
                    icon: 'mdi-ruler',
                    title: 'Distance',
                    label: 'distance',
                    disabled: true,
                    data: [
                        {value: this.convert(this.routeModel.path?.distance, 'distance'), label: 'total'},
                    ]
                });
            }
            if(this.routeModel.path?.duration) {
                schema.push({
                    icon: 'mdi-clock',
                    title: 'Time',
                    label: 'time',
                    pointLabel: 'time',
                    disabled: true,
                    data: [
                        {value: this.convert(this.routeModel.path?.duration, 'duration'), label: 'total'},
                    ]
                });
            }
            if(this.routeModel.path?.elevation_gain) {
                schema.push({
                    icon: 'mdi-image-filter-hdr',
                    title: 'Elevation',
                    label: 'elevation',
                    pointLabel: 'elevation',
                    disabled: true,
                    data: [
                        {value: this.convert(this.routeModel.path?.elevation_gain, 'elevation'), label: 'gain'},
                    ]
                })
            }
            return schema;
        },
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
                    title: 'Download original route file',
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
