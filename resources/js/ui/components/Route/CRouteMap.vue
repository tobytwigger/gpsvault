<template>
    <div>
        <div v-if="loading">Loading map</div>
        <div v-else-if="geojson === null">No route could be plotted</div>
        <c-map :geojson="geojson" v-else></c-map>
    </div>
</template>

<script>
import CMap from '../CMap';
export default {
    name: "CRouteMap",
    components: {CMap},
    props: {
        stats: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            loading: true,
            geojson: null
        }
    },
    mounted() {
        axios.get(route('route.stats.geojson', this.stats.id))
            .then(response => this.geojson = response.data)
            .then(() => this.loading = false);
    },

}
</script>

<style scoped>

</style>
