<template>
    <div>
        <div style="height: 800px" ref="map"></div>
        <div id="routing-control">
            <c-routing-control :schema="schema"></c-routing-control>
        </div>
    </div>
</template>

<script>
import maplibregl from 'maplibre-gl';
import RoutingControl from './RoutingControl';
import CRoutingControl from './CRoutingControl';

export default {
    name: "CRoutePlanner",
    components: {CRoutingControl},
    props: {
        result: {
            required: false,
            type: Object,
            default: null
        },
        schema: {
            required: false,
//            required: true,
            type: Object,
            default: () => {
                return {

                }
            }
        }
    },
    data() {
        return {
            map: null
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



        this.map.on('load', () => {
            this.setupMap();
        });
    },
    computed: {
        _schema: {
            get: function() {
                return this.schema;
            },
            set: function(val) {
                return this.$emit('update:schema', val);
            }
        },
        hasRoute() {
            return this.result !== null;
        }
    },
    methods: {
        setupMap() {
            this.map.addControl(
                new maplibregl.NavigationControl({
                    showZoom: true,
                    showCompass: true
                })
            );

            this.map.addControl(
                new maplibregl.ScaleControl({
                    maxWidth: 350,
                    unit: this.$setting.unit_system || 'metric'
                })
            );

            this.map.addControl(
                new RoutingControl()
            );


            this.map.addControl(
                new maplibregl.GeolocateControl({
                    positionOptions: {
                        enableHighAccuracy: false,
                        maximumAge: 0,
                        timeout: 6000 /* 6 sec */
                    },
                    fitBoundsOptions: {
                        maxZoom: 15
                    },
                    trackUserLocation: false,
                    showAccuracyCircle: true,
                    showUserLocation: true
                })
            );


            this.map.addSource('route', {
                'type': 'geojson',
                'data': {
                    'type': 'Feature',
                    'properties': {},
                    'geometry': this.normalGeojson
                }
            });
            this.map.addLayer({
                'id': 'route',
                'type': 'line',
                'source': 'route',
                'layout': {
                    'line-join': 'round',
                    'line-cap': 'round'
                },
                'paint': {
                    'line-color': '#888',
                    'line-width': 8
                }
            });
            this.map.addControl(new maplibregl.FullscreenControl({}));
            this.resetMapBounds();
        },
        resetMapBounds() {
            let bounds = null;
            if(this.result?.coordinates?.length > 1) {
                let coordinates = this.result?.coordinates;

                bounds = coordinates.reduce(function (bounds, coord) {
                    return bounds.extend([coord[0], coord[1]]);
                }, new maplibregl.LngLatBounds(coordinates[0], coordinates[0]));
            } else {
                bounds = new maplibregl.LngLatBounds(new maplibregl.LngLat(-26, 37), new maplibregl.LngLat(10, 60));
            }
            this.map.fitBounds(bounds, {
                padding: 20
            });
        },

    },

}
</script>

<style scoped>
</style>
