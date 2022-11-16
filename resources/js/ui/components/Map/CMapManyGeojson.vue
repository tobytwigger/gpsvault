<template>
    <div>
        <div style="height: 500px" ref="map"></div>
    </div>
</template>

<script type="text/ecmascript-6">
import maplibregl from 'maplibre-gl';

export default {
    name: "CMapManyGeojson",
    components: {
    },
    props: {
        geojson: {
            type: Array,
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

            this.mapRoutes();

            this.map.addControl(new maplibregl.FullscreenControl({}));
            this.resetMapBounds();
        });
    },

    methods: {
        mapRoutes() {
            for(let lineIndex in this.geojson) {
                this.map.addSource('route-' + lineIndex, {
                    'type': 'geojson',
                    'data': {
                        'type': 'Feature',
                        'properties': {},
                        'geometry': {
                            type: 'LineString',
                            coordinates: this.geojson[lineIndex].map(coords => [coords[1], coords[0]])
                        }
                    }
                });
                this.map.addLayer({
                    'id': 'route-' + lineIndex,
                    'type': 'line',
                    'source': 'route-' + lineIndex,
                    'layout': {
                        'line-join': 'round',
                        'line-cap': 'round'
                    },
                    'paint': {
                        'line-color': '#888',
                        'line-width': 8
                    }
                });
            }

        },
        resetMapBounds() {
            if(this.coordinates.length > 1) {
                let bounds = this.coordinates.reduce(function (bounds, coord) {
                    return bounds.extend([coord[1], coord[0]]);
                }, new maplibregl.LngLatBounds([this.coordinates[0][1], this.coordinates[0][0]], [this.coordinates[1][1], this.coordinates[1][0]]));

                this.map.fitBounds(bounds, {
                    padding: 20
                });
            }
        },
    },
    computed: {
        coordinates() {
            let coords = [];
            for(let lineIndex in this.geojson) {
                coords.push(...this.geojson[lineIndex]);
            }
            return coords;
        }
    }
}
</script>

<style lang="scss">
 .vue2leaflet-map {
     z-index: 1;
 }
</style>
