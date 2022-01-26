<template>
    <v-card
        class="mx-auto"
        max-width="344"
        outlined
    >

        {{client}}
        <v-card-title>
            {{cardName}}
        </v-card-title>

        <v-card-subtitle v-if="client.description">
            {{ client.description }}
        </v-card-subtitle>

        <v-card-text>
            <p>Enabled: {{client.enabled}}</p>
            <p>Public: {{client.public}}</p>
            <p>Is connected: {{client.is_connected}}</p>
            <p>Client secret: {{client.client_secret}}</p>
            <p class="text-black">{{ client.used_15_min_calls }}/100 used until {{ next15Mins }}</p>
            <p class="text-black">{{ client.used_daily_calls }}/1000 used until {{ nextDay }}</p>

            <span v-if="client.invitation_link_uuid">
                {{ client.invitation_link }}.
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

<!--        <v-card-text>-->
<!--            <v-chip-->
<!--                outlined-->
<!--                class="ma-2"-->
<!--                color="indigo"-->
<!--            >-->
<!--                <v-icon left>-->
<!--                    mdi-ruler-->
<!--                </v-icon>-->
<!--                {{ convertDistance(activity.distance) }}-->
<!--            </v-chip>-->
<!--            &lt;!&ndash;&ndash;&gt;-->
<!--            <v-chip-->
<!--                outlined-->
<!--                color="indigo"-->
<!--            >-->
<!--                <v-icon left>-->
<!--                    mdi-calendar-range-->
<!--                </v-icon>-->
<!--                {{ toDateTime(activity.started_at) }}-->
<!--            </v-chip>-->
<!--        </v-card-text>-->

<!--        &lt;!&ndash;            <v-list-item-avatar&ndash;&gt;-->
<!--        &lt;!&ndash;                tile&ndash;&gt;-->
<!--        &lt;!&ndash;                size="80"&ndash;&gt;-->
<!--        &lt;!&ndash;                color="grey"&ndash;&gt;-->
<!--        &lt;!&ndash;            ></v-list-item-avatar>&ndash;&gt;-->
<!--        &lt;!&ndash;        </v-list-item>&ndash;&gt;-->

<!--        <v-card-actions>-->
<!--            <v-btn-->
<!--                color="deep-purple lighten-2"-->
<!--                text-->
<!--                @click="$inertia.get(route('activity.show', activity.id))"-->
<!--            >-->
<!--                View-->
<!--            </v-btn>-->

<!--            <v-spacer></v-spacer>-->

<!--        </v-card-actions>-->

    </v-card>
</template>

<script>
import strava from '../../mixins/strava';
import moment from 'moment';

export default {
    name: "CStravaClientCard",
    mixins: [strava],
    props: {
        client: {
            required: true,
            type: Object
        }
    },
    computed: {
        cardName() {
            if(this.client.name) {
                return this.client.name + ' - ' + this.client.client_id;
            }
            return this.client.client_id;
        }
    },
    methods: {
        toDateTime(value) {
            if (value === null) {
                return 'No Date';
            }
            return moment(value).format('DD/MM/YYYY HH:mm');
        },
    }
}
</script>

<style scoped>

</style>
