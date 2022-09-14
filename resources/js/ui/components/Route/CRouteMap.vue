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
    name: "CRouteMap",
    components: {CMap},
    props: {
        places: {
            required: false,
            type: Array,
            default: () => []
        },
        geojson: {
            required: false,
            type: Object
        }
    },
    data() {
        return {
            loading: false,
        }
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
