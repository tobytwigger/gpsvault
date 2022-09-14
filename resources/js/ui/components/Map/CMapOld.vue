<template>
    <div>



        <l-map style="height: 50vh" v-model:zoom="zoom" ref="map" @ready="setUpElevation">
            <l-control-fullscreen position="topleft"
                :options="{ title: { 'false': 'Go big!', 'true': 'Be regular' } }" />

            <l-control-layers position="topright"></l-control-layers>

            <l-tile-layer
                v-for="tileProvider in tileProviders"
                :key="tileProvider.name"
                :name="tileProvider.name"
                :visible="tileProvider.visible"
                :url="tileProvider.url"
                :attribution="tileProvider.attribution"
                layer-type="base"/>

            <l-geo-json
                ref="geojson"
                :geojson="normalGeojson"
                @ready="setMapBounds"></l-geo-json>

            <l-marker v-for="marker in markers" :key="'marker-' + marker.id" :draggable="false" :ref="'marker-' + marker.id" :lat-lng="[marker.lng, marker.lat]">
                <l-icon
                    :icon-size="[32,32]"
                    :icon-anchor="[32,32]"
                    :icon-url="'/dist/images/leaflet/' + marker.icon + '.svg'"
                />
                <l-tooltip>{{ marker.title }}</l-tooltip>
            </l-marker>

            <l-ruler :options="rulerOptions" />
        </l-map>
    </div>
</template>

<script>
import { LMap, LGeoJson, LTileLayer, LControl, LControlLayers, LMarker, LTooltip, LIcon } from "vue2-leaflet";
import LControlFullscreen from 'vue2-leaflet-fullscreen';
import LRuler from "vue2-leaflet-ruler";

export default {
    name: "CMap",
    components: {
        LMap,
        LGeoJson,
        LTileLayer,
        LControlLayers,
        LControl,
        LControlFullscreen,
        LRuler,
        LMarker,
        LTooltip,
        LIcon
    },
    props: {
        geojson: {
            type: Object,
            required: true
        },
        markers: {
            type: Array,
            required: false,
            default: () => []
        }
    },
    data() {
        return {
            tileProviders: [
                {
                    name: 'OpenStreetMap',
                    visible: true,
                    attribution:
                        '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
                    url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                },
                {
                    name: 'OpenTopoMap',
                    visible: false,
                    url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
                    attribution:
                        'Map data: &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
                }
            ],
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

    methods: {
        setMapBounds() {
            this.$refs.map.mapObject.fitBounds(
                this.$refs.geojson.mapObject.getBounds()
            );
        },
        setUpElevation() {
            // let elevation = L.control.Elevation();
            // elevation.addTo(this.$refs.map.mapObject);
            // L.geoJson(this.elevationGeojson,{
            //     onEachFeature: el.addData.bind(el)
            // }).addTo(this.$refs.map.mapObject);
        }
    },

    computed: {
        elevationGeojson() {
            if(this.geojson === null) {
                return null;
            }
            return {
                "name":"NewFeatureType",
                "type":"FeatureCollection",
                "features":[
                    {
                        "type":"Feature",
                        "geometry":this.geojson,
                        "properties":null
                    }
                ]};
        },
        normalGeojson() {
            return {
                type: this.geojson.type,
                coordinates: this.geojson.coordinates.map(c => [c[0], c[1]])
            }
        }
    }
}
</script>

<style lang="scss">
 .vue2leaflet-map {
     z-index: 1;
 }
</style>
