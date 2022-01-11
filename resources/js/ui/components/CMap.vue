<template>
    <div>
        <l-map style="height:50vh" v-model:zoom="zoom" ref="map" v-if="!loadingMap">
            <l-control-fullscreen position="topleft"
                :options="{ title: { 'false': 'Go big!', 'true': 'Be regular' } }" />

            <l-tile-layer
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                layer-type="base"
                name="OpenStreetMap"
            ></l-tile-layer>

            <l-geo-json
                ref="geojson"
                :geojson="geojson"
                @ready="setMapBounds"></l-geo-json>

            <l-ruler :options="rulerOptions" />
        </l-map>
    </div>
</template>

<script>
import { LMap, LGeoJson, LTileLayer } from "vue2-leaflet";
import LControlFullscreen from 'vue2-leaflet-fullscreen';
import LRuler from "vue2-leaflet-ruler";

export default {
    name: "CMap",
    components: {
        LMap,
        LGeoJson,
        LTileLayer,
        LControlFullscreen,
        LRuler
    },
    props: {
        stats: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            geojson: null,
            loadingMap: true,
            zoom: 9,
            rulerOptions: {
                position: "topright", // Leaflet control position option
                circleMarker: {
                    // Leaflet circle marker options for points used in this plugin
                    color: "red",
                    radius: 2,
                },
                lineStyle: {
                    // Leaflet polyline options for lines used in this plugin
                    color: "red",
                    dashArray: "1,6",
                },
                lengthUnit: {
                    // You can use custom length units. Default unit is kilometers.
                    display: "km", // This is the display value will be shown on the screen. Example: 'meters'
                    decimal: 2, // Distance result will be fixed to this value.
                    factor: null, // This value will be used to convert from kilometers. Example: 1000 (from kilometers to meters)
                    label: "Distance:",
                },
                angleUnit: {
                    display: "&deg;", // This is the display value will be shown on the screen. Example: 'Gradian'
                    decimal: 2, // Bearing result will be fixed to this value.
                    factor: null, // This option is required to customize angle unit. Specify solid angle value for angle unit. Example: 400 (for gradian).
                    label: "Bearing:",
                },
            }
        }
    },
    mounted() {
        axios.get(ziggyRoute('stats.geojson', this.stats.id))
            .then(response => this.geojson = response.data)
            .then(() => this.loadingMap = false);
    },
    methods: {
        setMapBounds() {
            this.$refs.map.mapObject.fitBounds(
                this.$refs.geojson.mapObject.getBounds()
            );
        }
    }
}
</script>

<style scoped>

</style>
