<template>
    <v-card
        class="mx-auto"
        max-width="344"
        outlined
    >

        <v-card-title>
            {{cardName}}
        </v-card-title>

        <v-card-subtitle v-if="client.description">
            {{ client.description }}
        </v-card-subtitle>

        <v-card-text>

            <v-progress-linear
                :value="Math.ceil((client.used_15_min_calls / client.limit_15_min) * 100)"
                height="25"
            >
                {{ client.used_15_min_calls }}/{{client.limit_15_min }}
            </v-progress-linear>
            <p class="text-black">Rate limit reset at {{ next15Mins }}</p>

            <v-progress-linear
                :value="Math.ceil((client.used_daily_calls / client.limit_daily) * 100)"
                height="25"
            >
                {{ client.used_daily_calls }}/{{client.limit_daily }}
            </v-progress-linear>
            <p class="text-black">Daily limit resets in {{ nextDayIn }}</p>

        </v-card-text>

        <v-card-text>
            <v-btn @click="$inertia.post(route('strava.client.logout', client.id))" v-if="client.is_connected">Logout</v-btn>
            <v-btn :href="stravaLoginUrl" v-else>
                <v-img src="/dist/images/strava_logo.svg" alt="Connect to Strava" />
            </v-btn>
        </v-card-text>

        <v-card-actions v-if="type === 'owned'">

            <v-spacer></v-spacer>

            <c-strava-client-form :old-client="client" title="Edit client" button-text="Update">
                <template v-slot:activator="{trigger, showing}">
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                icon
                                link
                                @click="trigger"
                                :disabled="showing"
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                        </template>
                        Edit
                    </v-tooltip>
                </template>
            </c-strava-client-form>

            <div>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            v-if="client.is_connected"
                            icon
                            link
                            @click="testClient"
                            :disabled="testingClient"
                            :loading="testingClient"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon :color="testClientColour">mdi-test-tube</v-icon>
                        </v-btn>
                    </template>
                    Test Client
                </v-tooltip>

                <v-snackbar
                    v-model="showSnackbar"
                >
                    {{ clientSuccessString }}

                    <template v-slot:action="{ attrs }">
                        <v-btn
                            color="pink"
                            text
                            v-bind="attrs"
                            @click="showSnackbar = false"
                        >
                            Close
                        </v-btn>
                    </template>
                </v-snackbar>
            </div>

            <div>
                <c-confirmation-dialog key="disableClient" v-if="client.enabled" title="Disable client?" button-text="Disable" :loading="isDisabling" cancel-button-text="Nevermind" @confirm="disableClient" ref="disableClientDialog">
                    <template v-slot:activator="{trigger,showing}">
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    icon
                                    link
                                    @click="trigger"
                                    :disabled="showing"
                                    :loading="showing"
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                    <v-icon color="green">mdi-power</v-icon>
                                </v-btn>
                            </template>
                            Disable Client
                        </v-tooltip>
                    </template>
                    <p>Are you sure you want to disable this client?</p>

                    <p>You and anyone with access to this client will not be able to use it until it is enabled.</p>
                </c-confirmation-dialog>

                <c-confirmation-dialog key="enableClient" v-else title="Enable client?" button-text="Enable" :loading="isEnabling" cancel-button-text="Nevermind" @confirm="enableClient" ref="enableClientDialog">
                    <template v-slot:activator="{trigger,showing}">
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    icon
                                    link
                                    @click="trigger"
                                    :disabled="showing"
                                    :loading="showing"
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                    <v-icon color="red">mdi-power</v-icon>
                                </v-btn>
                            </template>
                            Enable Client
                        </v-tooltip>
                    </template>
                    <p>Are you sure you want to enable this client?</p>

                    <p>It will be instantly accessible for you and anyone with access to it.</p>
                </c-confirmation-dialog>

            </div>

            <div>
                <c-confirmation-dialog key="makeClientPrivate" v-if="client.public" title="Make client private?" button-text="Make private" :loading="isMakingPrivate" cancel-button-text="Nevermind" @confirm="makeClientPrivate" ref="makeClientPrivateDialog">
                    <template v-slot:activator="{trigger,showing}">
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    icon
                                    link
                                    @click="trigger"
                                    :disabled="showing"
                                    :loading="showing"
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                    <v-icon>mdi-incognito-off</v-icon>
                                </v-btn>
                            </template>
                            Make client private
                        </v-tooltip>
                    </template>
                    <p>Are you sure you want to make this client private?</p>

                    <p>Only you will have access to it. If you make it public again, anyone with access to it currently will have access again.</p>
                </c-confirmation-dialog>

                <c-confirmation-dialog key="makeClientPublic" v-else title="Make client public?" button-text="Make public" :loading="isMakingPublic" cancel-button-text="Nevermind" @confirm="makeClientPublic" ref="makeClientPublicDialog">
                    <template v-slot:activator="{trigger,showing}">
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    icon
                                    link
                                    @click="trigger"
                                    :disabled="showing"
                                    :loading="showing"
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                    <v-icon>mdi-incognito</v-icon>
                                </v-btn>
                            </template>
                            Make client public
                        </v-tooltip>
                    </template>
                    <p>Are you sure you want to make this client public?</p>

                    <p>It will be instantly accessible for anyone else on the platform. We will not share your information with them or vice versa.</p>
                </c-confirmation-dialog>

            </div>

            <c-strava-client-invitations :client="client">
                <template v-slot:activator="{trigger,showing}">
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                icon
                                link
                                @click="trigger"
                                :disabled="showing"
                                :loading="showing"
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon>mdi-account-group</v-icon>
                            </v-btn>
                        </template>
                        Share
                    </v-tooltip>
                </template>
            </c-strava-client-invitations>

            <c-confirmation-dialog key="deleteClient" title="Delete client?" button-text="Delete" :loading="isDeleting" cancel-button-text="Nevermind" @confirm="deleteClient" ref="deleteClientDialog">
                <template v-slot:activator="{trigger,showing}">
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                icon
                                link
                                @click="trigger"
                                :disabled="showing"
                                :loading="showing"
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                        Delete
                    </v-tooltip>
                </template>
                <p>Are you sure you want to delete this client?</p>

                <p>Anyone using the client will no longer be able to use it. If you later decide to use this client again, you can add it as a new client.</p>
            </c-confirmation-dialog>

        </v-card-actions>


        <v-card-actions v-if="type === 'shared'">
            <v-spacer></v-spacer>

            <c-confirmation-dialog key="sharedClient" title="Leave client?" button-text="Leave" :loading="isLeaving" cancel-button-text="Nevermind" @confirm="leaveClient" ref="leavingClientDialog">
                <template v-slot:activator="{trigger,showing}">
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                icon
                                link
                                @click="trigger"
                                :disabled="showing"
                                :loading="showing"
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                        Leave
                    </v-tooltip>
                </template>
                <p>Are you sure you want to leave this client?</p>

                <p>You will no longer be able to use it, and will need a new link if you want to use it in the future.</p>
            </c-confirmation-dialog>

        </v-card-actions>

    </v-card>
</template>

<script>
import strava from '../../mixins/strava';
import CConfirmationDialog from '../CConfirmationDialog';
import CStravaClientForm from './CStravaClientForm';
import CStravaClientInvitations from './CStravaClientInvitations';

export default {
    name: "CStravaClientCard",
    components: {CStravaClientInvitations, CStravaClientForm, CConfirmationDialog},
    mixins: [strava],
    props: {
        client: {
            required: true,
            type: Object
        },
        type: {
            required: true,
            type: String,
            validator: (val) => val === 'owned' || val === 'shared' || val === 'public'
        }
    },
    data() {
        return {
            isDeleting: false,
            isEnabling: false,
            isDisabling: false,
            isMakingPublic: false,
            isMakingPrivate: false,
            isLeaving: false,
            testingClient: false,
            clientWorks: null,
            clientSuccessString: '',
            showSnackbar: false
        }
    },
    computed: {
        testClientColour() {
            if(this.clientWorks === true) {
                return 'green';
            } else if(this.clientWorks === false) {
                return 'red';
            }
            return 'theme--light';
        },
        cardName() {
            if(this.client.name) {
                return this.client.name + ' - ' + this.client.client_id;
            }
            return this.client.client_id;
        },
        stravaLoginUrl() {
            let stravaUrl = this.$vuetify.breakpoint.mobile
                ? new URL('https://www.strava.com/oauth/mobile/authorize')
                : new URL('https://www.strava.com/oauth/authorize');

            stravaUrl.searchParams.append('client_id', this.client.client_id);
            stravaUrl.searchParams.append('redirect_uri', route('strava.client.login', this.client.id));
            stravaUrl.searchParams.append('response_type', 'code');
            stravaUrl.searchParams.append('approval_prompt', 'auto');
            stravaUrl.searchParams.append('scope', 'activity:read,read,read_all,profile:read_all,activity:read_all,activity:write');
            stravaUrl.searchParams.append('state', 12345);

            return stravaUrl.toString();
        }
    },
    methods: {
        testClient() {
            this.testingClient = true;

            axios.post(route('strava.client.test', this.client.id))
                .then((response) => {
                    this.clientWorks = true;
                    this.clientSuccessString = 'You are logged in as ' + response.data.name;
                })
                .catch((error) => {
                    this.clientWorks = true;
                    this.clientSuccessString = 'Sorry, an error occured: ' + error.message;
                })
                .finally(() => {
                    this.showSnackbar = true;
                    this.testingClient = false;
                    this.$inertia.reload();
                })
        },
        deleteClient() {
            let ref = this.$refs.deleteClientDialog;
            this.isDeleting = true;
            this.$inertia.delete(route('strava.client.destroy', this.client.id), {
                onSuccess: () => {
                    this.isDeleting = false;
                    ref.close();
                }
            })
        },
        makeClientPublic() {
            let ref = this.$refs.makeClientPublicDialog;
            this.isMakingPublic = true;
            this.$inertia.post(route('strava.client.public', this.client.id), {
                onSuccess: () => {
                    this.isMakingPublic = false;
                    ref.close();
                }
            })
        },
        makeClientPrivate() {
            let ref = this.$refs.makeClientPrivateDialog;
            this.isMakingPrivate = true;
            this.$inertia.post(route('strava.client.private', this.client.id), {
                onSuccess: () => {
                    this.isMakingPrivate = false;
                    ref.close();
                }
            })
        },
        enableClient() {
            let ref = this.$refs.enableClientDialog;
            this.isEnabling = true;
            this.$inertia.post(route('strava.client.enable', this.client.id), {
                onSuccess: () => {
                    this.isEnabling = false;
                    ref.close();
                }
            })
        },
        leaveClient() {
            let ref = this.$refs.leavingClientDialog;
            this.isLeaving = true;
            this.$inertia.delete(route('strava.client.leave', this.client.id), {
                onSuccess: () => {
                    this.isLeaving = false;
                    ref.close();
                }
            })
        },
        disableClient() {
            let ref = this.$refs.disableClientDialog;
            this.isDisabling = true;
            this.$inertia.post(route('strava.client.disable', this.client.id), {
                onSuccess: () => {
                    this.isDisabling = false;
                    ref.close();
                }
            })
        }

    }
}
</script>

<style scoped>

</style>
