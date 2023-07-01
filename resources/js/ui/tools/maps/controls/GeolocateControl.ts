import {Control, MapState} from "../types/MapType";
import maplibregl, {IControl, Map} from "maplibre-gl";

class GeolocateControl implements Control {
    config: object = {};

    private triggerStateUpdate: () => void = () => undefined;

    constructor(config: object = {}){
        this.config = config;
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

    initialise(map: Map, triggerStateUpdate: () => void) {
    }

    modifyState(state: MapState, oldState: MapState): MapState {
        return state;
    }

}

export default GeolocateControl;
