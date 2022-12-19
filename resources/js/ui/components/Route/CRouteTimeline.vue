<template>
    <div>
        <v-timeline
            align-top
            :dense="$vuetify.breakpoint.smAndDown"
        >
            <v-timeline-item
                v-for="(waypoint, waypointIndex) in waypoints"
                :key="waypointIndex"
                :color="waypoint.color"
                fill-dot
            >
                <template v-slot:icon v-if="waypoint.icon">
                    <img :src="waypoint.icon" />
                </template>

                <v-card
                    :color="waypoint.color"
                >
                    <v-card-title class="text-h6">
                        {{ waypoint.name ?? 'Waypoint #' + (waypointIndex + 1)}}
                    </v-card-title>
                    <v-card-text>
                        Info about distance,
                    </v-card-text>
                    <v-card-text class="white text--primary">
                        <p v-if="waypoint.notes">{{waypoint.notes}}</p>
                        <div v-if="waypoint.place_id">
                            Stuff about the place!
                        </div>
                        <v-btn
                            v-if="waypoint.place_id"
                            :href="route('place.show', waypoint.place_id)"
                            :color="waypoint.color"
                            class="mx-0"
                            outlined
                        >
                            See more
                        </v-btn>
                    </v-card-text>
                </v-card>
            </v-timeline-item>
        </v-timeline>
        <!--                <v-row-->
        <!--                    align="center"-->
        <!--                    justify="center">-->
        <!--                    <v-col>-->

        <!--                        <c-pagination-iterator :paginator="places" item-key="id">-->
        <!--                            <template v-slot:default="{item}">-->
        <!--                                <c-place-card :place="item">-->
        <!--                                    <template v-slot:icons>-->
        <!--                                        <v-tooltip bottom>-->
        <!--                                            <template v-slot:activator="{ on, attrs }">-->
        <!--                                                <v-btn-->
        <!--                                                    icon-->
        <!--                                                    @click="removeFromRoute(item)"-->
        <!--                                                    v-bind="attrs"-->
        <!--                                                    v-on="on"-->
        <!--                                                >-->
        <!--                                                    <v-icon>mdi-minus</v-icon>-->
        <!--                                                </v-btn>-->
        <!--                                            </template>-->
        <!--                                            Remove from route-->
        <!--                                        </v-tooltip>-->
        <!--                                    </template>-->
        <!--                                </c-place-card>-->
        <!--                            </template>-->
        <!--                        </c-pagination-iterator>-->
        <!--                    </v-col>-->
        <!--                </v-row>-->


        <!--                <v-row-->
        <!--                    align="center"-->
        <!--                    justify="center">-->
        <!--                    <v-col>-->
        <!--                        <c-place-search ref="placeSearch" :route-id="routeModel.id" @addToRoute="addToRoute" title="Search for a place" button-text="Add to route">-->
        <!--                            <template v-slot:activator="{trigger,showing}">-->
        <!--                                <v-btn :disabled="showing" @click="trigger">-->
        <!--                                    Find Places-->
        <!--                                </v-btn>-->
        <!--                            </template>-->
        <!--                        </c-place-search>-->
        <!--                    </v-col>-->
        <!--                </v-row>-->
    </div>
</template>

<script>
import mapIcons from '../../mixins/mapIcons';

export default {
    name: "CRouteTimeline",
    mixins: [mapIcons],
    props: {
        routeModel: {
            required: true,
            type: Object
        }
    },
    computed: {
        waypoints() {
            if(this.routeModel.path) {
                return this.routeModel.path.waypoints.map(w => ({
                    icon: this.getIconUrl(w.place?.type),
                    color: this.getColor(w.place?.type),
                    name: w.name,
                    notes: w.notes,
                    place_id: w.place_id
                }));
            }
            return [];
        }
    }
}
</script>

<style scoped>

</style>
