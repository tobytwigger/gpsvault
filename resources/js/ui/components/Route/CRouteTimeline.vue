<template>
    <div>
        <v-timeline
            align-top
            :dense="$vuetify.breakpoint.smAndDown"
        >
            <c-route-timeline-card
                v-for="(waypoint, waypointIndex) in waypoints"
                :key="waypointIndex"
                :waypoint="waypoint"
                :waypoint-index="waypointIndex"
                :waypoint-count="waypoints.length"
                :route-distance="routeModel.path.distance"></c-route-timeline-card>

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
import CRouteTimelineCard from './CRouteTimelineCard';

export default {
    name: "CRouteTimeline",
    components: {CRouteTimelineCard},
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
                return this.routeModel.path.waypoints;
            }
            return [];
        }
    },
}
</script>

<style scoped>

</style>
