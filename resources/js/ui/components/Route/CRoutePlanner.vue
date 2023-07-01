<template>
    <div>
        <map-canvas
            :elevation="controls.elevation"
            :routing="controls.routing"
            :places="controls.places"
            :state="state"
            ref="map"></map-canvas>
    </div>
</template>

<script>
import maplibregl from 'maplibre-gl';

import CryptoJS from 'crypto-js';
import {cloneDeep} from 'lodash';
import polyline from '@mapbox/polyline';
import units from '../../mixins/units';

import MapCanvas from 'ui/tools/maps/MapCanvas';
import Map from 'ui/tools/maps/map';

import ElevationControl from 'ui/tools/maps/controls/ElevationControl';
import FullscreenControl from 'ui/tools/maps/controls/FullscreenControl';
import RoutingControl from 'ui/tools/maps/controls/RoutingControl';
import PlaceControl from 'ui/tools/maps/controls/PlaceControl';
import GeolocateControl from 'ui/tools/maps/controls/GeolocateControl';
import NavigationControl from 'ui/tools/maps/controls/NavigationControl';
import ScaleControl from 'ui/tools/maps/controls/ScaleControl';
import {MapState} from '../../tools/maps/types/MapType';


export default {
    name: "CRoutePlanner",
    components: {MapCanvas},
    mixins: [units],
    props: {
//         errors: {
//             required: false,
//             type: Object,
//             default: () => {
//                 return {};
//             }
//         },
//         result: {
//             required: false,
//             type: Object,
//             default: () => {
//                 return {
//                     coordinates: [] // 3d coordinates
//                 }
//             }
//         },
//         schema: {
//             required: false,
// //            required: true,
//             type: Object,
//             default: () => {
//                 return {
//                     waypoints: [
//                     ]
//                 }
//             }
//         }
    },
    data() {
        return {
            controls: {
                elevation: new ElevationControl(),
                fullscreen: new FullscreenControl(),
                geolocate: new GeolocateControl(),
                navigation: new NavigationControl(),
                places: new PlaceControl({
                    search: {
                        url: route('place.search')
                    }
                }),
                routing: new RoutingControl(),
                scale: new ScaleControl({
                    unit: this.$setting.unit_system || 'metric'
                })
            },
            map: null,
            state: null

            // bounds: null,
            // places: [],
            // placeMarkers: {},
            // // map: null,
            // zoom: 1,
            // geojsonMarker: null,
            // hoverLngLat: {lng: null, lat: null},
            // markers: {},
            // generalPopup: null,
            // ready: false,
            // selectedIndex: null
        }
    },
    mounted() {
        this.map = new Map({
            container: this.$refs.map.ref,
            controls: Object.values(this.controls)
        }, null);
        let self = this;
        this.map.onStateUpdated((state) => self.state = state);
    },
    watch: {
        // schema: {
        //     deep: true,
        //     handler: function(val) {
        //         this.syncMap();
        //     }
        // },
        // places: {
        //     deep: true,
        //     handler: function(val) {
        //         this.syncMap();
        //     }
        // },
        // result: {
        //     deep: true,
        //     handler: function(val) {
        //         if(this.ready) {
        //             this.updateMapWithResult();
        //         } else {
        //             this.map.on('load', () => {
        //                 this.updateMapWithResult();
        //             });
        //         }
        //     }
        // },
        // selectedIndex: {
        //     deep: true,
        //     handler: function(selectedIndex) {
        //         if(selectedIndex !== null) {
        //             if(this.geojsonMarker) {
        //                 this.geojsonMarker.remove();
        //             }
        //
        //             this.geojsonMarker = new maplibregl.Marker()
        //                 .setLngLat({
        //                     lng: this.result.coordinates[selectedIndex][1],
        //                     lat: this.result.coordinates[selectedIndex][0]
        //                 });
        //
        //             this.geojsonMarker.addTo(this.map);
        //         } else if(this.geojsonMarker) {
        //             this.geojsonMarker.remove();
        //         }
        //     }
        // }
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
                    'id': 'route-layer',
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

                this.map.on('mouseenter', 'route-layer', (event) => this.hoverLngLat = event.lngLat);
                this.map.on('mousemove', 'route-layer', (event) => this.hoverLngLat = event.lngLat);
                this.map.on('mouseleave', 'route-layer', (event) => {
                    setTimeout(() => {
                        this.hoverLngLat = null
                    }, 1000);
                });

            } else {
                this.map.getSource('route').setData(geojson);
            }

        },
        syncMap() {
            this.syncMarkers();
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
                        // If it does exist, see if we should update it.
                        let backgroundImage = this._getBackgroundImage(parseInt(waypointIndex) + 1);
                        let currentBackgroundImage = this.markers[waypoint.id].getElement().style.backgroundImage;
                        if(currentBackgroundImage === '') {
                            currentBackgroundImage = null;
                        }
                        if(
                            (waypoint.location !== this.markers[waypoint.id].getLngLat())
                            || (currentBackgroundImage !== backgroundImage)
                        ) {
                            this.markers[waypoint.id].setLngLat([waypoint.location[1], waypoint.location[0]]);
                            this.markers[waypoint.id].getElement().style.backgroundImage = backgroundImage;
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
                    waypoint.unsaved = true;
                    this._schema.waypoints[waypointIndex] = waypoint;
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
            let backgroundImage = this._getBackgroundImage(parseInt(index) + 1);
            if(backgroundImage !== null) {
                markerEl.style.backgroundImage = backgroundImage;
            }
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
            // let addAsPlaceBtn = this._createPopupButton('Add as a place', 'add-as-place-' + waypoint.id, (e) => console.log('Add as a place'));
            let buttonDiv = document.createElement('div');
            buttonDiv.appendChild(removeMarkerBtn);//.appendChild(addAsPlaceBtn);

            let popup = new maplibregl.Popup({ offset: 25 }).setDOMContent(buttonDiv);

            let marker = new maplibregl.Marker({element: markerEl, draggable: true})
                .setLngLat([waypoint.location[1], waypoint.location[0]])
                .setPopup(popup); // sets a popup on this marker

            marker.on('dragend', (e) => {
                let schema = cloneDeep(this._schema);
                let waypoints = schema.waypoints.filter(w => w.id.toString() === waypoint.id.toString());
                if(waypoints.length > 0) {
                    let waypoint = waypoints[0];
                    let index = schema.waypoints.indexOf(waypoint);
                    let coords = marker.getLngLat();
                    waypoint.location = [coords.lat, coords.lng];
                    schema.waypoints.splice(index, 1, waypoint);
                    this._schema = schema;
                }
            });

            return marker;
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
        _newWaypoint(location) {
            return {
                id: CryptoJS.lib.WordArray.random(32).toString(),
                unsaved: true,
                place_id: null,
                location: location
            }
        },
        _getBackgroundImage(text) {
            let shouldShowWaypoints = this.map.getZoom() > 10;
            if(!shouldShowWaypoints) {
                return null;
            }
            return 'url("data:image/svg+xml,%3C%3Fxml version=\'1.0\' encoding=\'utf-8\'%3F%3E%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 500 500\'  xmlns:bx=\'https://boxy-svg.com\'%3E%3Cellipse style=\'stroke: rgb(0, 0, 0);\' cx=\'253.821\' cy=\'257.697\' rx=\'183.5\' ry=\'183.5\'/%3E%3Ctext style=\'fill: rgb(255, 255, 255); font-family: Arial, sans-serif; font-size: 15.5px; white-space: pre;\' x=\'267.442\' y=\'224.806\' transform=\'matrix(0, 0, 0, 0, 0, 0)\'%3E2%3C/text%3E%3Ctext style=\'fill: rgb(255, 255, 255); font-family: Arial, sans-serif; font-size: ' + (text > 9 ? '2' : '3') + '00px; font-weight: 700; paint-order: fill; stroke-miterlimit: 7; stroke-width: 9px; white-space: pre;\' x=\'171.095\' y=\'363.411\' bx:origin=\'0.49881 0.5\'%3E' + text +'%3C/text%3E%3C/svg%3E")';
        },

    },

}
</script>

<style scoped>
</style>
