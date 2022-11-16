<template>
    <div>
        <div style="height: 800px" ref="map"></div>
        <div id="elevation-control">
            <c-elevation-control :coordinates="geojson.coordinates" :selected="selectedIndex" @update:selected="selectedIndex = $event"></c-elevation-control>
        </div>
    </div>
</template>

<script type="text/ecmascript-6">
import maplibregl from 'maplibre-gl';
import ElevationControl from './../Route/controls/elevation/ElevationControl';
import CElevationControl from './../Route/controls/elevation/CElevationControl';
import {cloneDeep} from 'lodash';

export default {
    name: "CMap",
    components: {
        CElevationControl
    },
    props: {
        geojson: {
            type: Object,
            required: true
        },
        markers: {
            type: Array,
            required: false,
            default: () => []
        }
    },
    data() {
        return {
            map: null,
            selectedIndex: null,
            geojsonMarker: null,
            // How many points to compress into one point.
            graphScaleFactor: 10
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

            this.map.addControl(
                new maplibregl.NavigationControl({
                    showZoom: true,
                    showCompass: false
                })
            );

            this.map.addControl(
                new ElevationControl()
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
        });
    },

    methods: {
        resetMapBounds() {
            let coordinates = this.normalGeojson.coordinates;

            let bounds = coordinates.reduce(function (bounds, coord) {
                return bounds.extend(coord);
            }, new maplibregl.LngLatBounds(coordinates[0], coordinates[0]));

            this.map.fitBounds(bounds, {
                padding: 20, maxDuration: 0.1
            });
        },
    },

    watch: {
        selectedIndex: {
            deep: true,
            handler: function(selectedIndex) {
                if(selectedIndex !== null) {
                    if(this.geojsonMarker) {
                        this.geojsonMarker.remove();
                    }

                    this.geojsonMarker = new maplibregl.Marker()
                        .setLngLat({
                            lng: this.geojson.coordinates[selectedIndex][0],
                            lat: this.geojson.coordinates[selectedIndex][1]
                        });

                    this.geojsonMarker.addTo(this.map);
                } else if(this.geojsonMarker) {
                    this.geojsonMarker.remove();
                }
            }
        }
    },

    computed: {
        elevationGeojson() {
            if(this.geojson === null) {
                return null;
            }

            let geojson = cloneDeep(this.geojson);
            geojson.coordinates = this.geojson.coordinates.map(c => [c[0], c[1], c[2]]);

            return {
                "name":"NewFeatureType",
                "type":"FeatureCollection",
                "features":[
                    {
                        "type":"Feature",
                        "geometry":geojson,
                        "properties":null
                    }
                ]};
        },
        normalGeojson() {
            return {
                type: this.geojson.type,
                coordinates: this.geojson.coordinates.map(c => [c[0], c[1]])
            }
        },
        elevationProfileDataset() {
            const data = [];

            this.elevationGeojson.coordinates.forEach((coords, index) => {
                if (coords.length === 3) {
                    data.push(coords[2]);
                }
            });

            return data.filter((d, i) => i % this.graphScaleFactor === 0);
        },
    }
}
</script>

<style lang="scss">
 .vue2leaflet-map {
     z-index: 1;
 }
</style>
