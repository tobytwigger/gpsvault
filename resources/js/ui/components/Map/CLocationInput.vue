<template>
    <div>
        <div>Location</div>
        <div>
            <div style="height: 300px" ref="map"></div>
        </div>
    </div>

</template>

<script type="text/ecmascript-6">
import maplibregl from 'maplibre-gl';

export default {
    name: "CLocationInput",
    components: {
    },
    props: {
        value: {
            required: true,
            type: Object,
        },
        disabled: {
            required: false,
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            map: null,
            marker: null
        }
    },
    watch: {
        value: {
            deep: true,
            handler: function(val) {
                this.updateMarker(val);
            }
        }
    },
    mounted() {
        this.map = new maplibregl.Map({
            container: this.$refs.map,
            // style: 'https://demotiles.maplibre.org/style.json', // style URL
            center: [0, 51], // starting position [lng, lat]
            zoom: 1, // starting zoom
            style: {
                version: 8,
                sources: {
                    osm: {
                        type: 'raster',
                        tiles: ['https://a.tile.openstreetmap.org/{z}/{x}/{y}.png'],
                        tileSize: 256,
                        attribution: '&copy; OpenStreetMap Contributors',
                        maxzoom: 19
                    },
                    opentopo: {
                        type: 'raster',
                        tiles: ['https://a.tile.opentopomap.org/{z}/{x}/{y}.png'],
                        tileSize: 256,
                        attribution: 'Map data: &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>',
                        maxzoom: 19
                    },
                    simple: {
                        type: 'raster',
                        tiles: ['https://a.tile.opentopomap.org/{z}/{x}/{y}.png'],
                        tileSize: 256,
                        attribution: 'Map data: &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>',
                        maxzoom: 19
                    },
                },
                layers: [
                    {
                        id: 'osm',
                        type: 'raster',
                        source: 'osm'
                    },
                ],
            },
        });

        this.map.on('click', (e) => {
            if(!this.disabled) {
                this.setLatLng(e.lngLat.lat, e.lngLat.lng);
            }
        });

        this.map.on('load', () => {
            this.map.addControl(
                new maplibregl.NavigationControl({
                    showZoom: true,
                    showCompass: false
                })
            );

            if(this.value !== null) {
                this.updateMarker(this.value);
            }
        });
    },

    methods: {
        setLatLng(lat, lng) {
            this.$emit('input', {lat: lat, lng: lng})
        },
        updateMarker(value) {
            if(this.marker) {
                this.marker.setLngLat(value);
            } else {
                this.marker = new maplibregl.Marker()
                    .setLngLat(value)
                    .addTo(this.map);
            }
        }
    },

    computed: {
        hasLocation() {
            return this.value.lat !== null && this.value.lng !== null;
        },
        markerLatLng() {
            if(this.hasLocation) {
                return [this.value.lat, this.value.lng];
            }
            return [];
        }
    }
}
</script>

<style lang="scss">
.vue2leaflet-map {
    z-index: 1;
}
</style>
