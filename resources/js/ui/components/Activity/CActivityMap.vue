<template>
    <div>
        <div v-if="loading">Loading map</div>
        <div v-else-if="geojson.coordinates === null">No route could be plotted</div>
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
            return {
                type: 'LineString',
                coordinates: this.stats.linestringWithDistance ?? null
            };
        }
    }
}
</script>

<style scoped>

</style>
