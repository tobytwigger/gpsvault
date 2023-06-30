import maplibregl from "maplibre-gl";
import {Control, MapConfig, MapState} from "./types/MapType";

class Map {

    map: any = null;

    hasLoaded: boolean = false;

    initialState: MapState;

    private controls: Control[];

    constructor(mapConfig: MapConfig, initialState: MapState) {
        this.hasLoaded = false;
        this.initialState = initialState;
        this.map = this.createMap(mapConfig)



        this.controls = mapConfig.controls;

        this.initialise();

    }

    /*
     * 
     */

    private initialise() {
        this.map.on('load', () => {
            this.hasLoaded = true;
            this.setState(this.initialState);
        });

        for(let control of mapConfig.controls) {
            this.map.addControl(control.createControl());
        }

        for(let control of mapConfig.controls) {
            this.map.addControl(control.createControl());
        }
    }

    isReady(): boolean {
        return this.hasLoaded;
    }


    setState(mapState: MapState): void {
        // TODO Set the state of the map.
    }

    private createMap(mapConfig: MapConfig): any {
        return new maplibregl.Map({
            container: mapConfig.container,
            // style: 'https://demotiles.maplibre.org/style.json', // style URL
            // TODO Load all this from the config. Config is stuff that won't be reactive/won't change.
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
    }
}

export default Map;
