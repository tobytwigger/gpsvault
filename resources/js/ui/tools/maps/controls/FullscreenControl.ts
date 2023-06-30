import {Control} from "../types/MapType";
import maplibregl, {IControl} from "maplibre-gl";

class FullscreenControl implements Control {
    config: object = {};

    setConfig(config: object = {}): this {
        this.config = config;

        return this;
    };

    createControl(): IControl {
        return new maplibregl.FullscreenControl({});
    }
}

export default FullscreenControl;
