<template>
    <div>
        <div style="height: 800px" ref="map"></div>
        <div id="routing-control" v-if="routing !== null">
            <c-routing-control :routing-control="routing"></c-routing-control>
        </div>
        <div id="elevation-control" v-if="elevation !== null">
            <!--            <c-elevation-control :coordinates="result.coordinates" :selected="selectedIndex" @update:selected="selectedIndex = $event"></c-elevation-control>-->
        </div>
        <div id="place-control" v-if="places !== null">
            <c-place-control :place-control="places"></c-place-control>
        </div>
    </div>
</template>

<script>
import CRoutingControl from 'ui/components/Route/controls/routing/CRoutingControl';
import CElevationControl from 'ui/components/Route/controls/elevation/CElevationControl';
import CPlaceControl from 'ui/components/Route/controls/places/CPlaceControl';
import ElevationControl from './controls/ElevationControl';
import PlaceControl from './controls/PlaceControl';
import RoutingControl from './controls/RoutingControl';

export default {
    name: "MapCanvas",
    components: {CPlaceControl, CElevationControl, CRoutingControl},
    props: {
        state: {
            required: false,
            type: Object|null,
            default: null
        },
        elevation: {
            required: false,
            type: ElevationControl|null,
            default: null
        },
        routing: {
            required: false,
            type: RoutingControl|null,
            default: true
        },
        places: {
            required: false,
            type: PlaceControl|null,
            default: true
        },
    },
    computed: {
        ref() {
            return this.$refs.map;
        },
        listOfPlaces: {
            get() {
                return this.places?.getPlaces() ?? [];
            },
            set(places) {
                if(this.places) {
                    this.places.setPlaces(places);
                }
            }
        }
    },
}
</script>

<style scoped>

</style>
