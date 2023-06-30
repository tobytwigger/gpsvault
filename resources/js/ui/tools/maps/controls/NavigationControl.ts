import {Control} from "../types/MapType";
import maplibregl, {IControl} from "maplibre-gl";

class NavigationControl implements Control {
    config: object = {};

    setConfig(config: object = {}): this {
        this.config = config;

        return this;
    };

    createControl(): IControl {
        return new maplibregl.NavigationControl({
            showZoom: true,
            showCompass: true
        })
    };
}



export default NavigationControl;
