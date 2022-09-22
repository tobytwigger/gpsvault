<template>
    <c-app-wrapper title="Strava" :action-sidebar="true">
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
            <v-tab href="#tab-sync">Sync
                <v-icon>mdi-autorenew</v-icon>
            </v-tab>
            <v-tab href="#tab-import">Imports
                <v-icon>mdi-import</v-icon>
            </v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tab-connection">
                <div v-if="!canManageClients">
                    <div v-if="!isConnected && clientIsAvailable">
                        <v-alert
                            outlined
                            type="warning"
                            prominent
                            border="left"
                        >
                            Not connected
                        </v-alert>
                        <v-btn :href="stravaLoginUrl" v-if="stravaLoginUrl">
                            <v-img src="/dist/images/strava_logo.svg" alt="Connect to Strava"/>
                        </v-btn>
                    </div>
                    <div v-else-if="!isConnected && !clientIsAvailable">
                        <v-alert
                            outlined
                            type="warning"
                            prominent
                            border="left"
                        >
                            No client is available
                        </v-alert>
                    </div>
                    <div v-else-if="isConnected">
                        <v-alert
                            outlined
                            type="success"
                            prominent
                            border="left"
                            v-if="clientHasSpace"
                        >
                            Connected
                        </v-alert>
                        <v-alert
                            outlined
                            type="warning"
                            prominent
                            border="left"
                            v-else
                        >
                            Available shortly, rate limits met
                        </v-alert>
                        <v-btn @click="logoutClient">Logout</v-btn>
                    </div>
                </div>
                <div v-else>
                    <div v-if="!isConnected && clientIsAvailable">
                        <v-alert
                            outlined
                            type="warning"
                            prominent
                            border="left"
                        >
                            Not connected
                            <c-strava-client-form title="Add new client" button-text="Create">
                                <template v-slot:activator="{trigger, showing}">
                                    <v-btn
                                        data-hint="You can add a new client here"
                                        @click="trigger"
                                        :disabled="showing"
                                    >
                                        Create client
                                    </v-btn>
                                </template>
                            </c-strava-client-form>

                        </v-alert>
                        <c-strava-client-list :owned-clients="ownedClients" :shared-clients="sharedClients"
                                              :public-clients="publicClients"></c-strava-client-list>
                    </div>
                    <div v-else-if="!isConnected && !clientIsAvailable">
                        <v-alert
                            outlined
                            type="warning"
                            prominent
                            border="left"
                        >
                            No client is available
                            <c-strava-client-form title="Add new client" button-text="Create">
                                <template v-slot:activator="{trigger, showing}">
                                    <v-btn
                                        data-hint="You can add a new client here"
                                        @click="trigger"
                                        :disabled="showing"
                                    >
                                        Create client
                                    </v-btn>
                                </template>
                            </c-strava-client-form>
                        </v-alert>
                    </div>
                    <div v-else-if="isConnected">
                        <v-alert
                            outlined
                            type="success"
                            prominent
                            border="left"
                            v-if="clientHasSpace"
                        >
                            Connected
                        </v-alert>
                        <v-alert
                            outlined
                            type="warning"
                            prominent
                            border="left"
                            v-else
                        >
                            Available shortly, rate limits met
                        </v-alert>
                        <c-strava-client-list :owned-clients="ownedClients" :shared-clients="sharedClients"
                                              :public-clients="publicClients"></c-strava-client-list>
                    </div>
                </div>
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
                                Loading from Strava. These numbers aren't yet accurate.
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
        <template #sidebar>
            <v-list>
                <v-list-item v-if="canManageClients">
                    <c-strava-client-form title="Add new client" button-text="Create">
                        <template v-slot:activator="{trigger, showing}">
                            <v-btn
                                data-hint="You can add a new client here"
                                @click="trigger"
                                :disabled="showing"
                            >
                                Create client
                            </v-btn>
                        </template>
                    </c-strava-client-form>
                </v-list-item>
                <v-list-item v-if="canManageClients">
                    <v-btn
                        :href="route('larecipe.show', {version: '1.0', page: 'strava/logging-in-clients'})"
                        data-hint="Get help creating a new client"
                    >
                        Get help creating a new client
                    </v-btn>
                </v-list-item>
                <v-list-item>
                    <v-btn
                        data-hint="Sync Strava"
                        @click="$inertia.post(route('strava.sync'))"
                        :disabled="!isConnected"
                    >
                        Sync Strava
                    </v-btn>
                </v-list-item>
                <v-list-item>
                    <c-strava-import-form>
                        <template v-slot:activator="{trigger, showing}">
                            <v-btn
                                data-hint="You can import the export of your Strava account to quickly syncronise your data."
                                @click="trigger"
                                :disabled="showing"
                            >
                                Import Strava data
                            </v-btn>
                        </template>
                    </c-strava-import-form>
                </v-list-item>
            </v-list>
        </template>
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

export default {
    name: "Index",
    components: {
        CImportResult,
        CStravaImportForm,
        CLinkActivity, CSyncInformationItem, CStravaClientList, CStravaClientForm, CAppWrapper
    },
    props: {
        ownedClients: {
            required: true,
            type: Object
        },
        sharedClients: {
            required: true,
            type: Object
        },
        publicClients: {
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
        }
    },
    data() {
        return {
            tab: null,
            isReloading: false
        }
    },

    methods: {
        createStravaLoginUrl(client) {
            let stravaUrl = this.$vuetify.breakpoint.mobile
                ? new URL('https://www.strava.com/oauth/mobile/authorize')
                : new URL('https://www.strava.com/oauth/authorize');

            stravaUrl.searchParams.append('client_id', client.client_id);
            stravaUrl.searchParams.append('redirect_uri', route('strava.client.login', client.id));
            stravaUrl.searchParams.append('response_type', 'code');
            stravaUrl.searchParams.append('approval_prompt', 'auto');
            stravaUrl.searchParams.append('scope', 'activity:read,read,read_all,profile:read_all,activity:read_all,activity:write');
            stravaUrl.searchParams.append('state', 12345);

            return stravaUrl.toString();
        },
        logoutClient() {
            if (this.isConnected) {
                this.$inertia.post(route('strava.client.logout', this.clients
                    .filter(client => client.enabled)
                    .filter(client => client.is_connected)[0].id));
            }
        },
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
        clients() {
            let clients = [];
            for (let data of this.ownedClients.data) {
                clients.push(data);
            }
            for (let data of this.sharedClients.data) {
                clients.push(data);
            }
            for (let data of this.publicClients.data) {
                clients.push(data);
            }
            return clients;
        },
        canManageClients() {
            return this.$page.props.permissions.indexOf('manage-strava-clients') > -1;
        },
        clientIsAvailable() {
            return this.clients.filter(client => client.enabled).length > 0;
        },
        isConnected() {
            return this.clients.filter(client => client.enabled).filter(client => client.is_connected).length > 0;
        },
        clientHasSpace() {
            return this.clients.filter(client => client.enabled)
                .filter(client => client.is_connected)
                .filter(client => client.limit_15_min > client.used_15_min_calls)
                .filter(client => client.limit_daily > client.used_daily_calls)
                .length > 0;
        },
        stravaLoginUrl() {
            if (this.clientIsAvailable) {
                return this.createStravaLoginUrl(
                    this.clients.filter(client => client.enabled)[0]
                )
            }
            return null;
        }
    }
}
</script>

<style scoped>

</style>
