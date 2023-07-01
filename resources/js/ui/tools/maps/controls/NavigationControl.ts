import {Control, MapState} from "../types/MapType";
import maplibregl, {IControl, Map} from "maplibre-gl";

class NavigationControl implements Control {
    config: object = {};

    private triggerStateUpdate: () => void = () => undefined;

    constructor(config: object = {}){
        this.config = config;
    };

    createControl(): IControl {
        return new maplibregl.NavigationControl({
            showZoom: true,
            showCompass: true
        })
    };

    initialise(map: Map, triggerStateUpdate: () => void) {
        this.triggerStateUpdate = triggerStateUpdate;
    }

    modifyState(state: MapState, oldState: MapState): MapState {
        return state;
    }
}



export default NavigationControl;
