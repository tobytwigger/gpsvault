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
    name: "CTourMap",
    components: {CMap},
    props: {
        tour: {
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
        axios.get(route('tour.geojson', this.tour.id))
            .then(response => this.geojson = response.data)
            .then(() => this.loading = false);
    },

}
</script>

<style scoped>

</style>
