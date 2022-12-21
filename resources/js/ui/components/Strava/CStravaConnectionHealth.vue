<template>

    <v-card>
        <v-card-title>
            Strava Connection
        </v-card-title>

        <v-card-subtitle>
            <div v-if="!isConnected">
                <v-alert
                    outlined
                    type="warning"
                    prominent
                    border="left"
                >
                    Your account is not connected to Strava
                </v-alert>
            </div>
            <div v-else>
                <v-alert
                    outlined
                    type="success"
                    prominent
                    border="left"
                >
                    You have connected your account to Strava.
                </v-alert>
            </div>
        </v-card-subtitle>

        <v-card-text>
            <div v-if="!isConnected">
                <span v-if="clientIsAvailable">
                    <p>You will need to connect your Strava account before you can sync any data.</p>

                    <p style="text-align: center;">
                        <v-btn :href="stravaLoginUrl" v-if="stravaLoginUrl">
                            <v-img src="/dist/images/strava_logo.svg" alt="Connect to Strava"/>
                        </v-btn>
                    </p>
                </span>
                <span v-else>
                    <p>The Strava integration is not yet set up.</p>
                    <p>
                        <span v-if="canManageClients">
                            Create an API client to connect with.
                        </span>
                        <span v-else>
                            Please contact us so we can get this sorted.
                        </span>
                    </p>
                    <p v-if="canManageClients">
                        <v-btn @click="showStravaClientForm = true">
                            <v-icon>mdi-plus</v-icon> Create a new client
                        </v-btn>
                        <c-strava-client-form title="Add new client" button-text="Create" v-model="showStravaClientForm"></c-strava-client-form>
                    </p>
                </span>
            </div>
            <div v-else>
                <p style="text-align: center;" v-if="!canManageClients">
                    <v-btn @click="logoutOfStrava">
                        Log out of Strava
                    </v-btn>
                </p>

                <span v-if="!clientHasSpace">
                    Strava is currently limiting the number of API calls we can make, so sync may not happen immediately.
                </span>
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
import CStravaClientForm from './CStravaClientForm';
export default {
    name: "CStravaConnectionHealth",
    components: {CStravaClientForm},
    props: {
        clientIsAvailable: {
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
            showStravaClientForm: false,
            loggingOut: false
        }
    },
    methods: {
        logoutOfStrava() {
            if (this.defaultClient.is_connected) {
                this.$inertia.post(route('strava.client.logout', this.defaultClient.id), {}, {
                    onBefore: () => this.loggingOut = true,
                    onFinish: () => this.loggingOut = false
                });
            }
        },
    },
    computed: {
        stravaLoginUrl() {
            return route('strava.client.login.start', {client: this.defaultClient.id, mobile: this.$vuetify.breakpoint.mobile})
        },
        canManageClients() {
            return this.$page.props.permissions.indexOf('manage-strava-clients') > -1;
        },
    }
}
</script>

<style scoped>

</style>
