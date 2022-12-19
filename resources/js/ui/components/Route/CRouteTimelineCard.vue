<template>
    <v-timeline-item
        :color="color"
        fill-dot
    >
        <template v-slot:icon v-if="icon">
            <img style="height: 24px; width: 24px" :src="icon"/>
        </template>

        <template v-slot:opposite v-if="percentageThroughRoute !== null">
            <v-progress-circular
                :value="percentageThroughRoute"
                :color="color"
                :width="15"
                :size="75"
            >
                {{ Math.round(percentageThroughRoute) }}%
            </v-progress-circular>
        </template>

        <v-card
            :color="color"
        >
            <v-card-title class="text-h6">
                {{ name }}
            </v-card-title>
            <v-card-text class=" py-0 white text--primary">
                <v-chip
                    v-if="distance !== null"
                    outlined
                    class="ma-2"
                    color="indigo"
                >
                    <v-icon left>
                        mdi-ruler
                    </v-icon>
                    {{ distance }}
                </v-chip>
                <!---->
                <v-chip
                    v-if="duration !== null"
                    outlined
                    color="indigo"
                >
                    <v-icon left>
                        mdi-calendar-range
                    </v-icon>
                    {{ duration }}
                </v-chip>
            </v-card-text>
            <v-card-text class="white text--primary">
                <p v-if="waypoint.notes">{{ waypoint.notes }}</p>
                <div v-if="place">
                    <v-card
                        class="mx-auto my-12"
                        max-width="374"
                    >

                        <v-card-title>{{ place.name }}</v-card-title>

                        <v-card-text>
                            <v-row
                                align="center"
                                class="mx-0"
                            >
                                <v-list dense flat>
                                    <v-list-item-group>
                                        <v-list-item v-if="place?.url" :href="place.url">
                                            <v-list-item-icon>
                                                <v-icon>mdi-link</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>{{place.url}}</v-list-item-content>
                                        </v-list-item>
                                        <v-list-item v-if="place?.email" :href="'mailto:' + place.email">
                                            <v-list-item-icon>
                                                <v-icon>mdi-at</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>{{place.email}}</v-list-item-content>
                                        </v-list-item>
                                        <v-list-item v-if="place?.phone_number" :href="'tel:' + place.phone_number">
                                            <v-list-item-icon>
                                                <v-icon>mdi-phone</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>{{place.phone_number}}</v-list-item-content>
                                        </v-list-item>

                                    </v-list-item-group>
                                </v-list>

                            </v-row>

                            <div class="my-4 text-subtitle-1">
                                {{ typeText }}
                            </div>

                            <div>S{{ place?.description }}</div>
                        </v-card-text>

                        <v-divider class="mx-4"></v-divider>

                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                :href="route('place.show', waypoint.place_id)"
                                :color="color"
                                text
                            >
                                See More
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </div>
            </v-card-text>
        </v-card>
    </v-timeline-item>

</template>

<script>
import units from '../../mixins/units';
import mapIcons from '../../mixins/mapIcons';
import moment from 'moment/moment';
import {floor} from 'lodash';

export default {
    name: "CRouteTimelineCard",
    mixins: [units, mapIcons],
    props: {
        waypoint: {
            required: true,
            type: Object
        },
        routeDistance: {
            required: false,
            type: Number,
            default: null
        },
        waypointIndex: {
            required: true,
            type: Number
        },
        waypointCount: {
            required: true,
            type: Number
        }
    },
    computed: {
        place() {
            return this.waypoint.place ?? null;
        },
        icon() {
            if (this.waypointIndex === 0 || this.waypointIndex === (this.waypointCount - 1)) {
                return this.getEndpointIconUrl();
            }
            return this.getIconUrl(this.waypoint.place?.type);
        },
        color() {
            return this.getColor(this.waypoint.place?.type);
        },
        name() {
            let name = this.waypoint.name ?? 'Waypoint #' + (this.waypointIndex + 1);
            if (this.waypointIndex === 0) {
                name += ' (start)';
            } else if (this.waypointIndex === (this.waypointCount - 1)) {
                name += ' (end)';
            }
            return name;
        },
        distance() {
            if (this.waypoint.distance === null) {
                return 'N/A';
            }
            let converted = this.convert(this.waypoint.distance, 'distance');
            return converted.value + converted.unit;
        },
        percentageThroughRoute() {
            if(this.waypoint.distance === null || this.routeDistance === null) {
                return null;
            }
            return Math.round((this.waypoint.distance / this.routeDistance) * 100);
        },
        duration() {
            if(this.waypointIndex === 0) {
                return '0h 0m';
            }
            if (this.waypoint.duration === null) {
                return 'N/A';
            }
            let momentDuration = moment.duration(this.waypoint.duration, 's');
            if(momentDuration.asSeconds() > 0) {
                let hours = floor(momentDuration.asHours())
                let minutes = floor(momentDuration.asMinutes() % 60)
                return hours + 'h ' + minutes + 'm';
            }
            return 'N/A';
        },
        typeText() {
            return this.getTypeText(this.waypoint.type);
        },
    },
}
</script>

<style scoped>

</style>
