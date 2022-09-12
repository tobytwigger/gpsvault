<template>
    <c-app-wrapper title="Strava" :action-sidebar="true">
        <v-tabs
            v-model="tab"
            centered
            grow
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>
            <v-tab href="#tab-connection">Connection Health<v-icon>mdi-heart-pulse</v-icon></v-tab>
            <v-tab href="#tab-sync">Sync<v-icon>mdi-autorenew</v-icon></v-tab>
            <v-tab href="#tab-import">Imports<v-icon>mdi-import</v-icon></v-tab>
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
                            <v-img src="/dist/images/strava_logo.svg" alt="Connect to Strava" />
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
                        <c-strava-client-list :owned-clients="ownedClients" :shared-clients="sharedClients" :public-clients="publicClients"></c-strava-client-list>
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
                        <c-strava-client-list :owned-clients="ownedClients" :shared-clients="sharedClients" :public-clients="publicClients"></c-strava-client-list>
                    </div>
                </div>
            </v-tab-item>

            <v-tab-item value="tab-sync">
                <v-alert
                    outlined
                    type="warning"
                    prominent
                    border="left"
                >
                    Sync is still in development and will be available soon.
                </v-alert>
            </v-tab-item>

            <v-tab-item value="tab-import">
                <v-alert
                    outlined
                    type="warning"
                    prominent
                    border="left"
                >
                    Importing is still in development and will be available soon.
                </v-alert>
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
            </v-list>
        </template>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
import CStravaClientForm from '../../../ui/components/Strava/CStravaClientForm';
import CStravaClientList from '../../../ui/components/Strava/CStravaClientList';
export default {
    name: "Index",
    components: {CStravaClientList, CStravaClientForm, CAppWrapper},
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
        }
    },
    data() {
        return {
            tab: null
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
            if(this.isConnected) {
                this.$inertia.post(route('strava.client.logout', this.clients
                    .filter(client => client.enabled)
                    .filter(client => client.is_connected)[0].id));
            }
        }
    },

    computed: {
        clients() {
            let clients = [];
            for(let data of this.ownedClients.data) {
                clients.push(data);
            }
            for(let data of this.sharedClients.data) {
                clients.push(data);
            }
            for(let data of this.publicClients.data) {
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
            if(this.clientIsAvailable) {
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
