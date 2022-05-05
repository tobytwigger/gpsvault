<template>
    <c-app-wrapper title="New Route" :header-action="true">

        <template #headerActions>
            <v-tooltip bottom v-if="routeModel">
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        data-hint="View the route itself"
                        icon
                        :href="route('route.show', routeModel.id)"
                        v-bind="attrs"
                        v-on="on"
                    >
                        <v-icon>mdi-eye</v-icon>
                    </v-btn>
                </template>
                <span>View</span>
            </v-tooltip>

            <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        data-hint="Click here to save your route."
                        icon
                        v-bind="attrs"
                        v-on="on"
                        @click="save"
                    >
                        <v-icon>mdi-content-save</v-icon>
                    </v-btn>
                </template>
                <span>Save</span>
            </v-tooltip>
        </template>

<!--
- On click, add a point via an API

- Pass it the start and finish


-->
        <c-route-planner
            :geojson.sync="geojson"
            :routePoints.sync="routePoints"
        ></c-route-planner>

    </c-app-wrapper>
</template>

<script>
import CAppWrapper from '../../ui/layouts/CAppWrapper';
import CRoutePlanner from '../../ui/components/Map/CRoutePlanner';
import L from 'leaflet';

export default {
    name: "Planner",
    components: {CRoutePlanner, CAppWrapper},
    props: {
        routeModel: {
            required: false,
            type: Object,
            default: null
        }
    },
    data() {
        return {
            routePoints: [],
            geojson: null
        }
    },
    methods: {
        save() {
            if(this.routeModel) {
                this.$inertia.patch(route('planner.update', this.routeModel.id), {
                    'geojson': this.geojson.coordinates,
                    'distance': 500,
                    'points': this.routePoints.map(r => {
                        return {lat: r.lat, lng: r.lng}
                    })
                })
            } else {
                this.$inertia.post(route('planner.store'), {
                    name: 'New Route',
                    'geojson': this.geojson.coordinates,
                    'distance': 500,
                    'points': this.routePoints.map(r => {
                        return {lat: r.lat, lng: r.lng}
                    })
                })
            }
        }
    },
    mounted() {
        if(this.routeModel) {
            this.routePoints = this.routeModel.route_points.map(r => {
                return {lat: r.location.coordinates[1], lng: r.location.coordinates[0]}
            })
            this.geojson = this.routeModel.path?.linestring.map(l => {
                return {lat: l.coordinates[1], lng: l.coordinates[0]}
            })
            console.log(this.routeModel);
        }
    }
}
</script>

<style scoped>
    @import 'leaflet-routing-machine/dist/leaflet-routing-machine.css';
</style>
