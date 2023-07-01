import maplibregl, {MapLibreZoomEvent} from "maplibre-gl";
import {Control, MapConfig, MapState} from "./types/MapType";
import {Map as MapLibreMap} from "maplibre-gl";
import {throttle, cloneDeep, debounce} from "lodash";
import {convertMarker, convertMarkerPopup} from "./stateConverter";
import {createState} from "./mapFactory";

class Map {

    map: MapLibreMap;

    hasLoaded: boolean = false;

    state: MapState;

    private _controls: Control[];

    debouncedRedraw: () => void = throttle(this.redrawRaw.bind(this), 500);

    private stateUpdatedCallbacks: ((state: MapState) => void)[] = [];

    private modifyStateCallbacks: ((state: MapState) => MapState)[] = [];

    private existingMarkers: {[key: string]: maplibregl.Marker} = {};

    private existingPopups: {[key: string]: maplibregl.Popup} = {};

    constructor(mapConfig: MapConfig, initialState: MapState|null = null) {
        if(initialState === null) {
            initialState = createState();
        }
        this.hasLoaded = false;
        this.state = initialState;
        this._controls = mapConfig.controls;
        this.map = this.createMap(mapConfig)
        this.initialise();
    }

    /*
     *
     */

    resetMapBounds() {
        let bounds: maplibregl.LngLatBounds|null = null;
        // TODO  Can get the bounds from the result

        // if(this.result?.coordinates?.length > 1) {
        //     let coordinates = this.result?.coordinates;
        //
        //     bounds = coordinates.reduce(function (bounds, coord) {
        //         return bounds.extend([coord[0], coord[1]]);
        //     }, new maplibregl.LngLatBounds(
        //         new maplibregl.LngLat(coordinates[0][1], coordinates[0][0]),
        //         new maplibregl.LngLat(coordinates[1][1], coordinates[1][0])
        //     ));
        // } else if(Array.isArray(this._schema?.waypoints) && this._schema.waypoints.length > 2) {
        //
        //     bounds = this._schema.waypoints.reduce(function (bounds, waypoints) {
        //         return bounds.extend([waypoints.location[1], waypoints.location[0]]);
        //     }, new maplibregl.LngLatBounds([this._schema.waypoints[0].location[1], this._schema.waypoints[0].location[0]], [this._schema.waypoints[0].location[1], this._schema.waypoints[0].location[0]]));
        // } else {
            bounds = new maplibregl.LngLatBounds(new maplibregl.LngLat(-26, 37), new maplibregl.LngLat(10, 60));
        // }
        if(bounds !== null) {
            this.modifyState(mapState => {
                if(bounds !== null) {
                    mapState.bounds = {
                        southWest: {lat: bounds._sw.lat, lng: bounds._sw.lng},
                        northEast: {lat: bounds._ne.lat, lng: bounds._ne.lng},
                    }
                }
                return mapState;
            });
            this.map.fitBounds(bounds, {
                // padding: 50,
                maxDuration: 0,
            });
        }
    }

    private initialise() {
        this.onStateUpdated((state) => {
            this.redrawRawMap(state);
        });

        let self = this;

        // Refresh the map after we've loaded the basic canvas
        this.map.on('load', () => {
            self.hasLoaded = true;
            self.redraw();
            self.resetMapBounds();
        });

        // Refresh the map after we've zoomed in
        this.map.on('zoomend', function(e) {
            self.redraw();
        });

        this.map.on('zoomend', function(e: MapLibreZoomEvent) {
            self.modifyState((state): MapState => {
                state.zoom = e.target.getZoom();
                return state;
            });
        });
        this.map.on('zoomend', function(e) {
            let bounds = e.target.getBounds();
            self.modifyState((state): MapState => {
                state.bounds = {
                    southWest: {lat: bounds._sw.lat, lng: bounds._sw.lng},
                    northEast: {lat: bounds._ne.lat, lng: bounds._ne.lng},
                };
                return state;
            });
        });
        this.map.on('moveend', function(e) {
            let bounds = e.target.getBounds();
            self.modifyState((state): MapState => {
                state.bounds = {
                    southWest: {lat: bounds._sw.lat, lng: bounds._sw.lng},
                    northEast: {lat: bounds._ne.lat, lng: bounds._ne.lng},
                };
                return state;
            });
        });

        // for(let control of this._controls) {
        //     control.setupUpdateFunction(this.setState);
        // }

        for(let control of this._controls) {
            this.map.addControl(control.createControl());
        }

        for(let control of this._controls) {
            control.initialise(this.map, this.redraw.bind(this));
        }
    }

    modifyState(callback: (mapState: MapState) => MapState) {
        this.modifyStateCallbacks.push(callback);
        this.redraw();
    }

    isReady(): boolean {
        return this.hasLoaded;
    }

    onStateUpdated(callback: (state: MapState) => void) {
        this.stateUpdatedCallbacks.push(callback);
    };

    redrawRaw() {
        let state = this.getState();
        for(let callback of this.modifyStateCallbacks) {
            state = callback(state);
        }
        let oldState = this.state;
        for(let control of this._controls) {
            state = control.modifyState(state, oldState);
        }
        this.setState(state);
    }

    redraw() {
        this.debouncedRedraw();
    }

    redrawRawMap(state: MapState) {
        // Get all the markers
        let markers = Object.keys(state.markers)
            .map((key) => state.markers[key].markers)
            .flat();

        // Convert and add them to the map
        let idsToKeep: string[] = [];
        for(let marker of markers) {
            if(this.existingMarkers[marker.uniqueId] === undefined) {
                this.existingMarkers[marker.uniqueId] = convertMarker(marker);
            } else {
                convertMarker(marker, this.existingMarkers[marker.uniqueId]);
            }
            idsToKeep.push(marker.uniqueId);
            this.existingMarkers[marker.uniqueId].addTo(this.map);
        }
        for(let markerUniqueId of Object.keys(this.existingMarkers)) {
            if(idsToKeep.indexOf(markerUniqueId) === -1) {
                this.existingMarkers[markerUniqueId].remove();
                delete this.existingMarkers[markerUniqueId];
            }
        }

        // Add popups to the map
        let popups = Object.keys(state.popups);

        let popupIdsToKeep: string[] = [];
        for(let popupKey of popups) {
            let popup = state.popups[popupKey];
            if(this.existingPopups[popup.uniqueId] === undefined) {
                this.existingPopups[popup.uniqueId] = convertMarkerPopup(popup);
            } else {
                convertMarkerPopup(popup, this.existingPopups[popup.uniqueId]);
            }
            popupIdsToKeep.push(popup.uniqueId);
            this.existingPopups[popup.uniqueId].addTo(this.map);
        }
        for(let popupUniqueId of Object.keys(this.existingPopups)) {
            if(popupIdsToKeep.indexOf(popupUniqueId) === -1) {
                this.existingPopups[popupUniqueId].remove();
                delete this.existingPopups[popupUniqueId];
            }
        }

    }

    setState(mapState: MapState) {
        this.state = mapState;
        for(let callback of this.stateUpdatedCallbacks) {
            callback(mapState);
        }
    }

    getState(): MapState {
        return cloneDeep(this.state);
    }

    private createMap(mapConfig: MapConfig): MapLibreMap {
        return new maplibregl.Map({
            container: mapConfig.container,
            // style: 'https://demotiles.maplibre.org/style.json', // style URL
            // TODO Load all this from the config. Config is stuff that won't be reactive/won't change.
            center: [0, 51], // starting position [lng, lat]
            // zoom: this.state.zoom, // starting zoom
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
    }
}

export default Map;
