import {Control} from "../types/MapType";
import maplibregl, {IControl} from "maplibre-gl";

class GeolocateControl implements Control {
    config: object = {};

    setConfig(config: object = {}): this {
        this.config = config;

        return this;
    };

    createControl(): IControl {
        return new maplibregl.GeolocateControl({
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
        });
    };

}

export default GeolocateControl;
