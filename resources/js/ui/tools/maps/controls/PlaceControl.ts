import {Control} from "../types/MapType";
import BasePlaceControl from './../../../components/Route/controls/places/PlaceControl.js';
import {IControl} from "maplibre-gl";

class PlaceControl implements Control  {
    config: object = {};

    setConfig(config: object = {}): this {
        this.config = config;

        return this;
    };

    createControl(): IControl {
        return new BasePlaceControl();
    }
}

export default PlaceControl;
