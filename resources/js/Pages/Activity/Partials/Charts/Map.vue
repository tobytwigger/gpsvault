<template>
    <div v-if="loadingMap">
        Loading map
    </div>
    <div v-else-if="geojson === null">
        Maps not available
    </div>
    <l-map style="height:50vh" v-model:zoom="zoom" ref="map" v-else>
        <l-tile-layer
            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            layer-type="base"
            name="OpenStreetMap"
        ></l-tile-layer>

        <l-geo-json
            ref="geojson"
            :geojson="geojson"
            @ready="setMapBounds"></l-geo-json>

    </l-map>
</template>

<script>
import MapMixin from './map';

export default {
    name: "Map",
    mixins: [MapMixin],
    props: {
        stats: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            geojson: null,
            loadingMap: false,
            zoom: 9
        }
    },
    watch: {
        stats: {
            handler() {
                this.loadGeoJson();
            },
            deep: true
        },
    },
    mounted() {
        this.loadGeoJson();
    },
    methods: {
        loadGeoJson() {
            this.loadingMap = true;
            axios.get(route('stats.geojson', this.stats.id))
                .then(response => this.geojson = response.data)
                .then(() => this.loadingMap = false);
        },
        setMapBounds() {
            this.$refs.map.leafletObject.fitBounds(
                this.$refs.geojson.leafletObject.getBounds()
            );
        }
    }
}
</script>

<style scoped>

</style>
