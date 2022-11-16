<template>
    <div>
        <div v-if="geojson.length === 0">No route could be plotted</div>
        <c-map-many-geojson :geojson="geojson" v-else></c-map-many-geojson>
    </div>
</template>

<script>
import CMap from '../Map/CMap';
import CMapManyGeojson from '../Map/CMapManyGeojson';
export default {
    name: "CTourMap",
    components: {CMapManyGeojson, CMap},
    props: {
        tour: {
            required: true,
            type: Object
        }
    },
    computed: {
        geojson() {
            return this.tour.stages.map(stage => {
                return (stage?.route?.path?.linestring ?? []).map(ls => [ls.coordinates[1], ls.coordinates[0]])
            })
        }
    },
}
</script>

<style scoped>

</style>
