import {Control} from "../types/MapType";
import maplibregl, {IControl, Unit} from "maplibre-gl";

interface ScaleControlOptions {
    unit?: Unit;
}

class ScaleControl implements Control {
    config: ScaleControlOptions = {
        unit: 'metric',
    };

    setConfig(config: ScaleControlOptions = {}): this {
        if(this.config.unit === undefined) {
            this.config.unit = 'metric';
        }
        this.config = config;
        return this;
    };

    createControl(): IControl {
        return new maplibregl.ScaleControl({
            maxWidth: 350,
            unit: this.config.unit
        })
    };

}

export default ScaleControl;
