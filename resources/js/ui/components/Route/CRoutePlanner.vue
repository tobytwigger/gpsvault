<template>
    <div>
        <div style="height: 800px" ref="map"></div>
        <div id="routing-control">
            <c-routing-control :schema.sync="_schema" :result="result"></c-routing-control>
        </div>
        <div id="elevation-control">
            <c-elevation-control :result="result"></c-elevation-control>
        </div>
    </div>
</template>

<script>
import maplibregl from 'maplibre-gl';
import RoutingControl from './controls/routing/RoutingControl';
import CRoutingControl from './controls/routing/CRoutingControl';

import ElevationControl from './controls/elevation/ElevationControl';
import CElevationControl from './controls/elevation/CElevationControl';

import CryptoJS from 'crypto-js';
import {cloneDeep} from 'lodash';

export default {
    name: "CRoutePlanner",
    components: {CElevationControl, CRoutingControl},
    props: {
        result: {
            required: false,
            type: Object,
            default: () => {
                return {
                    coordinates: [] // 3d coordinates
                }
            }
        },
        schema: {
            required: false,
//            required: true,
            type: Object,
            default: () => {
                return {
                    waypoints: [
                        {
                            id: 1,
                            order: 2,
                            place_id: null,
                            location: [0, 51],
                            route_path_id: 4
                        },
                        {
                            id: 2,
                            order: 1,
                            place_id: null,
                            location: [0, 51.2],
                            route_path_id: 4
                        },
                    ]
                }
            }
        }
    },
    data() {
        return {
            map: null,
            markers: {},
            generalPopup: null
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
    watch: {
        schema: {
            deep: true,
            handler: function(val) {
                this.syncMap();
            }
        },
        result: {
            deep: true,
            handler: function(val) {
                this.updateMapWithResult();
            }
        }
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
        updateMapWithResult() {
            let geojson = {
                type: 'LineString',
                coordinates: this.result.coordinates.map(c => [c[1], c[0]])
            };

            if(this.map.getSource('route') === undefined) {
                this.map.addSource('route', {
                    'type': 'geojson',
                    'data': {
                        'type': 'Feature',
                        'properties': {},
                        'geometry': geojson
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
            } else {
                this.map.getSource('route').setData(geojson);
            }
        },
        setupMap: function () {
            this.map.addControl(
                new maplibregl.NavigationControl({
                    showZoom: true,
                    showCompass: true
                })
            );

            // this.map.addControl(
            //     new maplibregl.ScaleControl({
            //         maxWidth: 350,
            //         unit: this.$setting.unit_system || 'metric'
            //     })
            // );

            this.map.addControl(
                new RoutingControl()
            );

            this.map.addControl(
                new ElevationControl()
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

            this.map.addControl(new maplibregl.FullscreenControl({}));

            this._createGeneralClickHandler();

            this.syncMap();

            this.resetMapBounds();
        },
        syncMap() {
            this.syncMarkers();
        },
        resetMapBounds() {
            let bounds = null;
            if(this.result?.coordinates?.length > 1) {
                let coordinates = this.result?.coordinates;

                bounds = coordinates.reduce(function (bounds, coord) {
                    return bounds.extend([coord[0], coord[1]]);
                }, new maplibregl.LngLatBounds(coordinates[1], coordinates[0]));
            } else if(Array.isArray(this._schema?.waypoints) && this._schema.waypoints.length > 0) {

                bounds = this._schema.waypoints.reduce(function (bounds, waypoints) {
                    return bounds.extend([waypoints.location[0], waypoints.location[1]]);
                }, new maplibregl.LngLatBounds(this._schema.waypoints[0].location[0], this._schema.waypoints[0].location[1]));
            } else {
                bounds = new maplibregl.LngLatBounds(new maplibregl.LngLat(-26, 37), new maplibregl.LngLat(10, 60));
            }
            this.map.fitBounds(bounds, {
                padding: 50,
                maxDuration: 1,
                // speed: 2.2
            });
        },

        syncMarkers() {
            let idArray = [];
            // Iterate through the waypoints in the schema
            for(let waypointIndex in this._schema?.waypoints ?? []) {
                let waypoint = this._schema.waypoints[waypointIndex];
                // Check if the ID exists in the markers
                if(waypoint?.id) {
                    // Check if the marker exists
                    if(this.markers.hasOwnProperty(waypoint.id)) {
                        // If it does exist, make sure the location matches.
                        if(waypoint.location !== this.markers[waypoint.id].getLngLat()) {
                            this.markers[waypoint.id].setLngLat([waypoint.location[0], waypoint.location[1]]);
                            this.markers[waypoint.id].getElement().style.backgroundImage = this._getBackgroundImage(parseInt(waypointIndex) + 1)
                        }
                    } else {
                        // Otherwise create it
                        this.markers[waypoint.id] = this._createMarker(waypoint, waypointIndex);
                    }
                    // Add the ID to an array
                    this.markers[waypoint.id].addTo(this.map);
                    idArray.push(waypoint.id.toString());
                } else {
                    // If it doesn't exist, assign it a random uuid ID, and create a marker.
                    let markerId = CryptoJS.lib.WordArray.random(32).toString();
                    this.markers[markerId] = this._createMarker(waypoint, waypointIndex);
                    this.markers[markerId].addTo(this.map);
                    // Add the ID to an array
                    idArray.push(markerId);
                }
            }
            for(let obsoleteMarkerId of Object.keys(this.markers).filter((id) => !idArray.includes(id))) {
                // Remove from map
                this.markers[obsoleteMarkerId].remove();
                // Remove from array
                delete this.markers[obsoleteMarkerId];
            }
        },
        _createMarker(waypoint, index) {

            // Create the marker
            let markerEl = document.createElement('div');
            markerEl.id = 'waypoint-' + waypoint.id;
            markerEl.className = 'marker clickable';
            markerEl.style.cursor = 'pointer';
            markerEl.style.backgroundImage = this._getBackgroundImage(parseInt(index) + 1);
            markerEl.style.width = '20px';
            markerEl.style.height = '48px';

            // Create the onclick popup
            let removeMarkerBtn = this._createPopupButton('Remove marker', 'remove-marker-' + waypoint.id, (e) => {
                let waypointId = (e.target.id ?? '').replace('remove-marker-', '');
                if(this.markers.hasOwnProperty(waypointId)) {
                    let schema = cloneDeep(this._schema);
                    schema.waypoints = schema.waypoints.filter(w => w.id.toString() !== waypointId.toString());
                    this._schema = schema;
                }
            });
            let addAsPlaceBtn = this._createPopupButton('Add as a place', 'add-as-place-' + waypoint.id, (e) => console.log('Add as a place'));
            let buttonDiv = document.createElement('div');
            buttonDiv.appendChild(removeMarkerBtn).appendChild(addAsPlaceBtn);

            let popup = new maplibregl.Popup({ offset: 25 }).setDOMContent(buttonDiv);

            return new maplibregl.Marker(markerEl)
                .setLngLat([waypoint.location[0], waypoint.location[1]])
                .setPopup(popup); // sets a popup on this marker
        },
        _createPopupButton(text, id, fn) {
            let btn = document.createElement('a');
            btn.innerHTML = text;
            btn.id = id
            btn.addEventListener('click', fn);

            let container = document.createElement('div');
            container.style.padding = '3px';
            container.appendChild(btn)

            return container;
        },
        _createGeneralClickHandler() {
            this.map.on('click', (e) => {
                if(e.originalEvent.target.classList.contains('clickable') === false) {
                    if(this.generalPopup === null) {
                        let addToStartButton = this._createPopupButton('Add to start', 'add-to-start', (e) => {
                            let waypoint = {
                                id: CryptoJS.lib.WordArray.random(32).toString(),
                                place_id: null,
                                location: [this.generalPopup.getLngLat().lng, this.generalPopup.getLngLat().lat]
                            }
                            let schema = cloneDeep(this._schema);
                            schema.waypoints.unshift(waypoint);
                            this._schema = schema;
                            if(this.generalPopup) {
                                this.generalPopup.remove();
                                this.generalPopup = null;
                            }
                        });
                        let addToEndBtn = this._createPopupButton('Add to end', 'add-to-end', (e) => {
                            let waypoint = {
                                id: CryptoJS.lib.WordArray.random(32).toString(),
                                place_id: null,
                                location: [this.generalPopup.getLngLat().lng, this.generalPopup.getLngLat().lat]
                            }
                            let schema = cloneDeep(this._schema);
                            schema.waypoints.push(waypoint);
                            this._schema = schema;
                            if(this.generalPopup) {
                                this.generalPopup.remove();
                                this.generalPopup = null;
                            }
                        });
                        let addAsWaypointBtn = this._createPopupButton('Add as waypoint', 'add-as-waypoint', (e) => {
                            let waypoint = {
                                id: CryptoJS.lib.WordArray.random(32).toString(),
                                place_id: null,
                                location: [this.generalPopup.getLngLat().lng, this.generalPopup.getLngLat().lat]
                            }
                            let schema = cloneDeep(this._schema);
                            schema.waypoints.splice(schema.waypoints.length - 1, 0, waypoint);
                            this._schema = schema;
                            if(this.generalPopup) {
                                this.generalPopup.remove();
                                this.generalPopup = null;
                            }
                        });

                        let buttonDiv = document.createElement('div');
                        buttonDiv.appendChild(addToStartButton).appendChild(addToEndBtn).appendChild(addAsWaypointBtn);

                        this.generalPopup = new maplibregl.Popup()
                            .setLngLat(e.lngLat)
                            .setDOMContent(buttonDiv)
                            .addTo(this.map);
                    } else {
                        this.generalPopup.remove();
                        this.generalPopup = null;
                    }
                }
            })
        },
        _getBackgroundImage(text) {
            return 'url("data:image/svg+xml,%3C%3Fxml version=\'1.0\' encoding=\'utf-8\'%3F%3E%3Csvg viewBox=\'0 0 500 500\' xmlns=\'http://www.w3.org/2000/svg\' xmlns:bx=\'https://boxy-svg.com\'%3E%3Cellipse style=\'stroke: rgb(0, 0, 0);\' cx=\'253.821\' cy=\'257.697\' rx=\'183.5\' ry=\'183.5\'/%3E%3Ctext style=\'fill: rgb(255, 255, 255); font-family: Arial, sans-serif; font-size: 15.5px; white-space: pre;\' x=\'267.442\' y=\'224.806\' transform=\'matrix(0, 0, 0, 0, 0, 0)\'%3E2%3C/text%3E%3Ctext style=\'fill: rgb(255, 255, 255); font-family: Arial, sans-serif; font-size: 300px; font-weight: 700; paint-order: fill; stroke-miterlimit: 7; stroke-width: 9px; white-space: pre;\' x=\'171.095\' y=\'363.411\' bx:origin=\'0.49881 0.5\'%3E' + text +'%3C/text%3E%3C/svg%3E")';
        }
    },

}
</script>

<style scoped>
</style>
