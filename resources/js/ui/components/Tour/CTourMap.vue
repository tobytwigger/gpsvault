<template>
    <div>
        <div v-if="geojson.length === 0">No route could be plotted</div>
        <c-map-many-geojson :key="uniqueKey" :geojson="geojson" v-else></c-map-many-geojson>
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
    data() {
        return {
            uniqueKey: null
        }
    },
    watch: {
        tour: {
            deep: true,
            handler: function() {
                this.uniqueKey = (Math.random() + 1).toString(36).substring(7);
            }
        }
    },
    mounted() {
        this.uniqueKey = (Math.random() + 1).toString(36).substring(7);
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
