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
            <p>Client secret: {{client.client_secret}}</p>
        </v-card-text>

        <v-card-text>

            <p class="text-black">{{ client.used_15_min_calls }}/100 used until {{ next15Mins }}</p>
            <v-progress-linear
                :value="Math.ceil((client.used_15_min_calls / client.limit_15_min) * 100)"
                height="25"
            >
                15 minute usage
            </v-progress-linear>

            <p class="text-black">{{ client.used_daily_calls }}/1000 used until {{ nextDay }}</p>
            <v-progress-linear
                :value="Math.ceil((client.used_daily_calls / client.limit_daily) * 100)"
                height="25"
            >
                Daily usage
            </v-progress-linear>

        </v-card-text>

        <v-card-text>
            <span v-if="client.invitation_link_uuid">
                <Link :href="client.invitation_link">{{client.invitation_link}}</Link>
                <span v-if="client.invitation_link_expired">
                    Link Expired
                </span>
                <span v-else>
                Valid until {{ toDateTime(client.invitation_link_expires_at) }}
                </span>
            </span>
            <span v-else>
                No invitation link
            </span>
            <v-btn @click="$inertia.post(route('strava.client.invite', client.id))">
                Refresh
            </v-btn>
        </v-card-text>

        <v-card-text>
            <v-btn @click="$inertia.post(route('strava.client.logout', client.id))" v-if="client.is_connected">Logout</v-btn>
            <v-btn :href="stravaLoginUrl" v-else>Login</v-btn>
        </v-card-text>

        <v-card-actions>

            <v-spacer></v-spacer>

<!--            <c-stage-form :tour-id="stage.tour_id" :old-stage="stage" title="Edit stage" button-text="Update">-->
<!--                <template v-slot:activator="{trigger, showing}">-->
<!--                    <v-tooltip bottom>-->
<!--                        <template v-slot:activator="{ on, attrs }">-->
<!--                            <v-btn-->
<!--                                icon-->
<!--                                link-->
<!--                                @click="trigger"-->
<!--                                :disabled="showing"-->
<!--                                v-bind="attrs"-->
<!--                                v-on="on"-->
<!--                            >-->
<!--                                <v-icon>mdi-pencil</v-icon>-->
<!--                            </v-btn>-->
<!--                        </template>-->
<!--                        Edit-->
<!--                    </v-tooltip>-->
<!--                </template>-->
<!--            </c-stage-form>-->

            <c-confirmation-dialog title="Delete client?" button-text="Delete" :loading="isDeleting" cancel-button-text="Nevermind" @confirm="deleteClient">
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

            <div>
                <c-confirmation-dialog v-if="client.enabled" title="Disable client?" button-text="Disable" :loading="isDisabling" cancel-button-text="Nevermind" @confirm="disableClient">
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

                <c-confirmation-dialog v-else title="Enable client?" button-text="Enable" :loading="isEnabling" cancel-button-text="Nevermind" @confirm="enableClient">
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
                <c-confirmation-dialog v-if="client.public" title="Make client private?" button-text="Make private" :loading="isMakingPrivate" cancel-button-text="Nevermind" @confirm="makeClientPrivate">
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

                <c-confirmation-dialog v-else title="Make client public?" button-text="Make public" :loading="isMakingPublic" cancel-button-text="Nevermind" @confirm="makeClientPublic">
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
        </v-card-actions>

    </v-card>
</template>

<script>
import strava from '../../mixins/strava';
import moment from 'moment';
import CConfirmationDialog from '../CConfirmationDialog';

export default {
    name: "CStravaClientCard",
    components: {CConfirmationDialog},
    mixins: [strava],
    props: {
        client: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            isDeleting: false,
            isEnabling: false,
            isDisabling: false,
            isMakingPublic: false,
            isMakingPrivate: false
        }
    },
    computed: {
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
        toDateTime(value) {
            if (value === null) {
                return 'No Date';
            }
            return moment(value).format('DD/MM/YYYY HH:mm');
        },
        deleteClient() {
            this.isDeleting = true;
            this.$inertia.delete(route('strava.client.destroy', this.client.id), {
                onFinish: () => this.isDeleting = false
            })
        },
        makeClientPublic() {
            this.isMakingPublic = true;
            this.$inertia.post(route('strava.client.public', this.client.id), {
                onFinish: () => this.isMakingPublic = false
            })
        },
        makeClientPrivate() {
            this.isMakingPrivate = true;
            this.$inertia.post(route('strava.client.private', this.client.id), {
                onFinish: () => this.isMakingPrivate = false
            })
        },
        enableClient() {
            this.isEnabling = true;
            this.$inertia.post(route('strava.client.enable', this.client.id), {
                onFinish: () => this.isEnabling = false
            })
        },
        disableClient() {
            this.isDisabling = true;
            this.$inertia.post(route('strava.client.disable', this.client.id), {
                onFinish: () => this.isDisabling = false
            })
        }

    }
}
</script>

<style scoped>

</style>
