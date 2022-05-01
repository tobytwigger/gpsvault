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

    </l-map>

</template>

<script>
import { LMap, LGeoJson, LTileLayer, LControl, LControlLayers, LMarker } from "vue2-leaflet";
import L from 'leaflet';
import Routing from 'leaflet-routing-machine';
import LControlFullscreen from 'vue2-leaflet-fullscreen';

export default {
    name: "CRoutePlanner",
    components: {
        LMap,
        LGeoJson,
        LTileLayer,
        LControlFullscreen,
        LControlLayers,
        LControl,
        LMarker
    },
    props: {
        routePoints: {
            required: false,
            type: Array,
            default: () => []
        },
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
    methods: {
        updateWaypoints(points) {
            this.$emit('update:routePoints', points);
            this.routeControl.setWaypoints(points);
        },
        setStart(startPoint) {
            let points = this.routePoints;
            points.unshift(startPoint)
            this.updateWaypoints(points);
        },
        setEnd(endPoint) {
            let points = this.routePoints;
            points.push(endPoint)
            this.updateWaypoints(points);
        },
        onMapReady() {
            this.$refs.map.mapObject.setView([52.025612, -0.801140]);
            this.addRouting();
            this.routeControl.on('routeselected', (e) => {
                console.log(e);
                this.$emit('update:geojson', {coordinates: e.route.coordinates});
            })
            this.routeControl.on('routingstart', () => this.isRouting = true);
            this.routeControl.on('routesfound routingerror', () => this.isRouting = false);
            // this.$refs.map.mapObject.on('click', (e) => {
            //     if (this.routePoints.length === 0) {
            //         this.setStart({lat: e.latlng.lat, lng: e.latlng.lng});
            //     } else {
            //         this.setEnd({lat: e.latlng.lat, lng: e.latlng.lng});
            //     }
            // })

            function createButton(label, container) {
                var btn = L.DomUtil.create('button', '', container);
                btn.setAttribute('type', 'button');
                btn.innerHTML = label;
                return btn;
            }

            this.$refs.map.mapObject.on('click', (e) => {
                var container = L.DomUtil.create('div'),
                    startBtn = createButton('Start here', container),
                    destBtn = createButton('Finish here', container);

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
            let plan = L.Routing.plan(this.routePoints);
            // plan.on('waypointsspliced', (e) => {
            //     if(e.nRemoved !== this.routePoints.length) {
            //         let points = this.routePoints;
            //         points.splice(e.index, e.nRemoved, e.added);
            //         // this.updateWaypoints(points);
            //     }
            // });
            let options = {
                routeWhileDragging: true,
                plan: plan
            };
            let control = L.Routing.control(options);

            control.addTo(this.$refs.map.mapObject);
            this.routeControl = control;
        }
    }
}
</script>

<style scoped>

</style>
