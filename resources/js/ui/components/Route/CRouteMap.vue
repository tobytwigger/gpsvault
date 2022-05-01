<template>
    <div>
        <div v-if="loading">Loading map</div>
        <div v-else-if="geojson === null">No route could be plotted</div>
        <c-map :geojson="geojson" :markers="markers" v-else></c-map>
    </div>
</template>

<script>
import CMap from '../Map/CMap';
export default {
    name: "CRouteMap",
    components: {CMap},
    props: {
        stats: {
            required: true,
            type: Object
        },
        places: {
            required: false,
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            loading: true,
            geojson: null
        }
    },
    mounted() {
        axios.get(route('stats.geojson', this.stats.id))
            .then(response => this.geojson = response.data)
            .then(() => this.loading = false);
    },
    computed: {
        markers() {
            return this.places.map(place => {
                return {
                    id: place.id,
                    lat: place.location.coordinates[0],
                    lng: place.location.coordinates[1],
                    icon: place.type,
                    title: place.name
                }
            })
        }
    }
}
</script>

<style scoped>

</style>
