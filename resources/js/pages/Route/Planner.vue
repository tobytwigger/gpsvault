<template>
    <c-app-wrapper title="New Route" :header-action="true">

        <template #headerActions>

            <v-alert
                outlined
                type="warning"
                border="left"
                v-if="schema?.waypoints?.length < 2"
            >
                Add a start and end point to start planning
            </v-alert>

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

            <v-progress-circular
                indeterminate
                v-if="searching"
                color="primary"
            ></v-progress-circular>

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

        <c-route-planner
            :result="result"
            :schema="schema"
            @update:schema="updateSchema"
        ></c-route-planner>

    </c-app-wrapper>
</template>

<script>
import CAppWrapper from '../../ui/layouts/CAppWrapper';
import CRoutePlanner from '../../ui/components/Route/CRoutePlanner';
import polyline from '@mapbox/polyline';

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
            searching: false,
            result: {
                coordinates: [],
                distance: 0,
                time: 0,
                elevation: 0
            },
            schema: {
                waypoints: [],
                use_roads: 0.3,
                use_hills: 0.5
            }
        }
    },
    methods: {
        updateSchema(schema) {
            this.schema = schema;
            if(this.schema?.waypoints?.length > 1) {
                this.performSearch();
            } else {
                this.result = {
                    coordinates: [],
                    distance: 0,
                    time: 0
                };
            }
        },
        performSearch() {
            this.searching = true;
            axios.post(route('planner.plan'), this.schema)
                .then(response => this.result = response.data)
                .catch(e => console.log(e))
                .finally(() => this.searching = false);
        },
        save() {
            if(this.routeModel) {
            //     this.$inertia.patch(route('planner.update', this.routeModel.id), {
            //         'geojson': this.geojson.map(c => {
            //             return {lat: c[0], lng: c[1], alt: c[2]};
            //         }),
            //         'points': this.waypoints.map(r => {
            //             return {lat: r.lat, lng: r.lng}
            //         }),
            //         'distance': this.distance,
            //         'complete_in_seconds': this.time,
            //         'elevation': this.elevation
            //     })
            } else if(this.schema.waypoints.length > 1 && this.result.coordinates.length > 0) {
                this.$inertia.post(route('planner.store'), {
                    name: 'New Route',
                    'geojson': polyline.encode(this.result.coordinates.map(c => {
                        return [c[0], c[1]];
                    }), 6),
                    'waypoints': this.schema.waypoints.map(waypoint => {
                        // If custom waypoint, then we remove the ID
                        if(waypoint.unsaved ?? false) {
                            delete waypoint.id;
                        }

                        return {
                            id: waypoint.id ?? null,
                            lat: waypoint.location[0],
                            lng: waypoint.location[1],
                            place_id: waypoint.place_id
                        }
                    }),
                    'distance': this.result.distance,
                    'duration': this.result.time,
                    'elevation_gain': this.elevation
                })
            }
        }
    },
    mounted() {
        if(this.schema.waypoints.length > 1) {
            this.performSearch();
        }
        if(this.routeModel) {
            // this.waypoints = (this.routeModel?.path?.route_points ?? []).map(r => {
            //     return {lat: r.location.coordinates[1], lng: r.location.coordinates[0]}
            // })
            // this.geojson = (this.routeModel.path?.linestring ?? []).map(l => {
            //     return {lat: l.coordinates[1], lng: l.coordinates[0], alt: l.coordinates[2]}
            // })
            // this.distance = this.routeModel?.distance ?? 0;
            // this.elevation = this.routeModel?.elevation ?? 0;
            // this.time = this.routeModel?.time ?? 0;
        }
    }
}
</script>

<style scoped>
</style>
