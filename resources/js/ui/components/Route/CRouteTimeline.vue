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
