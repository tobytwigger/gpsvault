import {Control, MapState} from "../types/MapType";
import BaseElevationControl from './../../../components/Route/controls/elevation/ElevationControl.js';
import {IControl, Map} from "maplibre-gl";

class ElevationControl implements Control {
    config: object = {};

    private triggerStateUpdate: () => void = () => undefined;

    constructor(config: object = {}){
        this.config = config;
    };

    createControl(): IControl {
        return new BaseElevationControl({});
    }

    initialise(map: Map, triggerStateUpdate: () => void) {

    }

    modifyState(state: MapState, oldState: MapState): MapState {
        return state;
    }
}

export default ElevationControl;
