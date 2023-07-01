import {Control, MapState} from "../types/MapType";
import maplibregl, {IControl, Unit, Map} from "maplibre-gl";

interface ScaleControlOptions {
    unit?: Unit;
}

class ScaleControl implements Control {
    config: ScaleControlOptions = {
        unit: 'metric',
    };

    private triggerStateUpdate: () => void = () => undefined;

    constructor(config: ScaleControlOptions = {}){
        if(this.config.unit === undefined) {
            this.config.unit = 'metric';
        }
        this.config = config;
    };

    createControl(): IControl {
        return new maplibregl.ScaleControl({
            maxWidth: 350,
            unit: this.config.unit
        })
    };

    initialise(map: Map, triggerStateUpdate: () => void) {
    }

    modifyState(state: MapState, oldState: MapState): MapState {
        return state;
    }

}

export default ScaleControl;
