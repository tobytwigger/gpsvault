<template>
    <div>
        <div v-if="loading">Loading map</div>
        <div v-else-if="geojson === null">No route could be plotted</div>
        <c-map :geojson="geojson" v-else></c-map>
    </div>
</template>

<script>
import CMap from '../Map/CMap';
export default {
    name: "CActivityMap",
    components: {CMap},
    props: {
        stats: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            loading: false,
        }
    },
    computed: {
        geojson() {
            if(this.stats.linestring === null) {
                return null;
            }
            return {
                type: 'LineString',
                coordinates: this.stats.linestring.map(l => [l.coordinates[0], l.coordinates[1]])
            };
        }
    }
}
</script>

<style scoped>

</style>
