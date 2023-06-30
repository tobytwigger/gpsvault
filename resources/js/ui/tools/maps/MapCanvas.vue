<template>
    <div>
        <div style="height: 800px" ref="map"></div>
<!--        <div id="routing-control">-->
<!--            <c-routing-control :errors="errors" :schema.sync="_schema" :result="result"></c-routing-control>-->
<!--        </div>-->
<!--        <div id="elevation-control">-->
<!--            <c-elevation-control :coordinates="result.coordinates" :selected="selectedIndex" @update:selected="selectedIndex = $event"></c-elevation-control>-->
<!--        </div>-->
<!--        <div id="place-control">-->
<!--            <c-place-control :bounds="bounds" :places.sync="places" :zoom="zoom"></c-place-control>-->
<!--        </div>-->
    </div>
</template>

<script>
import CRoutingControl from 'ui/components/Route/controls/routing/CRoutingControl';
import CElevationControl from 'ui/components/Route/controls/elevation/CElevationControl';
import CPlaceControl from 'ui/components/Route/controls/places/CPlaceControl';
import maplibregl from 'maplibre-gl';
import Map from 'ui/tools/maps/map';

export default {
    name: "MapCanvas",
    components: {CPlaceControl, CElevationControl, CRoutingControl},
    computed: {
        ref() {
            return this.$ref.map;
        }
    },
}
</script>

<style scoped>

</style>
