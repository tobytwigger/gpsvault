<template>
    <div>
        <div style="height: 500px" ref="map"></div>
        <canvas ref="canvas" height="250"></canvas>
    </div>
</template>

<script type="text/ecmascript-6">
import maplibregl from 'maplibre-gl';
import { Chart, registerables} from 'chart.js';
import 'chartjs-adapter-moment';

export default {
    name: "CMap",
    components: {
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
            chart: null,
            // How many points to compress into one point.
            graphScaleFactor: 10
        }
    },
    created() {
        Chart.register(...registerables);
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

        this.setupChart();


        this.map.on('load', () => {

            this.map.addControl(
                new maplibregl.NavigationControl({
                    showZoom: true,
                    showCompass: false
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
        });
    },

    methods: {
        resetMapBounds() {
            let coordinates = this.normalGeojson.coordinates;

            let bounds = coordinates.reduce(function (bounds, coord) {
                return bounds.extend(coord);
            }, new maplibregl.LngLatBounds(coordinates[0], coordinates[0]));

            this.map.fitBounds(bounds, {
                padding: 20
            });
        },
        setupChart() {
            let context = this.$refs.canvas.getContext('2d');

            this.chart = new Chart(context, {
                type: 'line',
                options: {
                    tooltips: {
                        intersect: false,
                    },
                    legend: {
                        display: false,
                    },
                    scales: {
                        x: {display: true},
                        y: {display: true}
                    }
                },
                data: {
                    labels: this.elevationProfileDataset.map((e, i) => i.toString()),
                    datasets: [
                        {
                            fill: false,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1,
                            data: this.elevationProfileDataset
                        }
                    ]
                }
            });
        }
    },

    computed: {
        elevationGeojson() {
            if(this.geojson === null) {
                return null;
            }
            return {
                "name":"NewFeatureType",
                "type":"FeatureCollection",
                "features":[
                    {
                        "type":"Feature",
                        "geometry":this.geojson,
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

            this.geojson.coordinates.forEach((coords, index) => {
                if (coords.length === 3) {
                    data.push(coords[2]);
                }
            });

            return data.filter((d, i) => i % this.graphScaleFactor === 0);
        }
    }
}
</script>

<style lang="scss">
 .vue2leaflet-map {
     z-index: 1;
 }
</style>
