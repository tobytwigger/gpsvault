<template>
    <div>
        <l-map style="height:50vh" v-model:zoom="zoom" ref="map" v-if="!loadingMap">
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
    </div>
</template>

<script>
import { LMap, LGeoJson, LTileLayer } from "vue2-leaflet";

export default {
    name: "CMap",
    components: {
        LMap,
        LGeoJson,
        LTileLayer
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
            zoom: 9
        }
    },
    mounted() {
        axios.get(route('stats.geojson', this.stats.id))
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
