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
            result: {
                coordinates: []
            },
            schema: {
                test: 'hello'
            }
        }
    },
    methods: {
        updateSchema(schema) {
            this.schema = schema;
            this.performSearch();
        },
        performSearch() {
            // TODO
        },
        save() {
            // if(this.routeModel) {
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
            // } else if(this.geojson) {
            //     this.$inertia.post(route('planner.store'), {
            //         name: 'New Route',
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
            // }
        }
    },
    mounted() {
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
