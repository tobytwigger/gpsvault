import {IControl, Map} from "maplibre-gl";

interface MapConfig {
    container: HTMLElement,
    controls: Control[]
}

interface LatLng {
    lat: number;
    lng: number;
}

interface MapState {
    bounds: {
        southWest: LatLng;
        northEast: LatLng;
    },
    zoom: number;
    markers: {
        [key: string]: MapMarkerCollection
    },
    popups: {
        [key: string]: MapMarkerPopup|null
    }
}

class MapMarkerCollection {
    markers: MapMarker[];

    constructor(markers: MapMarker[]) {
        this.markers = markers;
    }


    hasMarkerWithReference(reference: string): boolean {
        return this.markers.filter(m => m.reference === reference).length > 0;
    }

    getMarkerWithReference(reference: string): MapMarker|null {
        let marker: MapMarker|undefined = this.markers.find(m => m.reference === reference);
        if(marker === undefined) {
            return null;
        }
        return marker;
    }

    push(marker: MapMarker) {
        let existingMarker = this.markers.find(m => m.uniqueId === marker.uniqueId);
        if(existingMarker !== undefined) {
            this.markers.splice(this.markers.indexOf(existingMarker), 1);
        } else {
            this.markers.push(marker);
        }
    }

    sync(updatedMarkers: MapMarker[]) {
        this.markers = updatedMarkers;
    }
}

interface MapMarker {
    latitude: number;
    longitude: number;
    reference: string;
    uniqueId: string;
    backgroundImage?: null|string;
    popup?: MapMarkerPopup|null;
    draggable?: boolean;
}

interface MapMarkerPopup {
    coordinates?: {
        latitude: number;
        longitude: number;
    }
    buttons: MapMarkerPopupButton[];
    title: string|null;
    uniqueId: string;
}

interface MapMarkerPopupButton {
    label: string;
    action: (e) => void;
    uniqueId: string;
}

function mergeMarkers(originalMarker: MapMarker, newMarker: MapMarker): MapMarker {
    return {
        ...originalMarker,
        ...newMarker,
        uniqueId: originalMarker.uniqueId
    };
}

interface PaginationResponse<T> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number|null;
    last_page: number;
    last_page_url: string;
    links: {
        url: string|null;
        label: string;
        active: boolean;
    }[];
    next_page_url: string|null;
    path: string;
    per_page: number;
    prev_page_url: string|null;
    to: number|null;
    total: number;
}

interface Control {
    config: object;

    createControl(): IControl;

    initialise(map: Map, triggerStateUpdate: () => void);

    modifyState(state: MapState, oldState: MapState): MapState;
}

export {MapConfig, MapState, Control, MapMarker, MapMarkerCollection, mergeMarkers, MapMarkerPopup, MapMarkerPopupButton};
