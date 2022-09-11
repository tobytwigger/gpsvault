<template>

    <l-map style="height: 75vh" ref="map" :zoom="9" @ready="onMapReady">

        <l-control-fullscreen position="topleft"
                              :options="{ title: { 'false': 'Go big!', 'true': 'Be regular' } }" />

        <l-tile-layer
            v-for="tileProvider in tileProviders"
            :key="tileProvider.name"
            :name="tileProvider.name"
            :visible="tileProvider.visible"
            :url="tileProvider.url"
            :attribution="tileProvider.attribution"
            layer-type="base"/>

        <l-marker v-for="marker in _waypoints" :key="'marker-' + marker.id" :draggable="false" :ref="'marker-' + marker.id" :lat-lng="[marker.lng, marker.lat]">
            <l-icon
                :icon-size="[32,32]"
                :icon-anchor="[32,32]"
                :icon-url="'/dist/images/leaflet/' + marker.icon + '.svg'"
            />
            <l-tooltip>{{ marker.title }}</l-tooltip>
        </l-marker>

        <l-geo-json
            v-if="_geojson"
            ref="geojson"
            :geojson="_geojson"></l-geo-json>

    </l-map>

</template>

<script>
import { LMap, LGeoJson, LTileLayer, LControl, LControlLayers, LMarker, LIcon, LTooltip } from "vue2-leaflet";
import Routing from 'leaflet-routing-machine';
import Valhalla from 'lrm-valhalla';

import LControlFullscreen from 'vue2-leaflet-fullscreen';
import {clone} from 'lodash';
import Router from './RoutePlannerPlugins/Router';

export default {
    name: "CRoutePlanner",
    components: {
        LMap,
        LGeoJson,
        LTileLayer,
        LControlFullscreen,
        LControlLayers,
        LControl,
        LMarker,
        LIcon,
        LTooltip
    },
    props: {
        geojson: {
            required: false,
            type: Array,
            default: () => []
        },
        distance: {
            required: false,
            type: Number,
            default: 0
        },
        time: {
            required: false,
            type: Number,
            default: 0
        },
        elevation: {
            required: false,
            type: Number,
            default: 0
        },
        waypoints: {
            required: false,
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            routeControl: null,
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
            zoom: 9,
            isRouting: false
        }
    },
    computed: {
        _geojson: {
            get: function() {
                return this.geojson;
            },
            set: function(val) {
                return this.$emit('update:geojson', val);
            }
        },
        _distance: {
            get: function() {
                return this.distance;
            },
            set: function(val) {
                return this.$emit('update:distance', val);
            }
        },
        _time: {
            get: function() {
                return this.time;
            },
            set: function(val) {
                return this.$emit('update:time', val);
            }
        },
        _elevation: {
            get: function() {
                return this.elevation;
            },
            set: function(val) {
                return this.$emit('update:elevation', val);
            }
        },
        _waypoints: {
            get: function() {
                return this.waypoints;
            },
            set: function(val) {
                return this.$emit('update:waypoints', val);
            }
        },
    },
    methods: {






        setStart(startPoint) {
            let points = clone(this.waypoints);
            points.unshift(startPoint)
            this.routeControl.setWaypoints(points);
        },
        setEnd(endPoint) {
            let points = clone(this.waypoints);
            points.push(endPoint)
            this.routeControl.setWaypoints(points);
        },
        onMapReady() {
            // Set the initial location of the map
            this.$refs.map.mapObject.setView([52.025612, -0.801140]);

            // Add the routing later to the map
            this.addRouting();


            this.routeControl.on('routeselected', (e) => {
                console.log(e);
                console.log(e.route);
                this._geojson = this.addElevation(e.route.coordinates);
                this._distance = e.route.summary.totalDistance * 1000
                this._time = e.route.summary.totalTime
            });
            this.routeControl.on('routingstart', () => this.isRouting = true);
            this.routeControl.on('routesfound routingerror', () => this.isRouting = false);

            this.$refs.map.mapObject.on('click', (e) => {
                var container = L.DomUtil.create('div'),
                    startBtn = this.createButton('Start here', container),
                    destBtn = this.createButton('Finish here', container);

                L.DomEvent.on(startBtn, 'click', () => {
                    this.setStart({lat: e.latlng.lat, lng: e.latlng.lng});
                    this.$refs.map.mapObject.closePopup();
                });

                L.DomEvent.on(destBtn, 'click', () => {
                    this.setEnd({lat: e.latlng.lat, lng: e.latlng.lng});
                    this.$refs.map.mapObject.closePopup();
                });

                L.popup()
                    .setContent(container)
                    .setLatLng(e.latlng)
                    .openOn(this.$refs.map.mapObject);
            });

        },
        addRouting() {
            let self = this;
            let plan = L.Routing.plan(this.waypoints, {
                createMarker: function(i, wp) {
                    let marker = L.marker(wp.latLng, {
                        draggable: true
                    });

                    let container = L.DomUtil.create('div'),
                        removingButton = self.createButton('Remove', container);

                    L.DomEvent.on(removingButton, 'click', (e) => {
                        let points = clone(self._waypoints);
                        points.splice(i, 1);
                        self.routeControl.setWaypoints(points);
                        marker.closePopup();
                    });

                    marker.bindPopup(container);
                    return marker;
                },
            });
            let router = new Valhalla('mapzen-xxxxxx', 'bicycle', {
                costing:'bicycle',
            }, {
                serviceUrl: 'https://valhalla1.openstreetmap.de/',
                timeout: 30 * 1000,
                transitmode: 'bicycle'
            });

            plan.on('waypointsspliced', (e) => this._waypoints = this.routeControl.getWaypoints().map(w => w.latLng).filter(l => l !== null));
            let options = {
                routeWhileDragging: true,
                plan: plan,
                show: false,
                router: router,
                // router: Router,
                // formatter: new L.Routing.mapzenFormatter()
            };
            let control = L.Routing.control(options);

            control.addTo(this.$refs.map.mapObject);
            this.routeControl = control;
        },
        createButton(label, container) {
            var btn = L.DomUtil.create('button', '', container);
            btn.setAttribute('type', 'button');
            btn.innerHTML = label;
            return btn;
        },
        addElevation(coordinates) {
            return coordinates.map(c => {
                c[2] = 0;
                return c;
            });
        }
    }
}
</script>

<style scoped>

</style>