import {Control} from "../types/MapType";
import BaseElevationControl from './../../../components/Route/controls/elevation/ElevationControl.js';
import {IControl} from "maplibre-gl";

class ElevationControl implements Control {
    config: object = {};

    setConfig(config: object = {}): this {
        this.config = config;
        return this;
    };

    createControl(): IControl {
        return new BaseElevationControl({});
    }
}

export default ElevationControl;
