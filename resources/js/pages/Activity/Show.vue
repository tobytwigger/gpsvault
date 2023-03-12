<template>
    <c-app-wrapper :title="activity.name" :menu-items="menuItems">
        <v-tabs
            v-model="tab"
            centered
            grow
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>

            <v-tab href="#tab-summary" data-hint="Shows general information about the activity.">
                Summary
                <v-icon>mdi-information</v-icon>
            </v-tab>

            <v-tab href="#tab-analysis" data-hint="Dive into the data from your ride.">
                Analysis
                <v-icon>mdi-chart-areaspline-variant</v-icon>
            </v-tab>

            <v-tab href="#tab-social" data-hint="See how people interacted with your ride.">
                Social
                <v-icon>mdi-account-group</v-icon>
            </v-tab>

            <v-tab href="#tab-files" data-hint="Upload any photos, documents or other files associated with the ride.">
                Files
                <v-icon>mdi-file-document-multiple</v-icon>
            </v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tab-summary">
                <c-job-status job="load-strava-activity" :tags="{activity_id: activity.id}">
                    <template v-slot:incomplete>
                        <v-progress-circular
                            indeterminate
                            color="primary"
                        ></v-progress-circular>
                    </template>
                </c-job-status>

                <v-row v-if="activity.stats.length === 0">
                    <v-col v-if="jobStatus === null || jobStatus.status === 'succeeded'">
                        <span v-if="activity.file_id">
                            Activity analysis incomplete. Please contact support at support@gpsvault.co.uk.
                        </span>
                        <span v-else>
                            No path data available. Please upload the recording of your activity.
                        </span>
                    </v-col>
                    <v-col v-else>
                        <c-loading-from-job-status title="Analysing activity" :job-status="jobStatus">

                        </c-loading-from-job-status>
                    </v-col>
                </v-row>
                <v-row v-else>
                    <v-col :sm="12" :md="6">

                        <v-list flat>
                            <v-list-item v-if="activity.description">
                                <v-list-item-icon>
                                    <v-tooltip left>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-icon
                                                v-bind="attrs"
                                                v-on="on">
                                                mdi-text
                                            </v-icon>
                                        </template>
                                        <span>Activity description</span>
                                    </v-tooltip>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title v-text="activity.description"></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="startedAt">
                                <v-list-item-icon>
                                    <v-tooltip left>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-icon
                                                v-bind="attrs"
                                                v-on="on">
                                                mdi-calendar-clock
                                            </v-icon>
                                        </template>
                                        <span>Started at</span>
                                    </v-tooltip>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title v-text="startedAt.value"></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="humanStartedAt && humanEndedAt">
                                <v-list-item-icon>
                                    <v-tooltip left>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-icon
                                                v-bind="attrs"
                                                v-on="on">
                                                mdi-map-marker
                                            </v-icon>
                                        </template>
                                        <span>Activity start/end points</span>
                                    </v-tooltip>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <c-activity-location-summary v-if="hasStats" :started-at="humanStartedAt" :ended-at="humanEndedAt"></c-activity-location-summary>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                    <v-col>
                        <c-stats v-if="hasStats" :schema="statSchema" :limit="4"></c-stats>
                        <div v-else>
                            <c-stats-loading>

                            </c-stats-loading>
                        </div>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col class="pa-8">
                        <c-job-status job="load-strava-stats" :tags="{activity_id: activity.id}">
                            <template v-slot:incomplete>
                                <v-progress-circular
                                    indeterminate
                                    color="primary"
                                ></v-progress-circular>
                            </template>
                        </c-job-status>
<!--                        <c-map v-if="hasStats && stats.linestring" :geojson="stats.linestring" :key="'map-' + stats.integration"></c-map>-->
                        <v-container>
                            <c-activity-map v-if="hasStats" :key="'map-' + stats.integration" :stats="stats"></c-activity-map>
                        </v-container>
                    </v-col>
                </v-row>
            </v-tab-item>
            <v-tab-item value="tab-analysis">
                <c-activity-analysis :activity="activity"></c-activity-analysis>
            </v-tab-item>
            <v-tab-item value="tab-social">
                <v-row>
                    <c-job-status job="load-strava-kudos" :tags="{activity_id: activity.id}">
                        <template v-slot:incomplete>
                            <v-progress-circular
                                indeterminate
                                color="primary"
                            ></v-progress-circular>
                        </template>
                    </c-job-status>

                    <v-col v-if="hasKudos">
                        <v-list>
                            <v-subheader>Kudos
                                <v-badge :value="kudosCount" :content="kudosCount" inline></v-badge>
                            </v-subheader>

                            <v-list-item v-for="k in kudos" :key="k.id">
                                <v-list-item-icon><v-icon>mdi-thumb-up</v-icon></v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>{{ k.name }}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                    <c-job-status job="load-strava-comments" :tags="{activity_id: activity.id}">
                        <template v-slot:incomplete>
                            <v-progress-circular
                                indeterminate
                                color="primary"
                            ></v-progress-circular>
                        </template>
                    </c-job-status>

                    <v-col>
                        <v-list two-line v-if="hasComments">
                            <v-subheader>Comments
                                <v-badge :value="commentsCount" :content="commentsCount" inline></v-badge>
                            </v-subheader>

                            <template v-for="comment in comments">
                                <v-list-item
                                    :key="comment.id"
                                >
                                    <v-list-item-content>
                                        <v-list-item-title>{{ comment.name }}</v-list-item-title>
                                        <v-list-item-subtitle>
                                            <span class="text--primary">{{formatDateTime(comment.posted_at)}}</span>
                                            &mdash; {{comment.text}} </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </template>
                        </v-list>
                    </v-col>
                </v-row>
            </v-tab-item>
            <v-tab-item value="tab-files">
                <c-job-status job="load-strava-photos" :tags="{activity_id: activity.id}">
                    <template v-slot:incomplete>
                        <v-progress-circular
                            indeterminate
                            color="primary"
                        ></v-progress-circular>
                    </template>
                </c-job-status>
                <c-manage-activity-media :activity="activity"></c-manage-activity-media>
            </v-tab-item>
        </v-tabs-items>

        <c-file-form-dialog :activity="activity" title="Upload a file" text="Upload a new file" v-model="showingUploadFileForm"></c-file-form-dialog>
        <c-delete-activity-button :activity="activity" v-model="showingActivityDeleteForm"></c-delete-activity-button>
        <c-upload-activity-file-button v-model="showingActivityUploadForm" :activity="activity"></c-upload-activity-file-button>
        <c-activity-form :old-activity="activity" title="Edit activity" button-text="Update" data-hint="Edit this activity." v-model="showingActivityEditForm"></c-activity-form>
        <c-link-strava-activity-form v-model="showingLinkStravaForm" :activity="activity"></c-link-strava-activity-form>

    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
import CDeleteActivityButton from 'ui/components/Activity/CDeleteActivityButton';
import CUploadActivityFileButton from 'ui/components/Activity/CUploadActivityFileButton';
import CImageGallery from 'ui/components/CImageGallery';
import CFileFormDialog from 'ui/components/Activity/CFileFormDialog';
import stats from 'ui/mixins/stats';
import CActivityForm from 'ui/components/Activity/CActivityForm';
import CLineGraph from 'ui/components/CLineGraph';
import CActivityAnalysis from 'ui/components/Activity/CActivityAnalysis';
import strava from 'ui/mixins/strava';
import moment from 'moment';
import CManageActivityMedia from 'ui/components/Activity/CManageActivityMedia';
import CStats from 'ui/components/CStats';
import CActivityMap from 'ui/components/Activity/CActivityMap';
import CActivityLocationSummary from '../../ui/components/CActivityLocationSummary';
import CJobStatus from '../../ui/components/CJobStatus';
import CMap from '../../ui/components/Map/CMap';
import CLinkStravaActivityForm from '../../ui/components/Strava/Sync/CLinkStravaActivityForm';
import CStatsLoading from '../../ui/components/Activity/CStatsLoading';
import CLoadingFromJobStatus from '../../ui/components/Page/CLoadingFromJobStatus.vue';
import {client} from '@tobytwigger/laravel-job-status-js';

export default {
    name: "Show",
    components: {
        CLoadingFromJobStatus,
        CStatsLoading,
        CLinkStravaActivityForm,
        CMap,
        CJobStatus,
        CActivityLocationSummary,
        CActivityMap,
        CStats,
        CManageActivityMedia,
        CActivityAnalysis,
        CLineGraph,
        CActivityForm,
        CFileFormDialog,
        CImageGallery, CUploadActivityFileButton, CAppWrapper,CDeleteActivityButton
    },
    mixins: [stats, strava],
    props: {
        activity: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            tab: 'tab-summary',
            loadingStravaSync: false,
            showingActivityDeleteForm: false,
            showingActivityUploadForm: false,
            showingActivityEditForm: false,
            showingLinkStravaForm: false,
            showingUploadFileForm: false,
            jobStatus: null,
            listener: null
        }
    },
    mounted() {
        this.setupListeners();
    },
    beforeDestroy() {
        this.removeListeners();
    },
    watch: {
        jobStatus: {
            deep: true,
            handler: function(newVal, oldVal) {
                if(newVal !== null && oldVal !== null && newVal?.status !== oldVal?.status) {
                    this.$inertia.reload();
                }
            }
        }
    },
    methods: {
        setupListeners() {
            this.removeListeners();
            this.listener = client.runs.search()
                .whereAlias('analyse-activity-file')
                .whereTag('activity_id', this.activity.id)
                .listen()
                .onUpdated((runs) => {
                    this.jobStatus = runs.total > 0 ? runs.data[0] : null;
                })
                .start()
        },
        removeListeners() {
            if(this.listener) {
                this.listener.stop();
                this.listener = null;
            }
        },
        refreshStravaActivity() {
            this.loadingStravaSync = true;
            axios.post(route('strava.activity.sync', this.activity.id))
                .then(response => {
                    this.loadingChartData = false;
                    this.rawChartData = response.data
                })
                .then(() => {
                    this.loadingChartData = false;
                    this.loadingStravaSync = false;
                });
        },
        formatDateTime(dt) {
            return moment(dt).format('DD/MM/YYYY HH:mm:ss');
        }
    },
    computed: {
        allStats() {
            return this.activity.stats;
        },
        menuItems() {
            let createDataSourceMenuItem = (integration, title) => {
                return {
                    title: title,
                    icon: this.activeDataSource === integration ? 'mdi-checkbox-marked-circle-outline' : 'mdi-checkbox-blank-circle-outline',
                    disabled: this.activity.stats.filter(s => s.integration === integration).length === 0,
                    action: () => {
                        this.activeDataSource = integration;
                    }
                };
            }

            return [
                {
                    title: 'Edit activity',
                    icon: 'mdi-pencil',
                    action: () => {
                        this.showingActivityEditForm = true;
                    }
                },
                {
                    title: 'Delete activity',
                    icon: 'mdi-delete',
                    action: () => {
                        this.showingActivityDeleteForm = true;
                    }
                },
                {isDivider: true},
                {
                    title: 'Download activity backup',
                    icon: 'mdi-download',
                    href: route('activity.download', this.activity.id),
                    useInertia: false,
                },
                {isDivider: true},
                {
                    title: 'Activity File',
                    icon: 'mdi-file',
                    menu: [
                        {
                            title: 'Upload activity file',
                            icon: 'mdi-upload',
                            disabled: this.activity.file_id !== null,
                            action: () => {
                                this.showingActivityUploadForm = true;
                            }
                        },
                        {
                            title: 'Download activity file',
                            icon: 'mdi-download',
                            disabled: this.activity.file_id === null,
                            href: this.activity.file_id ? route('file.download', this.activity.file_id) : '#',
                            useInertia: false
                        },
                    ]
                },
                {isDivider: true},
                {
                    title: 'Data Source',
                    icon: 'mdi-database',
                    menu: [
                        createDataSourceMenuItem('php', 'GPS Vault'),
                        createDataSourceMenuItem('strava', 'Strava'),
                    ],
                },
                {isDivider: true},
                {
                    title: 'Strava',
                    icon: 'mdi-power-plug',
                    menu: [
                        {
                            title: 'Update from Strava',
                            icon: 'mdi-power-plug',
                            disabled: this.loadingStravaSync,
                            action: () => {
                                this.refreshStravaActivity()
                            }
                        },
                        {
                            title: 'View Strava',
                            icon: 'mdi-open-in-new',
                            disabled: this.activity.additional_data?.strava_id === null,
                            href: 'https://www.strava.com/activities/' + this.activity.additional_data?.strava_id,
                            useInertia: false,
                            hrefTarget: '_blank',
                        },
                        {
                            title: 'Edit link to Strava',
                            icon: 'mdi-power-plug',
                            action: () => {
                                this.showingLinkStravaForm = true;
                            }
                        }
                    ]
                },
                {isDivider: true},
                {
                    title: 'Upload media file',
                    icon: 'mdi-camera-plus',
                    action: () => {
                        this.showingUploadFileForm = true;
                    }
                },
            ];
        },
    }
}
</script>

<style scoped>

</style>
