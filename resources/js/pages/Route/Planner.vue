<template>
    <c-app-wrapper title="New Route" :header-action="true">

        <template #headerActions>
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

        <template #sidebar>
            <v-input
        </template>

<!--
- On click, add a point via an API

- Pass it the start and finish


-->
        <c-route-planner
            @waypointSpliced=""
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
    data() {
        return {
            routePoints: [],
            geojson: null
        }
    },
    methods: {
        save() {
            alert('Saving');
        }
    },
}
</script>

<style scoped>
    @import 'leaflet-routing-machine/dist/leaflet-routing-machine.css';
</style>
