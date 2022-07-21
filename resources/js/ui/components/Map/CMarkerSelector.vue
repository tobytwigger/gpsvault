<template>

    <l-map style="height: 50vh" ref="map" :zoom="9" @ready="initialiseClickListener" @update:bounds="updateBounds">

        <l-tile-layer
            v-for="tileProvider in tileProviders"
            :key="tileProvider.name"
            :name="tileProvider.name"
            :visible="tileProvider.visible"
            :url="tileProvider.url"
            :attribution="tileProvider.attribution"
            layer-type="base"/>

        <l-marker v-for="marker in markers" :key="'marker-' + marker.id" :draggable="false" :ref="'marker-' + marker.id"
                  @ready="bindMarkerClickListener(marker.id)" :lat-lng="[marker.lng, marker.lat]">
            <l-icon
                :icon-size="[32,32]"
                :icon-anchor="[32,32]"
                :class-name="value === marker.id ? 'selectedMarker' : 'normalMarker'"
                :icon-url="'/dist/images/leaflet/' + marker.icon + '.svg'"
            />
            <l-tooltip>{{ marker.title }}</l-tooltip>
        </l-marker>

    </l-map>

</template>

<script>
import {LMap, LGeoJson, LTileLayer, LControl, LControlLayers, LMarker, LIcon, LTooltip} from "vue2-leaflet";
import { icon } from 'leaflet';

export default {
    name: "CMarkerSelector",
    components: {
        LMap,
        LGeoJson,
        LTileLayer,
        LControlLayers,
        LControl,
        LMarker,
        LTooltip,
        LIcon
    },
    props: {
        value: {
            required: false,
            type: Number,
            default: null
        },
        markers: {
            required: true,
            type: Array,
            validation: (value) => {
                return value.filter(v => {
                    return v && v.hasOwnProperty('lat') && v.hasOwnProperty('lng') && v.hasOwnProperty('id') && v.hasOwnProperty('icon')
                        && v.hasOwnProperty('title')
                })
            }
        }
    },
    methods: {
        getIcon(marker) {
            if(marker.icon) {
                return icon({
                    iconUrl: '/dist/images/leaflet/' + marker.icon + '.svg',
                    iconSize: [32, 37],
                    iconAnchor: [16, 37]
                });
            }
            return '';
        },
        initialiseClickListener() {
            this.$refs.map.mapObject.setView([52.025612, -0.801140]);
            this.$refs.map.mapObject.invalidateSize();
        },
        bindMarkerClickListener(markerId) {
            this.$refs['marker-' + markerId][0].mapObject.on('click', () => this.triggerClickListener(markerId));
        },
        triggerClickListener(markerId) {
            this.$emit('input', markerId);
        },
        updateBounds(bounds) {
            this.$emit('update:bounds', bounds);
        }
    },
    data() {
        return {
            tileProviders: [
                {
                    name: 'OpenStreetMap',
                    visible: true,
                    attribution:
                        '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
                    url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                },
                {
                    name: 'OpenTopoMap',
                    visible: false,
                    url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
                    attribution:
                        'Map data: &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
                }
            ],
            zoom: 9
        }
    },
    computed: {}
}
</script>

<style>

.normalMarker {
    background-color: aqua;
    padding: 10px;
    border: 1px solid #333;
    border-radius: 0 20px 20px 20px;
    box-shadow: 5px 3px 10px rgba(0, 0, 0, 0.2);
    text-align: center;
    width: auto !important;
    height: auto !important;
    margin: 0 !important;
}

.selectedMarker {
    background-color: orange;
    padding: 10px;
    border: 1px solid #333;
    border-radius: 0 20px 20px 20px;
    box-shadow: 5px 3px 10px rgba(0, 0, 0, 0.2);
    text-align: center;
    width: auto !important;
    height: auto !important;
    margin: 0 !important;
}


</style>
