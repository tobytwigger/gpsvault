<template>
    <c-app-wrapper :title="activity.name" :action-sidebar="true">
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

            <v-tab href="#tab-analysis">
                Analysis
                <v-icon>mdi-chart-areaspline-variant</v-icon>
            </v-tab>

            <v-tab href="#tab-social">
                Social
                <v-icon>mdi-account-group</v-icon>
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
                                <div v-if="activity.description">
                                    {{ activity.description }}
                                </div>
                                <div v-else>
                                    No description
                                </div>
                            </v-col>
                        </v-row>
                        <v-row v-if="startedAt">
                            <v-col>
                                <v-chip class="ma-2">
                                    <v-icon>mdi-map-marker</v-icon>
                                    {{ startedAt.value }}
                                </v-chip>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="px-8">
                                <c-activity-location-summary v-if="hasStats" :started-at="humanStartedAt" :ended-at="humanEndedAt"></c-activity-location-summary>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col>
                        <c-stats v-if="hasStats" :schema="statSchema" :limit="4"></c-stats>
                        <div v-else>No stats available</div>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <c-image-gallery :images="images" :max-height="300"></c-image-gallery>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col class="pa-8">
                        <c-activity-map v-if="hasStats" :key="'map-' + stats.integration" :stats="stats"></c-activity-map>
                    </v-col>
                </v-row>
            </v-tab-item>
            <v-tab-item value="tab-analysis">
                <c-activity-analysis :activity="activity"></c-activity-analysis>
            </v-tab-item>
            <v-tab-item value="tab-social">
                <v-row>
                    <v-col v-if="hasKudos">
                        <v-list>
                            <v-subheader>Kudos
                                <v-badge :value="kudosCount" :content="kudosCount" inline></v-badge>
                            </v-subheader>

                            <v-list-item v-for="k in kudos" :key="k.id">
                                <v-list-item-content>
                                    <v-list-item-title>{{ k.name }}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
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
                <c-file-form-dialog :activity="activity" title="Upload a file" text="Upload a new file">
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
                </c-file-form-dialog>
                <c-manage-activity-media :activity="activity"></c-manage-activity-media>
            </v-tab-item>
        </v-tabs-items>

        <template #sidebar>
            <v-list>
                <v-list-item>
                    <c-delete-activity-button :activity="activity"></c-delete-activity-button>
                </v-list-item>
                <v-list-item v-if="!activity.file_id">
                    <c-upload-activity-file-button :activity="activity"></c-upload-activity-file-button>
                </v-list-item>
                <v-list-item v-if="activity.file_id">
                    <v-btn link :href="route('file.download', activity.file_id)">
                        Download activity file
                    </v-btn>
                </v-list-item>
                <v-list-item v-if="activity.file_id">
                    <v-btn link :href="route('activity.download', activity.id)">
                        Download activity
                    </v-btn>
                </v-list-item>
                <v-list-item>
                    <c-activity-form :old-activity="activity" title="Edit activity" button-text="Update">
                        <template v-slot:activator="{trigger,showing}">
                            <v-btn :disabled="showing" @click="trigger">
                                Edit Activity
                            </v-btn>
                        </template>
                    </c-activity-form>
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
                <v-list-item>
                    <a :href="'https://www.strava.com/activities/' + activity.additional_data.strava_id"
                       v-if="activity.linked_to.indexOf('strava') !== -1"
                    >View on strava</a>
                </v-list-item>
            </v-list>
        </template>
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

export default {
    name: "Show",
    components: {
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
            return this.activity.stats;
        },
        images() {
            return this.activity.files.filter(file => file.mimetype.startsWith('image/'))
                .map(file => {
                    return {
                        alt: file.caption,
                        src: route('file.preview', file.id)
                    }
                });
        }
    }
}
</script>

<style scoped>

</style>
