<template>
    <l-map style="height:50vh" v-model:zoom="zoom" ref="map">
        <l-tile-layer
            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            layer-type="base"
            name="OpenStreetMap"
        ></l-tile-layer>

        <l-polyline
            ref="polyline"
            @ready="setMapBounds"
            :lat-lngs="points"
            color="green"
        />
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
    computed: {
        points() {
            return this.stats.points.map(p => [p.latitude, p.longitude]);
        }
    },
    methods: {
        setMapBounds() {
            this.$refs.map.leafletObject.fitBounds(
                this.$refs.polyline.leafletObject.getBounds()
            );
        }
    }
}
</script>

<style scoped>

</style>
