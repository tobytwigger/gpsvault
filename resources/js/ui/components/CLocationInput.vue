<template>

    <l-map style="height: 50vh" ref="map" zoom="9" @ready="initialiseClickListener">
<!--        <l-control-layers position="topright"></l-control-layers>-->

        <l-tile-layer
            v-for="tileProvider in tileProviders"
            :key="tileProvider.name"
            :name="tileProvider.name"
            :visible="tileProvider.visible"
            :url="tileProvider.url"
            :attribution="tileProvider.attribution"
            layer-type="base"/>

        <l-marker v-if="hasLocation" :lat-lng="value"></l-marker>

    </l-map>

</template>

<script>
import { LMap, LGeoJson, LTileLayer, LControl, LControlLayers, LMarker } from "vue2-leaflet";

export default {
    name: "CLocationInput",
    components: {
        LMap,
        LGeoJson,
        LTileLayer,
        LControlLayers,
        LControl,
        LMarker
    },
    props: {
        value: {
            required: true,
            type: Object,
        }
    },
    methods: {
        initialiseClickListener() {
            this.$refs.map.mapObject.setView([52.025612, -0.801140]);

            this.$refs.map.mapObject.on('click', function(e) {
                this.$emit('input', {
                    lat: e.latlng.lat,
                    lng: e.latlng.lng,
                })
            });
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
            zoom: 9
        }
    },
    computed: {
        hasLocation() {
            return this.value.latitude !== null && this.value.longitude !== null;
        }
    }
}
</script>

<style scoped>

</style>
