import {IControl} from "maplibre-gl";

interface MapConfig {
    container: HTMLElement,
    controls: Control[]
}

interface MapState {

}

interface Control {
    config: object;
    setConfig(config: object): this;
    createControl(): IControl;
}

export {MapConfig, MapState, Control};
