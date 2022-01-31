<template>
    <div>
        <slot name="activator" v-bind:trigger="triggerDialog"  v-bind:showing="showDialog">

        </slot>

        <v-dialog
            v-model="showDialog"
            max-width="600"
        >
            <v-card>
                <v-card-title>
                    Invitations
                </v-card-title>
                <v-card-text>
                    <span v-if="client.invitation_link_expired">
                        Link Expired
                    </span>
                    <Link v-else-if="client.invitation_link" :href="client.invitation_link">{{client.invitation_link}}. Valid until {{ toDateTime(client.invitation_link_expires_at) }}</Link>
                    <span v-else>
                        No invitation link
                    </span>
                    <v-btn @click="$inertia.post(route('strava.client.invite', client.id))">
                        Refresh
                    </v-btn>
                </v-card-text>

                <v-card-text>
                    <v-list two-line>
                        <v-list-item v-for="user in client.shared_users">
                            <v-list-item-content>
                                <v-list-item-title>{{user.name}}</v-list-item-title>
                                <v-list-item-subtitle>{{user.email}}</v-list-item-subtitle>
                            </v-list-item-content>
                            <v-list-item-action>
                                <v-btn
                                    icon
                                    @click="removeFromClient(user.id)"
                                >
                                    <v-icon color="grey lighten-1">mdi-delete</v-icon>
                                </v-btn>
                            </v-list-item-action>
                        </v-list-item>
                    </v-list>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="secondary"
                        @click="showDialog = false"
                    >
                        Close
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import moment from 'moment';

export default {
    name: "CStravaClientInvitations",
    props: {
        client: {
            required: true,
            type: Object
        },
    },
    data() {
        return {
            showDialog: false,
        }
    },
    methods: {
        toDateTime(value) {
            if (value === null) {
                return 'No Date';
            }
            return moment(value).format('DD/MM/YYYY HH:mm');
        },
        removeFromClient(userId) {
            this.$inertia.delete(route('strava.client.remove', this.client.id), {
                data: {
                    user_id: userId
                }
            });
        },
        triggerDialog() {
            this.showDialog = true;
        }
    }
}
</script>

<style scoped>

</style>
