import {Control, MapState} from "../types/MapType";
import maplibregl, {IControl, Map} from "maplibre-gl";

class FullscreenControl implements Control {
    config: object = {};

    private triggerStateUpdate: () => void = () => undefined;

    constructor(config: object = {}){
        this.config = config;
    };

    createControl(): IControl {
        return new maplibregl.FullscreenControl({});
    }

    initialise(map: Map, triggerStateUpdate: () => void) {
        this.triggerStateUpdate = triggerStateUpdate;
    }

    modifyState(state: MapState, oldState: MapState): MapState {
        return state;
    }
}

export default FullscreenControl;
