<template>
    <c-app-wrapper title="Strava" :menu-items="menuItems">
        <v-tabs
            v-model="tab"
            centered
            grow
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>
            <v-tab href="#tab-connection">Connection Health
                <v-icon>mdi-heart-pulse</v-icon>
            </v-tab>
            <v-tab href="#tab-clients" v-if="canManageClients">Strava Clients
                <v-icon>mdi-connection</v-icon>
            </v-tab>
            <v-tab href="#tab-sync">Sync
                <v-icon>mdi-autorenew</v-icon>
            </v-tab>
            <v-tab href="#tab-import">Imports
                <v-icon>mdi-import</v-icon>
            </v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tab-connection">
                <v-container class="fill-height" fluid>
                    <v-row
                        align="center"
                        class="fill-height"
                        justify="center">
                        <v-col
                            cols="12"
                            md="4"
                            sm="8">
                            <c-strava-connection-health :client-has-space="clientHasSpace" :client-is-available="isAvailable"
                                :is-connected="isConnected" :default-client="defaultClient">
                            </c-strava-connection-health>
                        </v-col>
                    </v-row>
                </v-container>
            </v-tab-item>

            <v-tab-item value="tab-clients">
                <c-strava-client-list :clients="clients"></c-strava-client-list>
            </v-tab-item>

            <v-tab-item value="tab-sync">
                <v-row v-if="sync.unlinked_activities.length > 0">
                    <v-col>
                        <v-alert
                            border="top"
                            colored-border
                            type="info"
                            elevation="2"
                        >
                            There are {{ sync.unlinked_activities.length }} activities that are not yet linked to
                            Strava.

                            <v-expansion-panels>
                                <v-expansion-panel>
                                    <v-expansion-panel-header>
                                        <v-progress-linear
                                            :value="activitiesLinkedPercentage"
                                            height="20"
                                            color="orange lighten-2"
                                        >
                                            <span>{{ Math.floor(activitiesLinkedPercentage) }}%</span>
                                            <span>  </span>
                                            <span>({{ sync.activities_linked }}/{{ sync.total_activities }})</span>
                                        </v-progress-linear>

                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <c-link-activity :activities="sync.unlinked_activities">

                                        </c-link-activity>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </v-alert>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <v-list
                            subheader
                            two-line
                        >
                            <v-subheader>
                                <span v-if="syncIsOngoing">
                                    Loading from Strava...
                                </span>
                                <span v-else-if="isConnected">
                                    Start a sync from the menu
                                </span>
                                <span v-else>
                                    Please connect your Strava account before syncing
                                </span>
                                <v-spacer></v-spacer>
                                <v-btn @click="reload" icon :loading="isReloading">
                                    <v-icon>mdi-autorenew</v-icon>
                                </v-btn>
                            </v-subheader>

                            <c-sync-information-item title="Activities" :activities="sync.activities"
                                                     description="Checks for updates to basic information about your activity."
                                                     icon="mdi-bike"></c-sync-information-item>
                            <c-sync-information-item title="Stats" :activities="sync.stats"
                                                     description="Checks for updates to your data, such as speed and heart rate."
                                                     icon="mdi-chart-line"></c-sync-information-item>
                            <c-sync-information-item title="Photos" :activities="sync.photos"
                                                     description="CHecks for any new photos."
                                                     icon="mdi-camera"></c-sync-information-item>
                            <c-sync-information-item title="Kudos" :activities="sync.kudos"
                                                     description="Checks for any new Kudos."
                                                     icon="mdi-thumb-up-outline"></c-sync-information-item>
                            <c-sync-information-item title="Comments" :activities="sync.comments"
                                                     description="Checks for any new comments."
                                                     icon="mdi-comment-text"></c-sync-information-item>
                        </v-list>
                    </v-col>
                </v-row>
            </v-tab-item>

            <v-tab-item value="tab-import">
                <v-alert
                    outlined
                    type="warning"
                    prominent
                    border="left"
                    v-if="syncIsOngoing"
                >
                    We're taking a backup of your Strava account. We recommend importing once the sync is complete.
                </v-alert>

                <v-dialog>
                    <template v-slot:activator="{on, attrs}">
                        <v-alert
                            outlined
                            type="info"
                            prominent
                            border="left"
                            v-if="!syncIsOngoing && activity_files_needing_import.length > 0 && photos_needing_import.length > 0"
                        >
                            You should upload a Strava export shortly, we're missing data which Strava contains.

                            <v-btn text v-on="on" v-bind="attrs">Find out more</v-btn>
                        </v-alert>
                    </template>

                    <v-expansion-panels>
                        <v-expansion-panel v-if="activity_files_needing_import.length > 0">
                            <v-expansion-panel-header>
                                Missing activity files ({{ activity_files_needing_import.length }}) <small>The .gpx file is missing for some activities</small>
                            </v-expansion-panel-header>
                            <v-expansion-panel-content>
                                <ul>
                                    <li v-for="i in activity_files_needing_import">
                                        <a :href="route('activity.show', i.id)">{{i.name}}</a>
                                    </li>
                                </ul>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                        <v-expansion-panel v-if="photos_needing_import.length > 0">
                            <v-expansion-panel-header>
                                Missing photos ({{ photos_needing_import.length }}) <small>Expected photos are missing for some activities</small>
                            </v-expansion-panel-header>
                            <v-expansion-panel-content>
                                <ul>
                                    <li v-for="i in photos_needing_import">
                                        <v-tooltip>
                                            <template v-slot:activator="{on, attrs}">
                                                <a :href="route('activity.show', i.id)"><span v-on="on" v-bind="attrs">{{i.name}}</span></a>
                                            </template>
                                        </v-tooltip>
                                        <ul>
                                            <li v-for="photo in i.photos" :key="i.id">
                                                {{photo}}
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-dialog>

                <v-list dense>
                    <c-import-result :import-model="importModel" v-for="(importModel, index) in imports"
                                     :key="importModel.id" :index="imports.length - index">
                    </c-import-result>
                </v-list>

            </v-tab-item>
        </v-tabs-items>


        <c-strava-client-form title="Add new client" button-text="Create" v-model="showStravaClientForm"></c-strava-client-form>
        <c-strava-import-form v-model="showStravaImportForm"></c-strava-import-form>


    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
import CStravaClientForm from '../../../ui/components/Strava/CStravaClientForm';
import CStravaClientList from '../../../ui/components/Strava/CStravaClientList';
import CSyncInformationItem from '../../../ui/components/Strava/Sync/CSyncInformationItem';
import CLinkActivity from '../../../ui/components/Strava/Sync/CLinkActivity';
import CStravaImportForm from '../../../ui/components/Strava/Import/CStravaImportForm';
import CImportResult from '../../../ui/components/Strava/Import/CImportResult';
import CStravaConnectionHealth from '../../../ui/components/Strava/CStravaConnectionHealth';

export default {
    name: "Index",
    components: {
        CStravaConnectionHealth,
        CImportResult,
        CStravaImportForm,
        CLinkActivity, CSyncInformationItem, CStravaClientList, CStravaClientForm, CAppWrapper
    },
    props: {
        clients: {
            required: true,
            type: Object
        },
        sync: {
            required: true,
            type: Object
        },
        imports: {
            required: true,
            type: Array
        },
        activity_files_needing_import: {
            required: true,
            type: Array
        },
        photos_needing_import: {
            required: true,
            type: Array
        },
        isAvailable: {
            required: true,
            type: Boolean
        },
        isConnected: {
            required: true,
            type: Boolean
        },
        clientHasSpace: {
            required: true,
            type: Boolean
        },
        defaultClient: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            showStravaImportForm: false,
            showStravaClientForm: false,
            tab: null,
            isReloading: false
        }
    },

    methods: {
        reload() {
            this.$inertia.reload({
                onStart: () => this.isReloading = true,
                onFinish: () => this.isReloading = false
            });
        }
    },

    computed: {
        syncIsOngoing() {
            return this.sync.activities.length > 0
                || this.sync.stats.length > 0
                || this.sync.photos.length > 0
                || this.sync.kudos.length > 0
                || this.sync.comments.length > 0
        },
        activitiesLinkedPercentage() {
            return (this.sync.activities_linked / (this.sync.total_activities === 0 ? 1 : this.sync.total_activities)) * 100;
        },
        canManageClients() {
            return this.$page.props.permissions.indexOf('manage-strava-clients') > -1
                || this.$page.props.permissions.indexOf('superadmin') > -1;
        },
        menuItems() {
            return [
                {
                    title: 'Add a new client',
                    icon: 'mdi-plus',
                    action: () => {
                        this.showStravaClientForm = true;
                    }
                },
                {
                    title: 'Get help with clients',
                    icon: 'mdi-help',
                    href: route('larecipe.show', {version: '1.0', page: 'strava/logging-in-clients'}),
                    hrefTarget: '_blank',
                    useInertia: false
                },
                {isDivider: true},
                {
                    title: 'Sync Strava',
                    disabled: !this.isConnected,
                    icon: 'mdi-autorenew',
                    action: () => {
                        this.$inertia.post(route('strava.sync'))
                    }
                },
                {isDivider: true},
                {
                    title: 'Import Strava download',
                    icon: 'mdi-upload',
                    action: () => {
                        this.showStravaImportForm = true;
                    }
                },
            ];
        }
    }
}
</script>

<style scoped>

</style>
