import {Control} from "../types/MapType";
import BaseRoutingControl from './../../../components/Route/controls/routing/RoutingControl.js';
import {IControl} from "maplibre-gl";

class RoutingControl implements Control {
    config: object = {};

    public setConfig(config: object = {}): this {
        this.config = config;
        return this;
    };

    createControl(): IControl {
        return new BaseRoutingControl({});
    };
}

export default RoutingControl;

// Use all in CRoutePlanner
// Build up controls to have an add funcition
// Use add function in map.ts

// See what else you can move from CRoutePlanner to map.ts
