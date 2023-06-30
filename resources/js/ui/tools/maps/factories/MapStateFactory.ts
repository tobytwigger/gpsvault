import {MapState} from "../types/MapType";

function mapStateFactory(): MapStateFactory {
    return new MapStateFactory();
}

class MapStateFactory {
    create(): MapState {
        return {

        }
    }
}

export default MapStateFactory;

export {mapStateFactory};
