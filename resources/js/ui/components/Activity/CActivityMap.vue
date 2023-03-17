<template>
    <div>
        <div v-if="geojson.coordinates === null">No route could be plotted</div>
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
